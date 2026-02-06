<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoteDocumentController extends Controller
{
    public function show(Lote $lote): StreamedResponse
    {
        // Verificar que el usuario esté autenticado
        abort_unless(auth()->check(), 403);
        
        // Verificar que el lote tenga un documento
        abort_unless($lote->documento_archivo, 404);
        
        // Verificar que el archivo existe
        abort_unless(Storage::exists($lote->documento_archivo), 404);
        
        // Obtener el tipo MIME del archivo
        $mimeType = Storage::mimeType($lote->documento_archivo);
        
        // Retornar el archivo
        return Storage::response($lote->documento_archivo, null, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($lote->documento_archivo) . '"'
        ]);
    }
}
