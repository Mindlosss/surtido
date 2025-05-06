<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventario extends Model
{
    protected $table = 'REG00521';
    protected $connection = 'sqlsrv';
    use HasFactory;

    public static function obtenerAnaqueles($ubicacion_id) 
    {

        \Log::info("Ubicación ID recibida: " . $ubicacion_id); // Verifica que este valor sea correcto
        try {
            $query = "";

            switch ($ubicacion_id) {
                case 1: // Matriz
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'MATRIZ' ORDER BY TONO ASC";
                    break;
                case 2: // Almacen
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO BETWEEN 'ANAQUEL037' AND 'ANAQUEL112' ORDER BY TONO ASC";
                    break;
                case 3: // Bodega
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'BODEGA' ORDER BY TONO ASC";
                    break;
                case 4: // Milwaukee
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'MILWAUKEE' ORDER BY TONO ASC";
                    break;
                case 5: // Makita
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'MAKITA' ORDER BY TONO ASC";
                    break;
                case 6: // Industrial
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'SUCURSALM' ORDER BY TONO ASC";
                    break;
                case 7: // Synergy
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'SYNERGY' ORDER BY TONO ASC";
                    break;
                case 8: // Tienda Remate
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'TIENDAREMATE' ORDER BY TONO ASC";
                    break;
                case 9: // California   
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'CALIFORNIA' ORDER BY TONO ASC";
                    break;
                case 10: // DIINA   
                    $query = "SELECT DISTINCT TONO, CALIBRE, CAJA FROM REG00521 WHERE TONO = 'DIINA' ORDER BY TONO ASC";
                    break;
                default:
                    // Evitar retornar todos los anaqueles
                    return [];
            }

            $results = DB::connection('sqlsrv')->select($query);

            $filteredResults = [];
            foreach ($results as $result) {
                if (strpos($result->TONO, 'ANAQUEL') !== false) {
                    $filteredResults[] = $result->TONO;
                } elseif (strpos($result->CALIBRE, 'ANAQUEL') !== false) {
                    $filteredResults[] = $result->CALIBRE;
                } elseif (strpos($result->CALIBRE, 'EXHIBICION') !== false) {
                    $filteredResults[] = $result->CALIBRE . ' ' . $result->CAJA;
                }
            }

            return array_values(array_unique($filteredResults));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function obtenerProductoPorBarcodeOSku($barcodeOrSku)
    {
        $query = "
            SELECT 
            ISNULL(rc2.CODIGO_BARRAS, 'No encontrado') AS CODIGO_BARRAS,
            r.ARTICULO,
            r.DESCRIP
        FROM 
            REG00005 r
        LEFT JOIN (
            SELECT 
                ARTICULO,
                CODIGO_BARRAS,
                DESCRIP
            FROM 
                REG00005_COD
            WHERE 
                DESCRIP = 'CODIGO BARRAS' 
        ) rc2 ON r.ARTICULO = rc2.ARTICULO
        WHERE 
            rc2.CODIGO_BARRAS = ? OR r.ARTICULO = ?
        ";

        return DB::connection('sqlsrv')->selectOne($query, [$barcodeOrSku, $barcodeOrSku]);
    }

}