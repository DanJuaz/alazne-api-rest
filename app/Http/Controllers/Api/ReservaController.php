<?php

namespace App\Http\Controllers\Api;

use App\Models\Reserva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservaController extends BaseApiController
{
    protected $casts = [
        // formatea fecha_hora_inicio como "YYYY-MM-DD HH:mm"
        'fecha_hora_inicio' => 'datetime:Y-m-d H:i',
    ];
    
    public function index(): JsonResponse
    {
        $reservas = Reserva::where('status', 1)->get();
        return $this->success($reservas);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:200',
            'fecha_hora_inicio' => 'required|date_format:Y-m-d H:i',
            'estado' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ]);
        }

        $reserva = Reserva::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'fecha_hora_inicio' => $request->fecha_hora_inicio,
            'estado' => $request->estado,
            'status' => 1
        ]);

        return $this->success($reserva);
    }

    public function show(Reserva $reserva): JsonResponse
    {
        return $this->success($reserva);
    }

    public function update(Request $request, Reserva $reserva): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:100',
            'apellidos' => 'sometimes|required|string|max:100',
            'telefono' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:200',
            'fecha_hora_inicio' => 'sometimes|required|date_format:Y-m-d H:i',
            'estado' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ]);
        }

        $reserva->update($request->all());
        return $this->success($reserva);
    }

    public function destroy(Reserva $reserva): JsonResponse
    {
        $reserva->update(['status' => 66]);
        return $this->success([
            'message' => 'Reserva eliminada exitosamente'
        ]);
    }

    public $timestamps = false;
}
