<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConteoAnaquel extends Model
{
    use HasFactory;

    protected $table = 'conteo_anaqueles';
    protected $fillable = [
        'conteo_id',
        'barcode',
        'sku',
        'cantidad',
        'cantidad2',
        'anaquel',
        'ubicacion_id',
        'segundo_conteo',
    ];
    
    public function conteo()
    {
        return $this->belongsTo(Conteo::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(ConteoUbicacion::class, 'ubicacion_id');
    }
}
