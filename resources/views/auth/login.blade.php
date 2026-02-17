<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Confidencial</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 h-screen flex flex-col items-center justify-center p-4">
    
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white tracking-widest uppercase">Sistema de Documentación</h1>
        <p class="text-gray-400 text-sm">Ejército, Fuerza Aérea y Guardia Nacional</p>
    </div>

    <div class="bg-white p-8 rounded shadow-2xl w-full max-w-lg relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-700 via-green-500 to-green-700 rounded-t"></div>

        <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Ingrese su Token de Acceso</h2>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.token') }}" method="POST">
            @csrf
            <div class="mb-6">
                <textarea name="token" rows="4" class="w-full border-2 border-gray-300 p-3 rounded focus:outline-none focus:border-green-600 font-mono text-sm text-gray-600" placeholder="Pegue su token JWT aquí..." required></textarea>
            </div>

            <button type="submit" class="w-full bg-green-800 text-white font-bold py-3 px-4 rounded hover:bg-green-700 transition flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                ACCEDER
            </button>
        </form>

        <div class="mt-6 text-center border-t pt-4">
            <p class="text-sm text-gray-500">¿No tiene un token?</p>
            <a href="{{ route('register') }}" class="text-green-700 font-bold hover:underline">Registrar Nuevo Personal</a>
        </div>
    </div>
</body>
</html>