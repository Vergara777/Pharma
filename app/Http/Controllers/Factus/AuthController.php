<?php

namespace App\Http\Controllers\Factus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Factus\FactusService;  

class AuthController extends Controller
{
    /**
     * Obtiene el token de autenticación de Factus.
     *
     * @param FactusService $factusService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(FactusService $factusService)
    {
        $data = $factusService->getToken();

        if ($data) {
            return response()->json($data);
        }

        return response()->json([
            'error' => 'No se pudo obtener el token',
            'message' => 'Error al conectar con la API de Factus',
        ], 400);
    }
}
