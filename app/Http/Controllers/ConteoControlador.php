<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Models\Conteo;
use App\Models\ConteoAnaquel;
use App\Models\ConteoUbicacion;
use App\Models\Inventario;

class ConteoControlador extends Controller
{
    public function index()
    {
        $conteos = Conteo::with(['conteoAnaqueles' => function($query) {
            $query->whereIn('segundo_conteo', [1, 2]);
        }])->get();

        return view('inv', compact('conteos'));
    }

    public function showSegundoConteo($id)
    {
        $conteo = Conteo::with(['conteoAnaqueles' => function($query) {
            $query->whereIn('segundo_conteo', [1, 2]);
        }])->findOrFail($id);

        return view('segundo_conteo', compact('conteo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_hora' => 'required|date_format:Y-m-d\TH:i',
        ]);

        Conteo::create($request->all());

        return redirect()->route('inv')->with('success', 'Conteo creado exitosamente.');
    }

    public function destroy($id)
    {
        try {
            $conteo = Conteo::findOrFail($id);

            ConteoAnaquel::where('conteo_id', $conteo->id)->delete();

            $conteo->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function SeleccionarUbicacion($id)
    {
        $conteo = Conteo::findOrFail($id);
        $ubicaciones = ConteoUbicacion::all();
        return view('ubicacion', compact('conteo', 'ubicaciones'));
    }

    public function showAnaqueles($id, $ubicacion_id)
    {
        $conteo = Conteo::findOrFail($id);
        $ubicacion = ConteoUbicacion::findOrFail($ubicacion_id);
        $anaqueles = Inventario::obtenerAnaqueles($ubicacion_id);

        $productos = [];
        foreach ($anaqueles as $anaquel) {
            $productos[$anaquel] = ConteoAnaquel::where('anaquel', $anaquel)
                                                ->where('conteo_id', $conteo->id)
                                                ->where('ubicacion_id', $ubicacion_id)
                                                ->get();
        }

        return view('anaqueles', compact('conteo', 'ubicacion', 'anaqueles', 'productos', 'ubicacion_id'));
    }

    public function storeAnaquel(Request $request, $conteo_id)
    {
        try {
            $validatedData = $request->validate([
                'barcode' => 'required|string',
                'cantidad' => 'required|integer',
                'anaquel' => 'required|string',
                'ubicacion_id' => 'required|integer',
                'operation' => 'required|string|in:insert,update,sum,check'
            ]);

            // Reemplazar apóstrofo por guion en el código de barras
            $validatedData['barcode'] = str_replace("'", "-", $validatedData['barcode']);
            \Log::info('Intentando buscar producto con barcode/sku: ' . $validatedData['barcode']);

            // Eliminar espacios adicionales del código de barras y el anaquel
            $validatedData['barcode'] = trim($validatedData['barcode']);
            $validatedData['anaquel'] = trim($validatedData['anaquel']);

            // Buscar el producto en el inventario
            $producto = Inventario::obtenerProductoPorBarcodeOSku($validatedData['barcode']);
            if (!$producto) {
                \Log::error('Producto no encontrado con barcode/sku: ' . $validatedData['barcode']);
                return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            }

            // Verificar si el producto ya está registrado en el conteo
            $conteoAnaquel = ConteoAnaquel::where('conteo_id', $conteo_id)
                ->where('anaquel', $validatedData['anaquel'])
                ->where('ubicacion_id', $validatedData['ubicacion_id'])
                ->where(function ($query) use ($producto, $validatedData) {
                    if ($producto->CODIGO_BARRAS && $producto->CODIGO_BARRAS !== 'No encontrado') {
                        $query->where('barcode', $producto->CODIGO_BARRAS);
                    } else {
                        $query->where('sku', $producto->ARTICULO);
                    }
                })
                ->first();

            $exists = (bool) $conteoAnaquel;

            if ($validatedData['operation'] === 'check') {
                return response()->json(['success' => true, 'exists' => $exists, 'conteoAnaquel' => $conteoAnaquel]);
            }

            if ($conteoAnaquel) {
                if ($validatedData['operation'] === 'update') {
                    $conteoAnaquel->cantidad = $validatedData['cantidad'];
                    $message = 'Cantidad actualizada.';
                } elseif ($validatedData['operation'] === 'sum') {
                    $conteoAnaquel->cantidad += $validatedData['cantidad'];
                    $message = 'Cantidad sumada.';
                }
                $conteoAnaquel->save();
            } else {
                ConteoAnaquel::create([
                    'conteo_id' => $conteo_id,
                    'barcode' => $producto->CODIGO_BARRAS,
                    'sku' => $producto->ARTICULO,
                    'cantidad' => $validatedData['cantidad'],
                    'cantidad2' => 0, // valor por defecto para este campo
                    'anaquel' => $validatedData['anaquel'],
                    'ubicacion_id' => $validatedData['ubicacion_id'],
                ]);
                $message = 'Producto registrado.';
            }

            return response()->json(['success' => true, 'message' => $message, 'exists' => false]);
        } catch (\Exception $e) {
            // Registrar el error en el log de Laravel
            \Log::error('Error al guardar producto en el conteo', [
                'barcode' => $request->input('barcode'),
                'cantidad' => $request->input('cantidad'),
                'anaquel' => $request->input('anaquel'),
                'ubicacion_id' => $request->input('ubicacion_id'),
                'conteo_id' => $conteo_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function obtenerProducto(Request $request)
    {
        $barcodeOrSku = $request->input('barcode');
        $producto = Inventario::obtenerProductoPorBarcodeOSku($barcodeOrSku);
        return response()->json($producto);
    }

    public function storeCantidad(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer',
        ]);
    
        $conteoAnaquel = ConteoAnaquel::findOrFail($id);
        $conteoAnaquel->cantidad2 = $request->input('cantidad');
        $conteoAnaquel->segundo_conteo = 2; // Cambiar el campo segundo_conteo a 2
        $conteoAnaquel->save();
    
        return response()->json(['success' => true]);
    }

    public function storeBarcode(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:255',
            'new-barcode' => 'required|string|max:255',
        ]);

        try {
            DB::connection('sqlsrv')->table('REG00005_COD')->insert([
                'ARTICULO' => $request->input('sku'),
                'CODIGO_BARRAS' => $request->input('new-barcode'),
                'DESCRIP' => 'CODIGO BARRAS'
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->back()->with('success', [
                'message' => 'Producto añadido exitosamente.',
                'sku' => $request->input('sku'),
                'barcode' => $request->input('new-barcode'),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error al añadir el producto.');
        }
    }

    public function validarSku(Request $request)
    {
        $sku = $request->input('sku');
        $producto = Inventario::where('ARTICULO', $sku)->first();

        if ($producto) {
            return response()->json(['exists' => true]);
        } else {
            return response()->json(['exists' => false]);
        }
    }
}
