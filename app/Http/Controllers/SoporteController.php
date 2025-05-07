<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SoporteController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            return view('soporte.admin');
        }
        return view('soporte.user');
    }
    
}
