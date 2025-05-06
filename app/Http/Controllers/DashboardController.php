<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $showAlert = false;
        
        if(auth()->user()->can('ver-alerta-precios')) {
            $bloqueoP = DB::connection('sqlsrv')
                        ->table('reg01000')
                        ->value('BLOQUEO_P');
            $showAlert = ($bloqueoP === 'F');
        }
        
        return view('dashboard', ['showAlert' => $showAlert]);
    }
    
    public function checkBloqueoP() {
        if(!auth()->user()->can('ver-alerta-precios')) {
            return response()->json(['showAlert' => false]);
        }
        
        $bloqueoP = DB::connection('sqlsrv')
                    ->table('reg01000')
                    ->value('BLOQUEO_P');
                    
        return response()->json(['showAlert' => ($bloqueoP === 'F')]);
    }
}