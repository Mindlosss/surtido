<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conteo extends Model
{
    use HasFactory;

    protected $table = 'conteos';
    protected $fillable = [
        'nombre',
        'fecha_hora',
    ];

    public function conteoAnaqueles()
    {
        return $this->hasMany(ConteoAnaquel::class);
    }
}
