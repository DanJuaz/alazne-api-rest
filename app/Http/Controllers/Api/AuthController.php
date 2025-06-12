<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'usuario' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = Usuario::where('usuario', $request->usuario)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'usuario' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }
            // Generar token aleatorio y guardarlo
            $token = Str::random(60);
            $user->token = $token;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Usuario::where('token', $request->bearerToken())->first();

            if ($user) {
                $user->token = null;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión: ' . $e->getMessage()
            ], 500);
        }
    }
}