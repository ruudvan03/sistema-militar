<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Militar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-green-900 mb-6 uppercase">Alta de Personal</h2>
        
        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700">Grado</label>
                <input type="text" name="grado" class="w-full border p-2 rounded bg-gray-50" placeholder="Ej: Teniente" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Nombre Completo</label>
                <input type="text" name="name" class="w-full border p-2 rounded bg-gray-50" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Matrícula</label>
                <input type="text" name="matricula" class="w-full border p-2 rounded bg-gray-50 uppercase" required>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Área</label>
                    <input type="text" name="area" class="w-full border p-2 rounded bg-gray-50" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Especialidad</label>
                    <input type="text" name="especialidad" class="w-full border p-2 rounded bg-gray-50" required>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700">Contraseña de Respaldo</label>
                <input type="password" name="password" class="w-full border p-2 rounded bg-gray-50" required>
            </div>

            <button type="submit" class="w-full bg-green-800 hover:bg-green-900 text-white font-bold py-2 px-4 rounded transition">
                REGISTRAR Y OBTENER TOKEN
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Ya tengo mi token</a>
        </div>
    </div>
</body>
</html>