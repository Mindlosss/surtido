<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ordenes extends Model
{
    public static function obtenerPedidos()
    {
        $query = "SELECT  
                    A.PEDIDO,
                    A.IMPORTE AS IMP_TOTAL,
					B.IMPORTE AS IMP_TOTAL_PROD,
                    B.UNITARIO AS IMP_UNI,
                    CAST(CAST(A.FECHA - 36163 as DATETIME) as DATE) as FECHA_OC,  
                    A.PROV,  
                    B.ARTICULO AS COD_CON,  
                    C.BAJAART AS COD_PRO,  
                    B.CANTIDAD,  
                    B.PENDIENTES,  
                    B.ENTREGADOS,  
                    D.NOMBRE
                    
                FROM REG00508 A  
                INNER JOIN REG00509 B ON A.PEDIDO = B.PEDIDO  
                INNER JOIN REG00005 C ON B.ARTICULO = C.ARTICULO  
                INNER JOIN REG00004 D ON A.PROV = D.PROV  
                WHERE CAST(A.FECHA - 36163 as DATETIME) >= DATEADD(DAY, -90, GETDATE())  
                ORDER BY A.PEDIDO DESC;";
        return DB::connection('sqlsrv')->select($query);
    }
}