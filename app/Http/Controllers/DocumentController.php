<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // 1. LISTAR ARCHIVOS (LA REGLA DE ORO)
    public function index()
    {
        $user = Auth::user();

        // Si es ADMIN (Servidor), ve todo. Si es USUARIO, solo lo suyo.
        if ($user->role === 'admin') {
            $documents = Document::with('user')->latest()->get();
        } else {
            $documents = $user->documents()->latest()->get();
        }

        return view('dashboard', compact('documents'));
    }

    // 2. SUBIR ARCHIVO
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required',
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240', // Max 10MB
        ]);

        // Guardar archivo en carpeta 'private' (no pública directamente)
        // Se guarda en storage/app/documentos_confidenciales
        $path = $request->file('archivo')->store('documentos_confidenciales');

        Document::create([
            'titulo' => $request->titulo,
            'ruta_archivo' => $path,
            'tipo' => $request->file('archivo')->getClientOriginalExtension(),
            'user_id' => Auth::id(), // Se vincula al usuario logueado
        ]);

        return back()->with('success', 'Documento resguardado exitosamente.');
    }

    // 3. DESCARGAR/VER ARCHIVO
    public function download($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // Seguridad: Solo el dueño O el admin pueden ver esto
        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'ACCESO DENEGADO: No tiene permiso para ver este archivo confidencial.');
        }

        return Storage::download($document->ruta_archivo, $document->titulo . '.' . $document->tipo);
    }

    // 4. PREVISUALIZAR ARCHIVO EN EL NAVEGADOR
    public function preview($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // SEGURIDAD: Solo puede verlo el ADMIN o el DUEÑO
        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'ACCESO DENEGADO: NO TIENE AUTORIZACIÓN PARA VER ESTE DOCUMENTO.');
        }

        if (!\Illuminate\Support\Facades\Storage::exists($document->ruta_archivo)) {
            abort(404, 'EL ARCHIVO FÍSICO NO FUE ENCONTRADO EN LA BÓVEDA.');
        }

        // Obtenemos el tipo exacto del archivo
        $mimeType = \Illuminate\Support\Facades\Storage::mimeType($document->ruta_archivo);

        // Le ordenamos explícitamente al navegador mostrarlo en línea ('inline')
        return response()->make(\Illuminate\Support\Facades\Storage::get($document->ruta_archivo), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->ruta_archivo) . '"'
        ]);
    }
}