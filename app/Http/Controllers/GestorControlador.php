<?php
namespace App\Http\Controllers;

use App\Models\Conteo;
use App\Models\ConteoUbicacion;
use App\Models\ConteoAnaquel;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class GestorControlador extends Controller
{
    public function index()
    {
        $conteos = Conteo::all();
        return view('gestor', compact('conteos'));
    }

    public function getUbicaciones($conteo_id, Request $request)
    {
        try {
            $soloContados = $request->query('solo_contados', 'false') === 'true';
            if ($soloContados) {
                $ubicaciones = ConteoUbicacion::whereIn('id', function ($query) use ($conteo_id) {
                    $query->select('ubicacion_id')
                          ->from('conteo_anaqueles')
                          ->where('conteo_id', $conteo_id)
                          ->groupBy('ubicacion_id');
                })->get();
            } else {
                $ubicaciones = ConteoUbicacion::all();
            }
            return response()->json($ubicaciones);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAnaqueles($ubicacion_id, Request $request)
    {
        try {
            $soloContados = $request->query('solo_contados', 'false') === 'true';
            $conteoId = $request->query('conteo_id'); // Obtener el conteo_id desde la solicitud

            if ($soloContados && $conteoId) {
                // Filtrar anaqueles que tienen productos contados en el conteo actual
                $anaqueles = ConteoAnaquel::where('ubicacion_id', $ubicacion_id)
                    ->where('conteo_id', $conteoId) // Filtrar por conteo_id
                    ->groupBy('anaquel')
                    ->pluck('anaquel');
            } else {
                // Obtener todos los anaqueles si no se aplica el filtro "solo contados"
                $anaqueles = Inventario::obtenerAnaqueles($ubicacion_id);
            }

            return response()->json($anaqueles);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProductos(Request $request)
    {
        set_time_limit(400);
        try {
            $conteo_id = $request->input('conteo_id');
            $ubicacion_id = $request->input('ubicacion_id');
            $anaquel = $request->input('anaquel');
            $tono = $this->getTonoByUbicacion($ubicacion_id);
    
            \Log::info('------------------------------');
            \Log::info('Conteo ID: ' . $conteo_id);
            \Log::info('Ubicación ID: ' . $ubicacion_id);
            \Log::info('Anaquel: ' . $anaquel);
            \Log::info('Tono: ' . $tono);
    
            if (!$tono) {
                return response()->json([]);
            }
    
            // Caso cuando se seleccionan "todos los anaqueles"
            if (is_null($anaquel) || $anaquel === '') {
                if ($ubicacion_id == 2) { // Caso particular para ALMACEN (ID 2)
                    $query = "
                        SELECT 
                            LTRIM(RTRIM(r.ARTICULO)) as ARTICULO, 
                            r.EXISTENCIA, 
                            ISNULL(rc.CODIGO_BARRAS, 'No encontrado') AS CODIGO_BARRAS, 
                            rg.DESCRIP, 
                            r.TONO AS NIVEL,
                            r.CALIBRE AS NIVEL2,
                            (CASE WHEN (DOLAR='T') THEN 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) * 
                                (SELECT TOP 1 DOLAR FROM REG01000) 
                                ELSE 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) 
                            END) AS COSTO_CAPAS 
                        FROM REG00521 r
                        LEFT JOIN REG00005_COD rc ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rc.ARTICULO)) AND rc.DESCRIP = 'CODIGO BARRAS'
                        LEFT JOIN REG00005 rg ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rg.ARTICULO))
                        WHERE r.TONO BETWEEN 'ANAQUEL037' AND 'ANAQUEL112'
                        ORDER BY r.TONO ASC
                    ";
                    $bindings = [];
                } else {
                    // Caso general cuando se seleccionan "todos los anaqueles" en cualquier otra ubicación
                    $query = "
                        SELECT 
                            LTRIM(RTRIM(r.ARTICULO)) as ARTICULO, 
                            r.EXISTENCIA, 
                            ISNULL(rc.CODIGO_BARRAS, 'No encontrado') AS CODIGO_BARRAS, 
                            rg.DESCRIP, 
                            r.CALIBRE AS NIVEL,
                            r.CAJA AS NIVEL2,
                            (CASE WHEN (DOLAR='T') THEN 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) * 
                                (SELECT TOP 1 DOLAR FROM REG01000) 
                                ELSE 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) 
                            END) AS COSTO_CAPAS 
                        FROM REG00521 r
                        LEFT JOIN REG00005_COD rc ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rc.ARTICULO)) AND rc.DESCRIP = 'CODIGO BARRAS'
                        LEFT JOIN REG00005 rg ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rg.ARTICULO))
                        WHERE r.TONO = :tono
                        ORDER BY r.TONO ASC
                    ";
                    $bindings = ['tono' => $tono];
                }
            } else {
                // Lógica cuando se selecciona un anaquel específico
                if ($ubicacion_id == 2) { // Almacen
                    $query = "
                        SELECT 
                            LTRIM(RTRIM(r.ARTICULO)) as ARTICULO, 
                            r.EXISTENCIA, 
                            ISNULL(rc.CODIGO_BARRAS, 'No encontrado') AS CODIGO_BARRAS, 
                            rg.DESCRIP, 
                            r.CALIBRE AS NIVEL,
                            (CASE WHEN (DOLAR='T') THEN 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) * 
                                (SELECT TOP 1 DOLAR FROM REG01000) 
                                ELSE 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) 
                            END) AS COSTO_CAPAS 
                        FROM REG00521 r
                        LEFT JOIN REG00005_COD rc ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rc.ARTICULO)) AND rc.DESCRIP = 'CODIGO BARRAS'
                        LEFT JOIN REG00005 rg ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rg.ARTICULO))
                        WHERE r.TONO = :anaquel
                        ORDER BY r.TONO ASC
                    ";
                    $bindings = ['anaquel' => $anaquel];
                } elseif (strpos($anaquel, 'EXHIBICION') !== false) {
                    list($calibre, $caja) = explode(' ', $anaquel);
                    $query = "
                        SELECT 
                            LTRIM(RTRIM(r.ARTICULO)) as ARTICULO, 
                            r.EXISTENCIA, 
                            ISNULL(rc.CODIGO_BARRAS, 'No encontrado') AS CODIGO_BARRAS, 
                            rg.DESCRIP, 
                            r.CAJA AS NIVEL,
                            (CASE WHEN (DOLAR='T') THEN 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) * 
                                (SELECT TOP 1 DOLAR FROM REG01000) 
                                ELSE 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) 
                            END) AS COSTO_CAPAS 
                        FROM REG00521 r
                        LEFT JOIN REG00005_COD rc ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rc.ARTICULO)) AND rc.DESCRIP = 'CODIGO BARRAS'
                        LEFT JOIN REG00005 rg ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rg.ARTICULO))
                        WHERE r.TONO = :tono AND r.CALIBRE = :calibre AND r.CAJA = :caja
                        ORDER BY r.TONO ASC
                    ";
                    $bindings = ['tono' => $tono, 'calibre' => $calibre, 'caja' => trim($caja)];
                } else {
                    $query = "
                        SELECT 
                            LTRIM(RTRIM(r.ARTICULO)) as ARTICULO, 
                            r.EXISTENCIA, 
                            ISNULL(rc.CODIGO_BARRAS, 'No encontrado') AS CODIGO_BARRAS, 
                            rg.DESCRIP, 
                            r.CAJA AS NIVEL,
                            (CASE WHEN (DOLAR='T') THEN 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) * 
                                (SELECT TOP 1 DOLAR FROM REG01000) 
                                ELSE 
                                (DBO.CostoPromD(r.ARTICULO, floor(cast(getdate() as float)) + 36163)) 
                            END) AS COSTO_CAPAS 
                        FROM REG00521 r
                        LEFT JOIN REG00005_COD rc ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rc.ARTICULO)) AND rc.DESCRIP = 'CODIGO BARRAS'
                        LEFT JOIN REG00005 rg ON LTRIM(RTRIM(r.ARTICULO)) = LTRIM(RTRIM(rg.ARTICULO))
                        WHERE r.TONO = :tono AND r.CALIBRE = :anaquel
                        ORDER BY r.TONO ASC
                    ";
                    $bindings = ['tono' => $tono, 'anaquel' => $anaquel];
                }
            }
    
            $fullQuery = $this->remplazarBindings($query, $bindings);
            \Log::info('Consulta SQL completa: ' . $fullQuery);
    
            // Ejecutar consulta en SQL Server
            $productos = DB::connection('sqlsrv')->select($query, $bindings);
            usort($productos, function($a, $b) {
                return strcmp(trim($a->ARTICULO), trim($b->ARTICULO));
            });
    
            // Obtener productos de MySQL para el conteo
            $productosConteo = ConteoAnaquel::where('conteo_id', $conteo_id)
                ->where('ubicacion_id', $ubicacion_id)
                ->when($anaquel !== 'all' && !is_null($anaquel), function ($query) use ($anaquel) {
                    $query->where('anaquel', $anaquel);
                }, function ($query) {
                    $query->whereNotNull('anaquel');
                })
                ->get()
                ->keyBy(function ($item) {
                    return trim($item->sku);
                });
    
            // Combinar resultados
            $productosCombinados = [];
            foreach ($productos as $producto) {
                $articulo = trim($producto->ARTICULO);
                $existenciaConteo = $productosConteo[$articulo]->cantidad ?? 0;
                $cantidad2 = $productosConteo[$articulo]->cantidad2 ?? null;
                $segundoConteo = $productosConteo[$articulo]->segundo_conteo ?? null;
    
                $productosCombinados[] = [
                    'ARTICULO' => $articulo,
                    'DESCRIPCION' => $producto->DESCRIP,
                    'EXISTENCIA' => intval($producto->EXISTENCIA),
                    'CODIGO_BARRAS' => $producto->CODIGO_BARRAS ?? 'No encontrado',
                    'EXISTENCIA_CONTEO' => intval($existenciaConteo),
                    'CANTIDAD2' => intval($cantidad2),
                    'SEGUNDO_CONTEO' => $segundoConteo,
                    'NIVEL' => $producto->NIVEL,
                    'NIVEL2' => $producto->NIVEL2 ?? '',  // Añadir NIVEL2 aquí
                    'COSTO_CAPAS' => $producto->COSTO_CAPAS,
                    'es_nuevo' => false
                ];
            }
    
            // Agregar productos nuevos del conteo que no están en SQL Server
            foreach ($productosConteo as $productoConteo) {
                if (!in_array(trim($productoConteo->sku), array_column($productosCombinados, 'ARTICULO'))) {
                    $descripcion = DB::connection('sqlsrv')->table('REG00005')
                        ->where('ARTICULO', trim($productoConteo->sku))
                        ->value('DESCRIP');
                    
                    $nivel = ($ubicacion_id == 2) ? $productoConteo->calibre : $productoConteo->caja;
    
                    $productosCombinados[] = [
                        'ARTICULO' => trim($productoConteo->sku),
                        'DESCRIPCION' => $descripcion,
                        'EXISTENCIA' => 0,
                        'CODIGO_BARRAS' => $productoConteo->barcode ?? 'No encontrado',
                        'EXISTENCIA_CONTEO' => intval($productoConteo->cantidad),
                        'CANTIDAD2' => intval($productoConteo->cantidad2),
                        'SEGUNDO_CONTEO' => $productoConteo->segundo_conteo,
                        'NIVEL' => $nivel,
                        'NIVEL2' => '',  // Añadir NIVEL2 vacío si no existe
                        'COSTO_CAPAS' => null, 
                        'es_nuevo' => true
                    ];
                }
            }
    
            // Ordenar productos combinados por ARTICULO
            usort($productosCombinados, function($a, $b) {
                return strcmp(trim($a['ARTICULO']), trim($b['ARTICULO']));
            });
    
            return response()->json($productosCombinados);
    
        } catch (\Exception $e) {
            \Log::error('Error fetching productos: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function getTonoByUbicacion($ubicacion_id)
    {
        $tonos = [
            1 => 'MATRIZ',
            2 => 'ALMACEN',
            3 => 'BODEGA',
            4 => 'MILWAUKEE',
            5 => 'MAKITA',
            6 => 'SUCURSALM',
            7 => 'SYNERGY',
            8 => 'TIENDAREMATE',
            9 => 'CALIFORNIA',
            10 => 'DIINA'
        ];
    
        return $tonos[$ubicacion_id] ?? null;
    }
    
    private function remplazarBindings($query, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $query = preg_replace('/:' . $key . '/', "'" . $value . "'", $query, 1);
        }
        return $query;
    }
    
    public function exportar(Request $request)
    {
        $productos = $request->input('productos');
        $ubicacionSeleccionadaNombre = $request->input('ubicacion_seleccionada_nombre');
        $anaquelSeleccionado = $request->input('anaquel_seleccionado');
    
        if (empty($productos)) {
            return response()->json(['error' => 'No se recibieron productos para exportar'], 400);
        }
    
        // Generar archivo Excel con los datos tal como se muestran
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->fillSheetWithData($sheet, $productos);
        $writer = new Xlsx($spreadsheet);
    
        // Guardar archivo en memoria y descargar como ZIP
        $filename = 'productos_exportados.xlsx';
        $zipFilename = 'productos_exportados.zip';
    
        $zip = new \ZipArchive();
        if ($zip->open($zipFilename, \ZipArchive::CREATE) === true) {
            ob_start();
            $writer->save('php://output');
            $zip->addFromString($filename, ob_get_clean());
            $zip->close();
    
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
            readfile($zipFilename);
            unlink($zipFilename);
        } else {
            return response()->json(['error' => 'Error al crear el archivo ZIP'], 500);
        }
    }
    
    private function fillSheetWithData($sheet, $productos)
    {
        // Encabezados
        $headers = ['ARTICULO','DESCRIPCION','CODIGO_BARRAS','NIVEL','NIVEL2',
                    'EXISTENCIA','EXISTENCIA_CONTEO','RECTIFICACION','DIFERENCIA','PRECIO'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $header);
        }

        // Datos
        foreach ($productos as $index => $producto) {
            $row = $index + 2;
            $sheet->setCellValue("A{$row}", $producto['ARTICULO'] ?? '');
            $sheet->setCellValue("B{$row}", $producto['DESCRIPCION'] ?? '');
            $sheet->setCellValue("C{$row}", $producto['CODIGO_BARRAS'] ?? '');
            $sheet->setCellValue("D{$row}", $producto['NIVEL'] ?? '');
            $sheet->setCellValue("E{$row}", $producto['NIVEL2'] ?? '');
            $sheet->setCellValue("F{$row}", $producto['EXISTENCIA'] ?? '');
            $sheet->setCellValue("G{$row}", $producto['EXISTENCIA_CONTEO'] ?? '');
            $sheet->setCellValue("H{$row}", $producto['RECTIFICACION'] ?? '');
            $sheet->setCellValue("I{$row}", $producto['DIFERENCIA'] ?? '');

            $raw = isset($producto['PRECIO']) ? (string)$producto['PRECIO'] : '0';
            $clean = preg_replace('/[^\d\.\-]/','', $raw);
            $precio = floatval($clean);

            $sheet->setCellValueExplicit("J{$row}", $precio, DataType::TYPE_NUMERIC);

        }

        // Dar formato de moneda a J2:J{ultima fila}
        $lastRow = count($productos) + 1;
        $sheet->getStyle("J2:J{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('"$"#,##0.00');
    }

    public function marcarSegundoConteo(Request $request)
    {
        $conteoId = $request->input('conteo_id');
        $ubicacionId = $request->input('ubicacion_id');
        $anaquel = $request->input('anaquel');
        $conteoId = $request->input('conteo_id'); // Asegúrate de recibir el conteo_id

        $productos = $request->input('productos', []);
        
        $productosExistentes = [];
        $productosNoExistentes = [];

        foreach ($productos as $producto) {
            $conteoAnaquel = ConteoAnaquel::where('sku', $producto['id'])
            ->where('ubicacion_id', $ubicacion_id)
            ->where('anaquel', $anaquel)
            ->where('conteo_id', $conteoId)  // Asegúrate de filtrar por conteo_id
            ->first();

            if ($conteoAnaquel) {
                $productosExistentes[] = $producto;
            } else {
                $productosNoExistentes[] = $producto;
                // Crear registro si no existe
                ConteoAnaquel::create([
                    'sku' => $producto['id'],
                    'ubicacion_id' => $ubicacionId,
                    'anaquel' => $anaquel,
                    'conteo_id' => $conteoId,
                    'cantidad' => 0, // Suponiendo que no hay cantidad contada inicialmente
                    'barcode' => $producto['CODIGO_BARRAS'] ?? 'No encontrado',
                    'segundo_conteo' => 1, // Marcado para segundo conteo
                    'cantidad2' => 0 // Asegurando que cantidad2 tiene un valor por defecto
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'productos_existentes' => $productosExistentes,
            'productos_no_existentes' => $productosNoExistentes
        ]);
    }

    public function actualizarSegundoConteo(Request $request)
    {
        try {
            $productos = $request->input('productos', []);
            $ubicacion_id = $request->input('ubicacion_id');
            $anaquel = $request->input('anaquel');
            $conteoId = $request->input('conteo_id'); // Asegúrate de recibir el conteo_id

            foreach ($productos as $producto) {
                $conteoAnaquel = ConteoAnaquel::where('sku', $producto['id'])
                    ->where('ubicacion_id', $ubicacion_id)
                    ->where('anaquel', $anaquel)
                    ->where('conteo_id', $conteoId) // Filtro por conteo_id
                    ->first();

                if ($conteoAnaquel) {
                    $conteoAnaquel->update(['segundo_conteo' => 1]);
                } else {
                    // Crear registro si no existe
                    ConteoAnaquel::create([
                        'sku' => $producto['id'],
                        'ubicacion_id' => $ubicacion_id,
                        'anaquel' => $anaquel,
                        'conteo_id' => $conteoId,
                        'cantidad' => 0, 
                        'barcode' => $producto['CODIGO_BARRAS'] ?? 'No encontrado',
                        'segundo_conteo' => 1, 
                        'cantidad2' => 0 
                    ]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Exportar datos de resumen como Excel y PDF
    public function exportarResumen(Request $request)
    {
        $resumen = $request->input('resumen');
        $tipo = $request->input('tipo');
        $ubicacion = $request->input('ubicacion');
        $anaquel = $request->input('anaquel');

        if ($tipo == 'excel') {
            return $this->exportarResumenExcel($resumen);
        } elseif ($tipo == 'pdf') {
            return $this->exportarResumenPdf($resumen, $ubicacion, $anaquel);
        }

        return response()->json(['error' => 'Tipo de exportación no válido'], 400);
    }
    
    private function exportarResumenExcel($resumen)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Añadir encabezados
        $sheet->setCellValue('A1', 'Resumen');
        $sheet->setCellValue('B1', 'Códigos');
        $sheet->setCellValue('C1', 'Resultados');
        $sheet->setCellValue('D1', '%');
        
        // Añadir datos del resumen
        foreach ($resumen as $index => $data) {
            $sheet->setCellValue('A' . ($index + 2), $data['resumen']);
            $sheet->setCellValue('B' . ($index + 2), $data['codigos']);
            $sheet->setCellValue('C' . ($index + 2), $data['resultados']);
            $sheet->setCellValue('D' . ($index + 2), $data['porcentaje']);
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'resumen_inventario.xlsx';
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }
    
    private function exportarResumenPdf($resumen, $ubicacion, $anaquel)
    {
        // Limpiar el buffer de salida
        ob_clean();
        ob_start();
    
        $pdf = new \TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
    
        $logoPath = public_path('images/CF.png');
        
        // Verificar si el archivo de logo existe
        if (!file_exists($logoPath)) {
            throw new \Exception("El archivo de logo no existe: " . $logoPath);
        }
    
        // Obtener el nombre de la ubicación usando la función
        $ubicacionNombre = $this->getTonoByUbicacion($ubicacion);
        
        $html = '<table style="width: 100%;"><tr><br>
                    <td><img src="' . $logoPath . '" alt="Logo" width="auto" height="50"></td>
                    <td style="text-align: right;">
                        <h1>Resumen de Inventario</h1>
                        <p>Fecha: ' . date('d/m/Y') . '</p>
                    </td>
                </tr></table>';
    
        $html .= '<h2 style="text-align: left;">Conteo de:<br>Ubicación: ' . htmlspecialchars($ubicacionNombre) . '<br>Anaquel: ' . htmlspecialchars($anaquel) . '</h2>';
        
        // Crear tabla del resumen
        $html .= '<table border="1" cellpadding="5">
                    <thead>
                        <tr style="background-color: #d3d3d3;">
                            <th>Resumen</th>
                            <th>Códigos</th>
                            <th>Resultados</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($resumen as $data) {
            $html .= '<tr>
                        <td>' . $data['resumen'] . '</td>
                        <td>' . $data['codigos'] . '</td>
                        <td>' . $data['resultados'] . '</td>
                        <td>' . $data['porcentaje'] . '</td>
                    </tr>';
        }
        
        $html .= '</tbody></table>';
    
        // Renderizar el HTML al PDF
        $pdf->writeHTML($html, true, false, true, false, '');
    
        $pdf->Ln(40);
    
        // Añadir el pie de página con la certificación
        $html_footer = '<h2 style="text-align: center;">Certificación de Conteo de Inventario</h2>';
        $pdf->writeHTML($html_footer, true, false, true, false, '');
    
        $pdf->Ln(5); 
    
        // Agregar el párrafo con el texto de certificación con mayor espacio entre líneas
        $html_text = '<p style="text-align: justify;">Se hace constar que el presente documento refleja con precisión los resultados obtenidos en los conteos físicos de inventario. Los números aquí presentados han sido verificados y se corresponden con los totales determinados durante dicho proceso de conteo.</p>';
        $pdf->writeHTML($html_text, true, false, true, false, '');
    
        $pdf->Ln(20); 
        
        // Agregar el bloque de la firma
        $html_firma = '<p style="text-align: center;">__________________________<br>Nombre y Firma Responsable</p>';
        $pdf->writeHTML($html_firma, true, false, true, false, '');
    
        $pdf->Output('resumen_inventario.pdf', 'D');
        exit; // 
    }
    
}
