<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SurtidoController extends Controller
{
    public function index(Request $request)
    {
        $ubicacion = $request->input('ubicacion', 'almacen');

        $facturasPorSurtir = $this->obtenerFacturasPorSurtir($ubicacion);
        $facturasSurtidas = $this->obtenerFacturasSurtidas($ubicacion);
        $notasPorSurtir = $this->obtenerNotasPorSurtir($ubicacion);
        $notasSurtidas = $this->obtenerNotasSurtidas($ubicacion);

        return view('surtido', compact('facturasPorSurtir', 'facturasSurtidas', 'notasPorSurtir', 'notasSurtidas', 'ubicacion'));
    }

    public function fetchPedidos(Request $request)
    {
        $ubicacion = $request->input('ubicacion', 'almacen'); // valor por defecto "almacen"

        $facturasPorSurtir = $this->obtenerFacturasPorSurtir($ubicacion);
        $facturasSurtidas = $this->obtenerFacturasSurtidas($ubicacion);
        $notasPorSurtir = $this->obtenerNotasPorSurtir($ubicacion);
        $notasSurtidas = $this->obtenerNotasSurtidas($ubicacion);

        return response()->json([
            'facturasPorSurtir' => $facturasPorSurtir,
            'facturasSurtidas' => $facturasSurtidas,
            'notasPorSurtir' => $notasPorSurtir,
            'notasSurtidas' => $notasSurtidas,
        ]);
    }

    /**
     * Aplica la condición de ubicación en las consultas de FACTURAS
     */
    private function condicionUbicacionFacturas($ubicacion)
    {
        switch($ubicacion) {
            case 'almacen':
                return "AND reg00501.TONO BETWEEN 'ANAQUEL037' AND 'ANAQUEL112'";
            case 'bodega':
                return "AND (reg00501.TONO = 'BODEGA' OR reg00501.TONO = 'ANAQUEL001')";
            case 'matriz':
                return "AND reg00501.TONO = 'MATRIZ' AND reg00501.CALIBRE = 'EXHIBICION'";
            case 'milwaukee':
                return "AND reg00501.TONO = 'MILWAUKEE' AND reg00501.CALIBRE = 'EXHIBICION'";
            case 'makita':
                return "AND reg00501.TONO = 'MAKITA' AND reg00501.CALIBRE = 'EXHIBICION'";
            case 'sucursalm':
                return "AND reg00501.TONO = 'SUCURSALM' AND reg00501.CALIBRE = 'EXHIBICION'";
            case 'california':
                return "AND reg00501.TONO = 'CALIFORNIA' AND reg00501.CALIBRE = 'EXHIBICION'";
            default:
                return "";
        }
    }

    /**
     * Aplica la condición de ubicación en las consultas de NOTAS
     */
    private function condicionUbicacionNotas($ubicacion)
    {
        switch($ubicacion) {
            case 'almacen':
                return "AND REG00513.TONO BETWEEN 'ANAQUEL037' AND 'ANAQUEL112'";
            case 'bodega':
                return "AND (REG00513.TONO = 'BODEGA' OR REG00513.TONO = 'ANAQUEL001')";
            case 'matriz':
                return "AND REG00513.TONO = 'MATRIZ' AND REG00513.CALIBRE = 'EXHIBICION'";
            case 'milwaukee':
                return "AND REG00513.TONO = 'MILWAUKEE' AND REG00513.CALIBRE = 'EXHIBICION'";
            case 'makita':
                return "AND REG00513.TONO = 'MAKITA' AND REG00513.CALIBRE = 'EXHIBICION'";
            case 'sucursalm':
                return "AND REG00513.TONO = 'SUCURSALM' AND REG00513.CALIBRE = 'EXHIBICION'";
            case 'california':
                return "AND REG00513.TONO = 'CALIFORNIA' AND REG00513.CALIBRE = 'EXHIBICION'";
            default:
                return "";
        }
    }

    private function obtenerFacturasPorSurtir($ubicacion)
    {
        $ubicacionCond = $this->condicionUbicacionFacturas($ubicacion);
    
        return DB::connection('sqlsrv')->select("
            SELECT 
                reg00500.*, 
                reg00501.*, 
                CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) AS FechaGregoriana,
                REG00007.NOMBRE AS NombreAgente,
                reg00500.NOMBRE AS NombreCliente,
                SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6), 1, 2) 
                    + ':' + SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6), 3, 2)
                    + ':' + SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6), 5, 2) AS HORA_CFD_FORMATEADA
            FROM [GCINTER].[dbo].[REG00500]
            LEFT OUTER JOIN Reg00501 
                ON reg00500.factura = reg00501.factura 
               AND reg00500.letra   = reg00501.letra
            LEFT JOIN [GCINTER].[dbo].[REG00007] 
                ON reg00500.AGENTE = REG00007.AGENTE
            WHERE reg00501.B_SURTIDO = 0
              AND reg00500.letra IN ('G', 'E', 'F', 'B')
              AND CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) = CONVERT(date, GETDATE())
              $ubicacionCond
            ORDER BY reg00500.FECHA ASC, reg00500.FACTURA ASC;
        ");
    }

    /**
     * FACTURAS Surtidas - 
     * Trae todas las líneas de una factura, siempre que la factura tenga al menos un producto en la ubicación dada
     */
    private function obtenerFacturasSurtidas($ubicacion)
    {
        $ubicacionCond = $this->condicionUbicacionFacturas($ubicacion);
        
        return DB::connection('sqlsrv')->select("
            SELECT
                reg00500.*,
                reg00501.*,
                CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) AS FechaGregoriana,
                REG00007.NOMBRE AS NombreAgente,
                reg00500.NOMBRE AS NombreCliente,
                SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6),1,2)
                    + ':' + SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6),3,2)
                    + ':' + SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6),5,2) 
                    AS HORA_CFD_FORMATEADA
            FROM [GCINTER].[dbo].[REG00500]
            LEFT OUTER JOIN Reg00501 
              ON reg00500.factura = reg00501.factura 
             AND reg00500.letra   = reg00501.letra
            LEFT JOIN [GCINTER].[dbo].[REG00007] 
              ON reg00500.AGENTE = REG00007.AGENTE
            WHERE reg00500.letra IN ('G', 'E', 'F', 'B')
              AND CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) = CONVERT(date, GETDATE())
              AND reg00500.FACTURA IN (
                  SELECT DISTINCT reg00501.FACTURA
                  FROM [GCINTER].[dbo].[Reg00501] reg00501
                  WHERE 1=1
                  $ubicacionCond
              )
            ORDER BY reg00500.FECHA DESC, reg00500.FACTURA DESC;
        ");
    }
    
    private function obtenerNotasPorSurtir($ubicacion)
    {
        $ubicacionCond = $this->condicionUbicacionNotas($ubicacion);

        return DB::connection('sqlsrv')->select("
            SELECT 
                REG00512.*,  
                REG00512.NUMERO AS NUMERO_13,
                REG00513.*,
                CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) AS FechaGregoriana, 
                REG00007.NOMBRE AS NombreAgente,
                REG00512.NOMBRE AS NombreCliente,
                REG00005.DESCRIP AS DESCRIP,
                CONVERT(varchar(8), CAST(((REG00512.FECHA - 36163) + ((1.0 / 86400.0) * (CAST(REG00512.HORA AS FLOAT) /100.0))) AS datetime), 108) AS HORA_FORMATEADA
            FROM [GCINTER].[dbo].[REG00512]
            LEFT OUTER JOIN REG00513 
                ON REG00512.NOTA  = REG00513.NOTA 
               AND REG00512.letra = REG00513.letra
            LEFT OUTER JOIN reg00005 
                ON reg00005.ARTICULO = REG00513.ARTICULO
            LEFT JOIN [GCINTER].[dbo].[REG00007] 
                ON REG00512.AGENTE = REG00007.AGENTE
            WHERE REG00513.B_SURTIDO = 0
              AND REG00512.letra IN ('', 'z', 'E', 'F', 'B')
              AND CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) = CONVERT(date, GETDATE())
              $ubicacionCond
            ORDER BY REG00512.FECHA ASC, REG00512.NOTA ASC;
        ");
    }

    /**
     * NOTAS Surtidas - 
     * Trae todas las líneas de una nota, siempre que la nota tenga al menos un producto en la ubicación dada
     */
    private function obtenerNotasSurtidas($ubicacion)
    {
        $ubicacionCond = $this->condicionUbicacionNotas($ubicacion);
    
        return DB::connection('sqlsrv')->select("
            SELECT
                REG00512.*,
                REG00512.NUMERO AS NUMERO_13,
                REG00513.*,
                CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) AS FechaGregoriana, 
                REG00007.NOMBRE AS NombreAgente,
                REG00512.NOMBRE AS NombreCliente,
                REG00005.DESCRIP AS DESCRIP,
                CONVERT(varchar(8), CAST(((REG00512.FECHA - 36163)
                + ((1.0 / 86400.0)*(CAST(REG00512.HORA AS FLOAT)/100.0))) AS datetime), 108) 
                AS HORA_FORMATEADA
            FROM [GCINTER].[dbo].[REG00512]
            LEFT OUTER JOIN REG00513 
              ON REG00512.NOTA  = REG00513.NOTA 
             AND REG00512.letra = REG00513.letra
            LEFT OUTER JOIN reg00005 
              ON reg00005.ARTICULO = REG00513.ARTICULO
            LEFT JOIN [GCINTER].[dbo].[REG00007] 
              ON REG00512.AGENTE = REG00007.AGENTE
            WHERE REG00512.letra IN ('', 'z', 'E', 'F', 'B')
              AND CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) = CONVERT(date, GETDATE())
              AND REG00512.NOTA IN (
                  SELECT DISTINCT REG00513.NOTA
                  FROM [GCINTER].[dbo].[REG00513] REG00513
                  WHERE 1=1
                  $ubicacionCond
              )
            ORDER BY REG00512.FECHA DESC, REG00512.NOTA DESC;
        ");
    }

    public function marcarComoSurtido(Request $request)
    {
        $tipo = $request->input('tipo');
        $numero = $request->input('numero');
        $estado = $request->input('estado', 1);
        $ubicacion = $request->input('ubicacion', 'almacen'); // Ubicación actual
    
        // Nombre del surtidor (usuario en sesión)
        $surtidor = auth()->user()->name;
    
        // Obtenemos la condición de ubicación
        if ($tipo === 'factura') {
            $ubicacionCond = $this->condicionUbicacionFacturas($ubicacion);
        } else {
            $ubicacionCond = $this->condicionUbicacionNotas($ubicacion);
        }
    
        try {
            if ($tipo === 'factura') {
                // Marcar el producto como surtido o no encontrado
                DB::connection('sqlsrv')->update("
                    UPDATE [GCINTER].[dbo].[Reg00501]
                    SET B_SURTIDO = :estado
                    WHERE NUMERO = :numero
                    AND LETRA IN ('G', 'E', 'F', 'B')
                ", ['numero' => $numero, 'estado' => $estado]);
    
                // Obtener FACTURA y LETRA a partir del NUMERO
                $facturaInfo = DB::connection('sqlsrv')->select("
                    SELECT TOP 1 FACTURA, LETRA
                    FROM [GCINTER].[dbo].[Reg00501]
                    WHERE NUMERO = :numero
                    AND LETRA IN ('G','E','F','B')
                ", ['numero' => $numero]);
    
                if (!empty($facturaInfo)) {
                    $factura = $facturaInfo[0]->FACTURA;
                    $letra = $facturaInfo[0]->LETRA;
    
                    // Verificar si no hay pendientes en esta ubicación
                    $pendientes = DB::connection('sqlsrv')->select("
                        SELECT COUNT(*) AS total
                        FROM [GCINTER].[dbo].[Reg00501]
                        WHERE FACTURA = :factura
                          AND LETRA   = :letra
                          AND B_SURTIDO = 0
                          $ubicacionCond
                    ", ['factura' => $factura, 'letra' => $letra]);
    
                    if (!empty($pendientes) && $pendientes[0]->total == 0) {
                        // Factura completa en esta ubicación
                        $facturaCompleta = DB::connection('sqlsrv')->select("
                            SELECT 
                                REG00500.FACTURA AS numero,
                                REG00500.LETRA AS letra,
                                REG00007.NOMBRE AS NombreAgente,
                                CONVERT(date, CAST(REG00500.FECHA - 36163 AS datetime)) AS FechaGregoriana,
                                SUBSTRING(
                                    RIGHT('000000' + CAST(REG00500.HORA_CFD AS VARCHAR(6)), 6), 1, 2
                                ) + ':' + SUBSTRING(
                                    RIGHT('000000' + CAST(REG00500.HORA_CFD AS VARCHAR(6)), 6), 3, 2
                                ) + ':' + SUBSTRING(
                                    RIGHT('000000' + CAST(REG00500.HORA_CFD AS VARCHAR(6)), 6), 5, 2
                                ) AS hora
                            FROM [GCINTER].[dbo].[REG00500]
                            LEFT JOIN [GCINTER].[dbo].[REG00007] 
                                ON REG00500.AGENTE = REG00007.AGENTE
                            WHERE FACTURA = :factura
                              AND LETRA   = :letra
                        ", ['factura' => $factura, 'letra' => $letra]);
    
                        $itemsFactura = DB::connection('sqlsrv')->select("
                            SELECT 
                                Reg00501.ARTICULO, REG00005.DESCRIP, Reg00501.CANTIDAD, Reg00501.B_SURTIDO
                            FROM [GCINTER].[dbo].[Reg00501]
                            LEFT JOIN [GCINTER].[dbo].[REG00005] 
                                ON REG00005.ARTICULO = Reg00501.ARTICULO
                            WHERE Reg00501.FACTURA = :factura
                              AND Reg00501.LETRA   = :letra
                              $ubicacionCond
                        ", ['factura' => $factura, 'letra' => $letra]);
    
                        if (!empty($facturaCompleta)) {
                            $f = $facturaCompleta[0];
                            $productosList = [];
                            foreach ($itemsFactura as $item) {
                                if ($item->B_SURTIDO == 1) {
                                    $estadoStr = 'Surtido';
                                } elseif ($item->B_SURTIDO == 2) {
                                    $estadoStr = 'No encontrado';
                                } else {
                                    $estadoStr = 'Pendiente';
                                }
                                $productosList[] = trim($item->ARTICULO)
                                    . '(' . (int)$item->CANTIDAD
                                    . ', ' . $estadoStr . ')';
                            }
                            $productosStr = implode(', ', $productosList);
    
                            // Insertar en MySQL
                            DB::connection('mysql')->table('registro')->insert([
                                'numero' => $f->numero,
                                'vendedor' => $f->NombreAgente,
                                'surtidor' => $surtidor,
                                'productos' => $productosStr,
                                'dt_pedido' => $f->FechaGregoriana . ' ' . $f->hora,
                                'dt_surtido' => now(),
                                'tipo' => 'factura',
                                'letra' => $f->letra,
                                'ubicacion' => $ubicacion
                            ]);
                        }
                    }
                }
    
            } elseif ($tipo === 'nota') {
                // Marcar producto
                DB::connection('sqlsrv')->update("
                    UPDATE [GCINTER].[dbo].[Reg00513]
                    SET B_SURTIDO = :estado
                    WHERE NUMERO = :numero
                    AND LETRA IN ('', 'z', 'E', 'F', 'B')
                ", ['numero' => $numero, 'estado' => $estado]);
    
                // Obtener NOTA y LETRA a partir del NUMERO
                $notaInfo = DB::connection('sqlsrv')->select("
                    SELECT TOP 1 NOTA, LETRA
                    FROM [GCINTER].[dbo].[Reg00513]
                    WHERE NUMERO = :numero
                      AND LETRA IN ('', 'z', 'E', 'F', 'B')
                ", ['numero' => $numero]);
    
                if (!empty($notaInfo)) {
                    $nota = $notaInfo[0]->NOTA;
                    $letra = $notaInfo[0]->LETRA;
    
                    // Verificar si no hay pendientes en esta ubicación
                    $pendientesNota = DB::connection('sqlsrv')->select("
                        SELECT COUNT(*) AS total
                        FROM [GCINTER].[dbo].[Reg00513]
                        WHERE NOTA = :nota
                          AND LETRA   = :letra
                          AND B_SURTIDO = 0
                          $ubicacionCond
                    ", ['nota' => $nota, 'letra' => $letra]);
    
                    if (!empty($pendientesNota) && $pendientesNota[0]->total == 0) {
                        // Nota completa en esta ubicación
                        $notaCompleta = DB::connection('sqlsrv')->select("
                            SELECT
                                REG00512.NOTA AS numero,
                                REG00512.LETRA AS letra,
                                REG00007.NOMBRE AS NombreAgente,
                                CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) AS FechaGregoriana,
                                CONVERT(varchar(8),
                                    CAST(
                                        ((REG00512.FECHA - 36163)
                                          + ((1.0 / 86400.0)*(CAST(REG00512.HORA AS FLOAT)/100.0)))
                                        AS datetime
                                    ),
                                    108
                                ) AS hora
                            FROM [GCINTER].[dbo].[REG00512]
                            LEFT JOIN [GCINTER].[dbo].[REG00007] 
                                ON REG00512.AGENTE = REG00007.AGENTE
                            WHERE NOTA = :nota
                              AND LETRA   = :letra
                        ", ['nota' => $nota, 'letra' => $letra]);
    
                        $itemsNota = DB::connection('sqlsrv')->select("
                            SELECT 
                                Reg00513.ARTICULO, REG00005.DESCRIP, Reg00513.CANTIDAD, Reg00513.B_SURTIDO
                            FROM [GCINTER].[dbo].[Reg00513]
                            LEFT JOIN [GCINTER].[dbo].[REG00005]
                                ON REG00005.ARTICULO = Reg00513.ARTICULO
                            WHERE Reg00513.NOTA  = :nota
                              AND Reg00513.LETRA = :letra
                              $ubicacionCond
                        ", ['nota' => $nota, 'letra' => $letra]);
    
                        if (!empty($notaCompleta)) {
                            $n = $notaCompleta[0];
                            $productosList = [];
                            foreach ($itemsNota as $item) {
                                if ($item->B_SURTIDO == 1) {
                                    $estadoStr = 'Surtido';
                                } elseif ($item->B_SURTIDO == 2) {
                                    $estadoStr = 'No encontrado';
                                } else {
                                    $estadoStr = 'Pendiente';
                                }
                                $productosList[] = trim($item->ARTICULO)
                                    . '(' . (int)$item->CANTIDAD
                                    . ', ' . $estadoStr . ')';
                            }
                            $productosStr = implode(', ', $productosList);
    
                            // Insertar en MySQL
                            DB::connection('mysql')->table('registro')->insert([
                                'numero' => $n->numero,
                                'vendedor' => $n->NombreAgente,
                                'surtidor' => $surtidor,
                                'productos' => $productosStr,
                                'dt_pedido' => $n->FechaGregoriana . ' ' . $n->hora,
                                'dt_surtido' => now(),
                                'tipo' => 'nota',
                                'letra' => $n->letra,
                                'ubicacion' => $ubicacion
                            ]);
                        }
                    }
                }
    
            } else {
                return response()->json(['success' => false, 'message' => 'Tipo inválido.'], 400);
            }
    
            return response()->json(['success' => true, 'message' => 'Marcado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
        
    public function buscar(Request $request)
    {
        $numero = $request->input('numero');
        if(!$numero) {
            return response()->json([], 200);
        }

        // Buscar en facturas 
        $facturas = DB::connection('sqlsrv')->select("
            SELECT 
                reg00500.*, 
                reg00501.*, 
                CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) AS FechaGregoriana,
                REG00007.NOMBRE AS NombreAgente,
                reg00500.NOMBRE AS NombreCliente,
                SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6), 1, 2) 
                    + ':' + SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6), 3, 2)
                    + ':' + SUBSTRING(RIGHT('000000' + CAST(reg00500.HORA_CFD AS VARCHAR(6)), 6), 5, 2) AS HORA_CFD_FORMATEADA
            FROM [GCINTER].[dbo].[REG00500]
            LEFT OUTER JOIN Reg00501 
                ON reg00500.factura = reg00501.factura 
               AND reg00500.letra   = reg00501.letra
            LEFT JOIN [GCINTER].[dbo].[REG00007] 
                ON reg00500.AGENTE = REG00007.AGENTE
            WHERE reg00500.FACTURA = :numero
              AND reg00500.letra IN ('G', 'E', 'F', 'B')
              AND CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) >= DATEADD(DAY, -7, CONVERT(date, GETDATE()))
            ORDER BY reg00500.FECHA ASC, reg00500.FACTURA ASC;
        ", ['numero' => $numero]);

        // Buscar en notas 
        $notas = DB::connection('sqlsrv')->select("
            SELECT 
                REG00512.*,  
                REG00512.NUMERO AS NUMERO_13,
                REG00513.*,
                CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) AS FechaGregoriana, 
                REG00007.NOMBRE AS NombreAgente,
                REG00512.NOMBRE AS NombreCliente,
                REG00005.DESCRIP AS DESCRIP,
                CONVERT(varchar(8), CAST(((REG00512.FECHA - 36163) 
                  + ((1.0 / 86400.0) * (CAST(REG00512.HORA AS FLOAT)/100.0))) AS datetime), 108) 
                  AS HORA_FORMATEADA
            FROM [GCINTER].[dbo].[REG00512]
            LEFT OUTER JOIN REG00513 
                ON REG00512.NOTA  = REG00513.NOTA 
               AND REG00512.letra = REG00513.letra
            LEFT OUTER JOIN reg00005 
                ON reg00005.ARTICULO = REG00513.ARTICULO
            LEFT JOIN [GCINTER].[dbo].[REG00007] 
                ON REG00512.AGENTE = REG00007.AGENTE
            WHERE REG00512.NOTA = :numero
              AND REG00512.letra IN ('', 'z', 'E', 'F', 'B')
              AND CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) >= DATEADD(DAY, -7, CONVERT(date, GETDATE()))
            ORDER BY REG00512.FECHA ASC, REG00512.NOTA ASC;
        ", ['numero' => $numero]);

        $resultados = [];

        // Agrupar facturas
        if(!empty($facturas)) {
            $facturasGrouped = [];
            foreach($facturas as $fac) {
                $key = 'factura_'.$fac->FACTURA.'_'.$fac->LETRA;  
                if(!isset($facturasGrouped[$key])) {
                    $facturasGrouped[$key] = [
                        'tipo' => 'factura',
                        'numero' => $fac->FACTURA,
                        'letra' => $fac->LETRA,
                        'FechaGregoriana' => $fac->FechaGregoriana,
                        'hora' => $fac->HORA_CFD_FORMATEADA,
                        'items' => []
                    ];
                }
                $facturasGrouped[$key]['items'][] = (array)$fac;
            }
        
            foreach($facturasGrouped as $fg) {
                $resultados[] = $fg;
            }
        }

        // Agrupar notas
        if(!empty($notas)) {
            $notasGrouped = [];
            foreach($notas as $n) {
                $key = 'nota_'.$n->NOTA.'_'.$n->LETRA; 
                if(!isset($notasGrouped[$key])) {
                    $notasGrouped[$key] = [
                        'tipo' => 'nota',
                        'numero' => $n->NOTA,
                        'letra' => $n->LETRA,
                        'FechaGregoriana' => $n->FechaGregoriana,
                        'hora' => $n->HORA_FORMATEADA,
                        'items' => []
                    ];
                }
                $notasGrouped[$key]['items'][] = (array)$n;
            }
        
            foreach($notasGrouped as $ng) {
                $resultados[] = $ng;
            }
        }

        return response()->json($resultados);
    }
}
