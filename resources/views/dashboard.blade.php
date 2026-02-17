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
            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-green-800">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    Subir Documentación
                </h2>
                
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-2 rounded mb-4 text-sm font-bold border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Título del Documento</label>
                        <input type="text" name="titulo" class="w-full border p-2 rounded bg-gray-50" placeholder="Ej: Reporte Diario" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Archivo (PDF, Excel, Word)</label>
                        <input type="file" name="archivo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                    </div>

                    <button type="submit" class="w-full bg-green-800 text-white font-bold py-2 rounded hover:bg-green-900 transition">
                        RESGUARDAR ARCHIVO
                    </button>
                </form>
            </div>

            <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                <h3 class="font-bold text-gray-500 text-sm uppercase mb-2">Mi Credencial</h3>
                <div class="text-sm">
                    <p><strong>Matrícula:</strong> {{ Auth::user()->matricula }}</p>
                    <p><strong>Especialidad:</strong> {{ Auth::user()->especialidad }}</p>
                    <p><strong>Rol:</strong> 
                        @if(Auth::user()->role == 'admin')
                            <span class="text-red-600 font-bold">SERVIDOR CENTRAL</span>
                        @else
                            <span class="text-green-600 font-bold">PERSONAL OPERATIVO</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-800 text-white p-4 border-b border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-bold">Expediente Digital</h2>
                    <span class="bg-gray-700 text-xs px-2 py-1 rounded">{{ $documents->count() }} Archivos</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Documento</th>
                                <th class="px-6 py-3">Tipo</th>
                                @if(Auth::user()->role == 'admin')
                                    <th class="px-6 py-3">Subido Por</th>
                                @endif
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $doc->titulo }}
                                </td>
                                <td class="px-6 py-4 uppercase">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $doc->tipo }}</span>
                                </td>
                                
                                @if(Auth::user()->role == 'admin')
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900 font-bold">{{ $doc->user->grado }} {{ $doc->user->name }}</div>
                                        <div class="text-xs">{{ $doc->user->matricula }}</div>
                                    </td>
                                @endif

                                <td class="px-6 py-4">
                                    {{ $doc->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('documents.download', $doc->id) }}" class="font-medium text-green-600 hover:underline hover:text-green-800">
                                        Descargar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">
                                    No hay documentación registrada en el sistema.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>