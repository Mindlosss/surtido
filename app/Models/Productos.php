<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Productos extends Model
{
    use HasFactory;
    protected $table = 'REG00005'; 
    protected $connection = 'sqlsrv';

    public static function obtenerProductos($searchTerm = null, $clasif = null, $marca = null)
    {
        $query = "
            DECLARE @dolar AS DECIMAL(10,4)
            SET @dolar = (SELECT TOP 1 DOLAR FROM REG01000)
            
            DECLARE @hoy AS DATE = GETDATE();
            
            SELECT 
                RTRIM(P.ARTICULO) AS CODIGO,
                RTRIM(P.DESCRIP) AS DESCRIPCION,
                P.EXISTENCIA AS EX_T,
                U.EXISTENCIA AS EX,
                RTRIM(P.MARCA) AS MARCA,
                RTRIM(P.CLASIF_MRP) AS ESTATUS,
                RTRIM(P.CLASIF_PROD_ABC) AS CLASIF,
                RTRIM(P.PICTURE) AS 'RUTA_IMAGEN',
                P.ALTO,
                P.ANCHO,
                P.LARGO,
                P.PESO,
                P.DESIMP3 AS DES,
                P.PORCE,
                U.TONO,
                U.CALIBRE,
                U.CAJA,
                C.DESCRIP AS CAT,
                FORMAT(DATEADD(DAY, P.F_INICIO - 36163, '1900-01-01'), 'dd-MM-yyyy') AS 'FECHA_INICIO',
                FORMAT(DATEADD(DAY, P.F_FINAL - 36163, '1900-01-01'), 'dd-MM-yyyy') AS 'FECHA_FINAL',
                
                ROUND(
                    (
                        CASE 
                            WHEN @hoy BETWEEN DATEADD(DAY, P.F_INICIO - 36163, '1900-01-01') 
                                        AND DATEADD(DAY, P.F_FINAL - 36163, '1900-01-01')
                            THEN
                                CASE 
                                    WHEN P.PORCE IS NOT NULL AND P.PORCE <> 0 
                                    THEN (P.PRECIO_1 - (P.PRECIO_1 * P.PORCE / 100))
                                    WHEN P.PORCE IS NULL OR P.PORCE = 0 THEN P.DESIMP3
                                    ELSE P.PRECIO_1
                                END
                            ELSE P.PRECIO_1
                        END
                    ) * 
                    (CASE WHEN P.DOLAR = 'T' THEN @dolar ELSE 1 END) * 
                    (CASE WHEN P.IVA = 'T' THEN 1.16 ELSE 1.0 END), 
                2) AS PRECIO_1_NETO
            FROM REG00005 P
            LEFT OUTER JOIN REG00521 U ON P.ARTICULO = U.ARTICULO
            LEFT OUTER JOIN REG00001 C ON C.LINEA = P.LINEA
            WHERE P.CLASIF_MRP IN ('DE LINEA','NUEVO','REMATE','SOBRE PEDIDO')
              AND NOT (P.DESACTIVAR = 1 AND P.EXISTENCIA = 0)
        ";
        
        $params = [];
        
        // Filtro de búsqueda libre
        if ($searchTerm) {
            $query .= " AND (P.ARTICULO LIKE ? OR P.DESCRIP LIKE ?)";
            $searchTerm = '%'.$searchTerm.'%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        } else {
            // Si NO quieres devolver nada cuando no hay búsqueda, aplica AND 1=0
            // (o quítalo si deseas que devuelva todo por omisión)
            $query .= " AND 1=0";
        }
    
        // Filtro de clasificación (CLASIF_PROD_ABC) si viene
        if ($clasif) {
            $query .= " AND P.CLASIF_PROD_ABC = ?";
            $params[] = $clasif;
        }
    
        // Filtro de marca si viene
        if ($marca) {
            $query .= " AND P.MARCA = ?";
            $params[] = $marca;
        }
    
        return DB::connection('sqlsrv')->select($query, $params);
    }

    public static function getDistinctClasif()
    {
        // Ajusta la consulta según tu lógica de filtrado
        return DB::connection('sqlsrv')
            ->table('REG00005')
            ->selectRaw("DISTINCT RTRIM(CLASIF_PROD_ABC) AS clasif")
            ->whereIn('CLASIF_MRP', ['DE LINEA','NUEVO','REMATE','SOBRE PEDIDO'])
            ->where(function($q){
                // Si quieres excluir los desactivados con 0 existencias
                $q->where('DESACTIVAR', '<>', 1)
                  ->orWhere('EXISTENCIA', '>', 0);
            })
            ->orderBy('clasif')
            ->get();
    }

    /**
     * Retorna listado de marcas (MARCA) distintas
     */
    public static function getDistinctMarca()
    {
        return DB::connection('sqlsrv')
            ->table('REG00005')
            ->selectRaw("DISTINCT RTRIM(MARCA) AS marca")
            ->whereIn('CLASIF_MRP', ['DE LINEA','NUEVO','REMATE','SOBRE PEDIDO'])
            ->where(function($q){
                // Mismo filtro para ser consistente
                $q->where('DESACTIVAR', '<>', 1)
                  ->orWhere('EXISTENCIA', '>', 0);
            })
            ->orderBy('marca')
            ->get();
    }

    public static function getDistinctCategoria()
    {
        return DB::connection('sqlsrv')
            ->table('REG00005 AS p')
            ->join('REG00001 AS c', 'p.LINEA', '=', 'c.LINEA')
            ->selectRaw("DISTINCT RTRIM(c.DESCRIP) AS cat")
            ->whereIn('p.CLASIF_MRP', ['DE LINEA','NUEVO','REMATE','SOBRE PEDIDO'])
            ->where(function($q){
                // Misma lógica para excluir desactivados sin existencia:
                $q->where('p.DESACTIVAR', '<>', 1)
                  ->orWhere('p.EXISTENCIA', '>', 0);
            })
            ->orderBy('cat')
            ->get();
    }
    
}