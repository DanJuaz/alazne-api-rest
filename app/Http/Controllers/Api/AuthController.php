<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('usuario', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Inicio de sesión exitoso'
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return $this->success([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    public function refresh(): JsonResponse
    {
        $token = auth()->refresh();
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => auth()->user(),
            'message' => 'Token refrescado exitosamente'
        ]);
    }

    public function me(): JsonResponse
    {
        return $this->success(auth()->user());
    }
}