<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Generado</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-2xl border-t-4 border-green-800 text-center">
        <div class="mb-4 text-green-700">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-800 mb-2">¡Registro Exitoso!</h1>
        <p class="text-gray-600 mb-6">Bienvenido, {{ $user->grado }} {{ $user->name }}.</p>
        
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded mb-6 text-left">
            <p class="font-bold text-yellow-800 text-sm uppercase mb-2">Instrucciones de Seguridad:</p>
            <p class="text-sm text-yellow-700">Copia el siguiente <strong class="font-bold">TOKEN DE ACCESO</strong>. Lo necesitarás para ingresar al sistema cada vez que quieras subir o ver archivos confidenciales.</p>
        </div>

        <div class="relative mb-6">
            <textarea id="tokenArea" readonly class="w-full h-32 p-4 bg-gray-900 text-green-400 font-mono text-xs rounded border border-gray-700 resize-none focus:outline-none">{{ $token }}</textarea>
            <button onclick="copyToken()" class="absolute top-2 right-2 bg-gray-700 hover:bg-gray-600 text-white text-xs py-1 px-3 rounded">
                Copiar
            </button>
        </div>

        <a href="{{ route('login') }}" class="inline-block bg-green-800 text-white font-bold py-3 px-8 rounded hover:bg-green-900 transition shadow-lg">
            IR AL LOGIN
        </a>
    </div>

    <script>
        function copyToken() {
            var copyText = document.getElementById("tokenArea");
            copyText.select();
            document.execCommand("copy");
            alert("Token copiado al portapapeles");
        }
    </script>
</body>
</html>