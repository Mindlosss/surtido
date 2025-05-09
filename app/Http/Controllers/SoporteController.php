<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class SoporteController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('creador');

        if (! auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($qf) use($q){
                $qf->where('asunto','like',"%{$q}%")
                   ->orWhere('descripcion','like',"%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('estado', $request->status);
        }

        $tickets = $query->orderBy('created_at','desc')->get();
        $responsables = ['Alan','Maribel','Fer'];

        return view(
            auth()->user()->hasRole('admin')
                ? 'soporte.admin'
                : 'soporte.user',
            compact('tickets','responsables')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
          'area'        => 'required|string',
          'asunto'      => 'required|string',
          'descripcion' => 'required|string',
        ]);

        $data['user_id'] = auth()->id();
        Ticket::create($data);

        return redirect()->route('soporte')
                         ->with('success','Ticket creado');
    }

    public function update(Request $request, Ticket $ticket)
    {
        // **Solo admin puede actualizar estado/asignado**
        if (! auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $data = $request->validate([
          'estado'     => 'required|in:Abierto,En Progreso,Cerrado',
          'asignado_a' => 'nullable|in:Alan,Maribel,Fer',
        ]);

        $ticket->update($data);

        return back()->with('success','Ticket actualizado');
    }
}