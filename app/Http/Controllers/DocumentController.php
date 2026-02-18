<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // 1. LISTAR ARCHIVOS (LA REGLA DE ORO + HISTORIAL ADMIN)
    public function index()
    {
        $user = Auth::user();

        // Si es ADMIN (Servidor), ve todo, INCLUSO LOS DESTRUIDOS (withTrashed)
        if ($user->role === 'admin') {
            $documents = Document::withTrashed()->with('user')->orderBy('created_at', 'desc')->get();
        } else {
            // El usuario normal solo ve los suyos activos
            $documents = $user->documents()->orderBy('created_at', 'desc')->get();
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
        $path = $request->file('archivo')->store('documentos_confidenciales');

        Document::create([
            'titulo' => $request->titulo,
            'ruta_archivo' => $path,
            'tipo' => $request->file('archivo')->getClientOriginalExtension(),
            'user_id' => Auth::id(), // Se vincula al usuario logueado
        ]);

        return back()->with('success', 'Documento resguardado exitosamente.');
    }

    // 3. DESCARGAR ARCHIVO
    public function download($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // Seguridad: Solo el dueño O el admin pueden ver esto
        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'ACCESO DENEGADO: No tiene permiso para descargar este archivo confidencial.');
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

        if (!Storage::exists($document->ruta_archivo)) {
            abort(404, 'EL ARCHIVO FÍSICO NO FUE ENCONTRADO EN LA BÓVEDA.');
        }

        // Obtenemos el tipo exacto del archivo
        $mimeType = Storage::mimeType($document->ruta_archivo);

        // Le ordenamos explícitamente al navegador mostrarlo en línea ('inline')
        return response()->make(Storage::get($document->ruta_archivo), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->ruta_archivo) . '"'
        ]);
    }

    // 5. ACTUALIZAR ARCHIVO (Reemplazo)
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // Seguridad: Solo el Admin o el Dueño pueden actualizar
        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'ACCESO DENEGADO: NO TIENE AUTORIZACIÓN PARA MODIFICAR ESTE DOCUMENTO.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240'
        ]);

        // Si el usuario subió un archivo nuevo para reemplazar el viejo
        if ($request->hasFile('archivo')) {
            // 1. Borramos el archivo físico viejo de la bóveda
            if (Storage::exists($document->ruta_archivo)) {
                Storage::delete($document->ruta_archivo);
            }

            // 2. Guardamos el nuevo archivo
            $file = $request->file('archivo');
            $path = $file->store('documentos_confidenciales');

            // 3. Actualizamos las rutas en la base de datos
            $document->ruta_archivo = $path;
            $document->tipo = $file->getClientOriginalExtension();
        }

        // Actualizamos el título y cambiamos la etiqueta de estado
        $document->titulo = $request->titulo;
        $document->estado = 'Actualizado';
        $document->save();

        return back()->with('success', 'Documento actualizado y reemplazado con éxito.');
    }

    // 6. DESTRUIR ARCHIVO (Borrador Lógico)
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // Seguridad
        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'NO TIENE AUTORIZACIÓN PARA DESTRUIR ESTE DOCUMENTO.');
        }

        // 1. Borramos el archivo físico para que no ocupe espacio ni pueda ser filtrado
        if (Storage::exists($document->ruta_archivo)) {
            Storage::delete($document->ruta_archivo);
        }

        // 2. Etiquetamos como destruido y lo ocultamos (SoftDelete)
        $document->estado = 'Destruido';
        $document->save(); 
        $document->delete(); // Esto no lo borra de la BD, solo le pone fecha de "deleted_at"

        return back()->with('success', 'Documento destruido. Quedará registro en el historial de la Dirección.');
    }
}