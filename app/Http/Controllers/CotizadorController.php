<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Productos;

class CotizadorController extends Controller
{
    public function index()
    {
        return view('cotizador');
    }

    
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $clasif = $request->input('clasif');
        $marca = $request->input('marca');
        $categoria  = $request->input('cat');
    
        $productos = Productos::obtenerProductos($searchTerm, $clasif, $marca, $categoria);
    
        // Agrupar productos por código
        $groupedProducts = [];
        foreach ($productos as $producto) {
            $codigo = $producto->CODIGO;
            if (!isset($groupedProducts[$codigo])) {
                $groupedProducts[$codigo] = [
                    'CODIGO' => $producto->CODIGO,
                    'DESCRIPCION' => $producto->DESCRIPCION,
                    'EX_T' => $producto->EX_T,
                    'MARCA' => $producto->MARCA,
                    'ESTATUS' => $producto->ESTATUS,
                    'CLASIF' => $producto->CLASIF,
                    'RUTA_IMAGEN' => $producto->RUTA_IMAGEN,
                    'ALTO' => $producto->ALTO,
                    'ANCHO' => $producto->ANCHO,
                    'LARGO' => $producto->LARGO,
                    'PESO' => $producto->PESO,
                    'DES' => $producto->DES,
                    'PORCE' => $producto->PORCE,
                    'PRECIO_1_NETO' => $producto->PRECIO_1_NETO,
                    'ubicaciones' => []
                ];
            }
            // Agregar ubicación
            $groupedProducts[$codigo]['ubicaciones'][] = [
                'TONO' => trim($producto->TONO),
                'CALIBRE' => trim($producto->CALIBRE),
                'CAJA' => trim($producto->CAJA),
                'EX' => $producto->EX
            ];
        }
    
        // Convertir a array indexado
        $result = array_values($groupedProducts);
    
        return response()->json($result);
    }

    public function getFiltros()
    {
        $clasifs = Productos::getDistinctClasif();
        $marcas = Productos::getDistinctMarca();
        $cats = Productos::getDistinctCategoria(); 

        // Estructura de respuesta
        return response()->json([
            'clasifs' => $clasifs,
            'marcas' => $marcas,
            'cats' => $cats,
        ]);
    }
    
}