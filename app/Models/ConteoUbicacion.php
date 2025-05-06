<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConteoUbicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';
    protected $fillable = [
        'nombre',
    ];

    public function conteoAnaqueles()
    {
        return $this->hasMany(ConteoAnaquel::class, 'ubicacion_id');
    }
}
