<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    // Mostrar todas las reservas
    public function index()
    {
        return Reserva::all();
    }

    // Mostrar una reserva específica
    public function show($id)
    {
        return Reserva::findOrFail($id);
    }

    // Crear una nueva reserva
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'nombre_cliente' => 'required|string',
            'telefono' => 'required|string',
            'servicio' => 'required|string',
            'trabajador_id' => 'required|exists:trabajadores,id'
        ]);

        return Reserva::create($request->all());
    }

    // Actualizar una reserva
    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);
        
        $request->validate([
            'fecha' => 'nullable|date',
            'hora' => 'nullable|date_format:H:i',
            'nombre_cliente' => 'nullable|string',
            'telefono' => 'nullable|string',
            'servicio' => 'nullable|string',
            'trabajador_id' => 'nullable|exists:trabajadores,id'
        ]);

        $reserva->update($request->all());
        return $reserva;
    }

    // Eliminar una reserva
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->delete();
        return response()->noContent();
    }
}
