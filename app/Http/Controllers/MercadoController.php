<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MercadoController extends Controller
{
    // Método para mostrar la vista principal
    public function index()
    {
        // Pedidos por surtir (B_SURTIDO = 0)
        $pedidosPorSurtir = DB::connection('sqlsrv')->select("
            SELECT TOP (1000)
                r1.[ARTICULO],
                r1.[FACTURA],
                r1.[DESCRIP],
                r1.[CANTIDAD],
                r1.[TONO],
                r1.[CALIBRE],
                r1.[CAJA],
                r1.[B_SURTIDO],
                r2.[AGENTE],
                r2.[CANCELADA],
                r2.[COMENTARIO],
                r2.[NOMBRE],
                r2.[DIRECCION],
                CONVERT(date, CAST(r1.[FECHA] - 36163 AS datetime)) AS FechaGregoriana
            FROM [GCINTER].[dbo].[REG00501] r1
            JOIN [GCINTER].[dbo].[REG00500] r2 ON r1.FACTURA = r2.FACTURA
            WHERE r1.LETRA = 'M'
                AND r2.AGENTE = '1'
                AND r2.[COMENTARIO] != ''
                AND CONVERT(date, CAST(r1.[FECHA] - 36163 AS datetime)) BETWEEN DATEADD(day, -7, GETDATE()) AND GETDATE()
                AND r1.[FACTURA] NOT IN (
                    SELECT FACTURA
                    FROM [GCINTER].[dbo].[REG00501]
                    WHERE TONO = 'MERCADO LIBRE'
                )
                AND r1.[B_SURTIDO] = 0
            ORDER BY r1.[FACTURA] ASC;
        ");

        // Últimos 15 pedidos ya surtidos (B_SURTIDO = 1) sin límite de fecha y tomando solo los 15 primeros
        $pedidosSurtidos = DB::connection('sqlsrv')->select("
            SELECT TOP (15)
                r1.[FACTURA],
                r2.[NOMBRE],
                COUNT(r1.ARTICULO) AS num_articulos,
                CONVERT(date, CAST(r1.[FECHA] - 36163 AS datetime)) AS FechaGregoriana
            FROM [GCINTER].[dbo].[REG00501] r1
            JOIN [GCINTER].[dbo].[REG00500] r2 ON r1.FACTURA = r2.FACTURA
            WHERE r1.LETRA = 'M'
                AND r2.AGENTE = '1'
                AND r2.[COMENTARIO] != ''
                AND r1.[B_SURTIDO] = 1
            GROUP BY r1.[FACTURA], r2.[NOMBRE], r1.[FECHA]
            ORDER BY r1.[FACTURA] ASC;
        ");

        // Pasar los pedidos a la vista
        return view('mercado', compact('pedidosPorSurtir', 'pedidosSurtidos'));
    }

    // Función para actualizar los pedidos con AJAX
    public function fetchPedidos()
    {
        // Obtener los pedidos por surtir (B_SURTIDO = 0)
        $pedidosPorSurtir = DB::connection('sqlsrv')->select("
            SELECT TOP (1000)
                r1.[ARTICULO],
                r1.[FACTURA],
                r1.[DESCRIP],
                r1.[CANTIDAD],
                r1.[TONO],
                r1.[CALIBRE],
                r1.[CAJA],
                r1.[B_SURTIDO],
                r2.[AGENTE],
                r2.[CANCELADA],
                r2.[COMENTARIO],
                r2.[NOMBRE],
                r2.[DIRECCION],
                CONVERT(date, CAST(r1.[FECHA] - 36163 AS datetime)) AS FechaGregoriana
            FROM [GCINTER].[dbo].[REG00501] r1
            JOIN [GCINTER].[dbo].[REG00500] r2 ON r1.FACTURA = r2.FACTURA
            WHERE r1.LETRA = 'M'
                AND r2.AGENTE = '1'
                AND r2.[COMENTARIO] != ''
                AND CONVERT(date, CAST(r1.[FECHA] - 36163 AS datetime)) BETWEEN DATEADD(day, -7, GETDATE()) AND GETDATE()
                AND r1.[FACTURA] NOT IN (
                    SELECT FACTURA
                    FROM [GCINTER].[dbo].[REG00501]
                    WHERE TONO = 'MERCADO LIBRE'
                )
                AND r1.[B_SURTIDO] = 0
            ORDER BY r1.[FACTURA] ASC;
        ");

        // Últimos 15 pedidos ya surtidos (B_SURTIDO = 1) sin límite de fecha
        $pedidosSurtidos = DB::connection('sqlsrv')->select("
            SELECT TOP (15)
                r1.[FACTURA],
                r2.[NOMBRE],
                COUNT(r1.ARTICULO) AS num_articulos,
                CONVERT(date, CAST(r1.[FECHA] - 36163 AS datetime)) AS FechaGregoriana
            FROM [GCINTER].[dbo].[REG00501] r1
            JOIN [GCINTER].[dbo].[REG00500] r2 ON r1.FACTURA = r2.FACTURA
            WHERE r1.LETRA = 'M'
                AND r2.AGENTE = '1'
                AND r2.[COMENTARIO] != ''
                AND r1.[B_SURTIDO] = 1
            GROUP BY r1.[FACTURA], r2.[NOMBRE], r1.[FECHA]
            ORDER BY r1.[FACTURA] DESC;
        ");

        // Devolver los pedidos como JSON para AJAX
        return response()->json([
            'pedidosPorSurtir' => $pedidosPorSurtir,
            'pedidosSurtidos' => $pedidosSurtidos,
        ]);
    }

    // Función para marcar un pedido como surtido
    public function marcarComoSurtido(Request $request)
    {
        $factura = $request->input('factura');

        try {
            // Actualizar los registros que coinciden con el número de factura
            $affectedRows = DB::connection('sqlsrv')
                ->table('REG00501')
                ->where('FACTURA', $factura)
                ->update(['B_SURTIDO' => 1]);

            if ($affectedRows > 0) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontraron registros para actualizar.']);
            }
        } catch (\Exception $e) {
            // Capturar el error y devolver un mensaje detallado
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
