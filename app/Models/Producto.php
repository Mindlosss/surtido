<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Producto extends Model
{
    protected $table = 'REG00501';
    protected $connection = 'sqlsrv';

    public static function obtenerProductosPorFactura($ubicacion, $mostrarNoSurtidos, $busqueda = '')
    {
        $surtidoCondition = $mostrarNoSurtidos ? "AND (reg00501.B_SURTIDO IS NULL OR reg00501.B_SURTIDO = 0)" : "";
        $tonoCondition = $ubicacion === 'almacen' 
            ? "AND reg00501.TONO BETWEEN 'ANAQUEL037' AND 'ANAQUEL104'"
            : "AND (reg00501.TONO = 'BODEGA' OR reg00501.TONO = 'ANAQUEL001')";

        $busquedaCondition = $busqueda ? "AND (reg00500.FACTURA LIKE '%$busqueda%' OR reg00500.NOMBRE LIKE '%$busqueda%')" : "";

        $fechaCondition = $busqueda ? "WHERE CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) >= DATEADD(day, -20, GETDATE())" : "WHERE CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) = CONVERT(date, GETDATE())";

        $query = "
            SELECT 
                reg00500.*, 
                reg00501.*,  
                CONVERT(date, CAST(reg00500.FECHA - 36163 AS datetime)) AS FechaGregoriana,
                REG00007.NOMBRE AS NombreAgente,
                reg00500.NOMBRE AS NombreCliente 
            FROM [GCINTER].[dbo].[REG00500]
            LEFT OUTER JOIN Reg00501 ON reg00500.factura = reg00501.factura AND reg00500.letra = reg00501.letra
            LEFT JOIN [GCINTER].[dbo].[REG00007] ON reg00500.AGENTE = REG00007.AGENTE
            $fechaCondition
            AND reg00500.letra IN ('G', 'E', 'F', 'B') $surtidoCondition $tonoCondition $busquedaCondition
            ORDER BY reg00500.FECHA ASC, reg00500.FACTURA ASC
    
        ";

        return DB::connection('sqlsrv')->select($query);
    }

    public static function obtenerProductosPorNota($ubicacion, $mostrarNoSurtidos, $busqueda = '')
    {
        $surtidoCondition = $mostrarNoSurtidos ? "AND (reg00513.B_SURTIDO IS NULL OR reg00513.B_SURTIDO = 0)" : "";
        $tonoCondition = $ubicacion === 'almacen' 
            ? "AND reg00513.TONO BETWEEN 'ANAQUEL037' AND 'ANAQUEL104'"
            : "AND (reg00513.TONO = 'BODEGA' OR reg00513.TONO = 'ANAQUEL001')";

        $busquedaCondition = $busqueda ? "AND (REG00512.NOTA LIKE '%$busqueda%' OR REG00512.NOMBRE LIKE '%$busqueda%')" : "";

        $fechaCondition = $busqueda ? "WHERE CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) >= DATEADD(day, -20, GETDATE())" : "WHERE CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) = CONVERT(date, GETDATE())";

        $query = "
            SELECT 
                REG00512.*,  
                REG00512.NUMERO AS NUMERO_13,
                REG00513.*,
                CONVERT(date, CAST(REG00512.FECHA - 36163 AS datetime)) AS FechaGregoriana, 
                CONVERT(VARCHAR, DATEADD(SECOND, (CAST(REG00512.HORA AS FLOAT) / 100.0), CAST((REG00512.FECHA - 36163) AS DATETIME)), 108) AS HoraFormateada,
                REG00007.NOMBRE AS NombreAgente,
                REG00512.NOMBRE AS NombreCliente,
                REG00005.DESCRIP AS DESCRIP             
            FROM [GCINTER].[dbo].[REG00512]
            LEFT OUTER JOIN REG00513 ON REG00512.NOTA = REG00513.NOTA AND REG00512.letra = REG00513.letra
            LEFT OUTER JOIN reg00005 ON reg00005.ARTICULO = REG00513.ARTICULO
            LEFT JOIN [GCINTER].[dbo].[REG00007] ON REG00512.AGENTE = REG00007.AGENTE
            $fechaCondition
            AND REG00512.letra IN ('', 'z', 'E', 'F', 'B') $surtidoCondition $tonoCondition $busquedaCondition
            ORDER BY REG00512.FECHA ASC, REG00512.NOTA ASC;
    
        ";

        return DB::connection('sqlsrv')->select($query);
    }
}
