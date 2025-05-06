<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $table = 'registro';
    protected $connection = 'mysql';

    protected $fillable = [
        'vendedor',
        'fecha_surtido',
        'productos',
        'tipo',
        
    ];
}
