<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id','area','asunto','descripcion',
      'prioridad','estado','asignado_a'
    ];

    public function creador()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
