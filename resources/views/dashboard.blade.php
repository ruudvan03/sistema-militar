<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Mando</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-gray-900 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 bg-green-700 rounded-full flex items-center justify-center font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight">{{ Auth::user()->grado }} {{ Auth::user()->name }}</h1>
                    <span class="text-xs text-gray-400 uppercase tracking-widest">{{ Auth::user()->area }}</span>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-red-700 hover:bg-red-800 px-4 py-2 rounded text-sm font-bold transition">CERRAR SESIÓN</button>
            </form>
        </div>
    </nav>

    <div class="container mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1">
            
            @if(Auth::user()->role == 'admin')
            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-600 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586a1 1 0 01.293-.707l7.457-7.457A6 6 0 1121 9z"></path></svg>
                    Regenerar Acceso
                </h2>

                <form action="{{ route('admin.regenerar_token') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="text" name="matricula" placeholder="Matrícula del elemento" class="w-full border p-2 rounded bg-gray-50 focus:border-blue-500 focus:outline-none uppercase font-mono" required>
                    <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 rounded transition shadow">REGENERAR TOKEN</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-800 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Alta de Personal
                </h2>

                @if(session('new_user_token'))
                    <div class="bg-yellow-50 text-yellow-900 p-4 rounded mb-4 border border-yellow-300 shadow-sm">
                        <p class="font-bold text-sm uppercase">Elemento: <span class="text-black">{{ session('new_user_name') }}</span></p>
                        <p class="font-bold text-xs uppercase mt-3 text-red-700">TOKEN DE ACCESO:</p>
                        <textarea id="tokenToCopy" readonly class="w-full h-20 text-xs font-mono p-2 bg-white border border-yellow-400 mt-1 rounded focus:outline-none">{{ session('new_user_token') }}</textarea>
                        <button type="button" onclick="copiarToken()" id="copyBtn" class="mt-2 w-full bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold py-2 rounded flex items-center justify-center gap-2 transition shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span id="copyBtnText">COPIAR TOKEN</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any() && !request()->hasFile('archivo')) 
                    <div class="bg-red-100 border-l-4 border-red-600 text-red-800 p-3 mb-4 text-sm font-bold shadow-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.create_user') }}" method="POST" class="space-y-4 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Grado</label>
                        <input type="text" name="grado" placeholder="Ej: Cabo" class="w-full border p-2 rounded bg-gray-50 focus:border-red-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nombre Completo</label>
                        <input type="text" name="name" placeholder="Nombre completo del elemento" class="w-full border p-2 rounded bg-gray-50 focus:border-red-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Matrícula</label>
                        <input type="text" name="matricula" placeholder="No. de Matrícula" class="w-full border p-2 rounded bg-gray-50 uppercase focus:border-red-500 focus:outline-none" required>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Área</label>
                            <input type="text" name="area" placeholder="Ej: Sanidad" class="w-full border p-2 rounded bg-gray-50 focus:border-red-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Área de Trabajo</label>
                            <input type="text" name="especialidad" placeholder="Ej: Transmisiones" class="w-full border p-2 rounded bg-gray-50 focus:border-red-500 focus:outline-none" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-red-800 hover:bg-red-900 font-bold py-2.5 rounded transition text-white shadow">
                        GENERAR ACCESO
                    </button>
                </form>
            </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-green-800 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    Subir Documentación
                </h2>
                
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-sm font-bold border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Título del Documento</label>
                        <input type="text" name="titulo" class="w-full border p-2 rounded bg-gray-50 focus:border-green-600 focus:outline-none" placeholder="Ej: Reporte Diario" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Archivo (PDF, Excel, Word)</label>
                        <input type="file" name="archivo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                    </div>
                    <button type="submit" class="w-full bg-green-800 text-white font-bold py-2.5 rounded hover:bg-green-900 transition shadow">
                        RESGUARDAR ARCHIVO
                    </button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-gray-400">
                <h3 class="font-bold text-gray-500 text-sm uppercase mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    Mi Credencial
                </h3>
                <div class="text-sm space-y-2">
                    <p><strong class="text-gray-700">Matrícula:</strong> {{ Auth::user()->matricula }}</p>
                    <p><strong class="text-gray-700">Área de Trabajo:</strong> {{ Auth::user()->especialidad }}</p>
                    <p><strong class="text-gray-700">Nivel de Acceso:</strong> 
                        @if(Auth::user()->role == 'admin')
                            <span class="text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded border border-red-200">SERVIDOR CENTRAL</span>
                        @else
                            <span class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded border border-green-200">PERSONAL OPERATIVO</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-800 text-white p-4 border-b border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Expediente Digital
                    </h2>
                    <span class="bg-gray-700 text-xs px-2 py-1 rounded font-bold">{{ $documents->count() }} Archivos</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4">Documento</th>
                                <th class="px-6 py-4">Estado</th>
                                @if(Auth::user()->role == 'admin')
                                    <th class="px-6 py-4">Subido Por</th>
                                @endif
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                            <tr class="bg-white border-b hover:bg-gray-50 transition {{ $doc->trashed() ? 'opacity-70 bg-red-50' : '' }}">
                                <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                    {{ $doc->titulo }}
                                    <div class="text-xs text-gray-400 font-normal mt-1 uppercase">{{ $doc->tipo }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($doc->estado == 'Original')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded border border-blue-300">ORIGINAL</span>
                                    @elseif($doc->estado == 'Actualizado')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded border border-yellow-300">ACTUALIZADO</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-1 rounded border border-red-300">DESTRUIDO</span>
                                    @endif
                                </td>
                                @if(Auth::user()->role == 'admin')
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900 font-bold">{{ $doc->user->grado }} {{ $doc->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $doc->user->matricula }}</div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-xs font-medium">
                                    {{ $doc->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center flex justify-center gap-4">
                                    @if($doc->trashed())
                                        <span class="text-xs text-red-600 font-bold italic">ARCHIVO INACCESIBLE</span>
                                    @else
                                        <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('documents.download', $doc->id) }}" class="text-green-600 hover:text-green-800 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                        @if(Auth::user()->role == 'admin' || Auth::user()->id == $doc->user_id)
                                            <button onclick="document.getElementById('edit-modal-{{$doc->id}}').showModal()" class="text-yellow-600 hover:text-yellow-800 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('¿CONFIRMA LA DESTRUCCIÓN?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            <dialog id="edit-modal-{{$doc->id}}" class="p-0 rounded-lg shadow-2xl border-t-4 border-yellow-500 w-full max-w-md backdrop:bg-black/50">
                                <div class="bg-white p-6 text-left">
                                    <h3 class="font-bold text-lg mb-4 text-gray-800 flex items-center gap-2">Reemplazar Documento</h3>
                                    <form action="{{ route('documents.update', $doc->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                        @csrf @method('PUT')
                                        <input type="text" name="titulo" value="{{ $doc->titulo }}" class="w-full border p-2 rounded focus:border-yellow-500 focus:outline-none" required>
                                        <input type="file" name="archivo" class="w-full text-sm text-gray-500">
                                        <div class="flex justify-end gap-2 mt-6">
                                            <button type="button" onclick="document.getElementById('edit-modal-{{$doc->id}}').close()" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                                            <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>
                            @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium text-lg italic">No hay archivos en el sistema.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copiarToken() {
            var tokenTextarea = document.getElementById("tokenToCopy");
            tokenTextarea.select();
            navigator.clipboard.writeText(tokenTextarea.value).then(function() {
                var btnText = document.getElementById("copyBtnText");
                var textoOriginal = btnText.innerHTML;
                btnText.innerHTML = "¡COPIADO CON ÉXITO!";
                setTimeout(function() { btnText.innerHTML = textoOriginal; }, 2500);
            });
        }
    </script>
</body>
</html>