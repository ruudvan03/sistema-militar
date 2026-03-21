<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $documents = Document::withTrashed()->with('user')->orderBy('created_at', 'desc')->get();
        } else {
            $documents = $user->documents()->orderBy('created_at', 'desc')->get();
        }

        return view('dashboard', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required',
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240', 
        ]);

        $path = $request->file('archivo')->store('documentos_confidenciales');

        Document::create([
            'titulo' => $request->titulo,
            'ruta_archivo' => $path,
            'tipo' => $request->file('archivo')->getClientOriginalExtension(),
            'user_id' => Auth::id(), 
        ]);

        return back()->with('success', 'Documento resguardado exitosamente.');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'ACCESO DENEGADO: No tiene permiso para descargar este archivo confidencial.');
        }

        return Storage::download($document->ruta_archivo, $document->titulo . '.' . $document->tipo);
    }

    public function preview($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'ACCESO DENEGADO: NO TIENE AUTORIZACIÓN PARA VER ESTE DOCUMENTO.');
        }

        if (!Storage::exists($document->ruta_archivo)) {
            abort(404, 'EL ARCHIVO FÍSICO NO FUE ENCONTRADO EN LA BÓVEDA.');
        }

        $mimeType = Storage::mimeType($document->ruta_archivo);

        return response()->make(Storage::get($document->ruta_archivo), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->ruta_archivo) . '"'
        ]);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'ACCESO DENEGADO: NO TIENE AUTORIZACIÓN PARA MODIFICAR ESTE DOCUMENTO.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240'
        ]);

        if ($request->hasFile('archivo')) {
            if (Storage::exists($document->ruta_archivo)) {
                Storage::delete($document->ruta_archivo);
            }

            $file = $request->file('archivo');
            $path = $file->store('documentos_confidenciales');

            $document->ruta_archivo = $path;
            $document->tipo = $file->getClientOriginalExtension();
        }

        $document->titulo = $request->titulo;
        $document->estado = 'Actualizado';
        $document->save();

        return back()->with('success', 'Documento actualizado y reemplazado con éxito.');
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && $document->user_id !== $user->id) {
            abort(403, 'NO TIENE AUTORIZACIÓN PARA DESTRUIR ESTE DOCUMENTO.');
        }

        if (Storage::exists($document->ruta_archivo)) {
            Storage::delete($document->ruta_archivo);
        }

        $document->estado = 'Destruido';
        $document->save(); 
        $document->delete(); 

        return back()->with('success', 'Documento destruido. Quedará registro en el historial de la Dirección.');
    }
}