<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Confidencial - EMT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 min-h-screen flex flex-col items-center justify-center p-4">
    
    <div class="mb-6 text-center w-full max-w-4xl mt-4">
        
        <div class="flex justify-center mb-6">
            <img src="{{ asset('logo_emt.png') }}" alt="Escudo de la Escuela Militar de Transmisiones" class="w-32 h-32 md:w-40 md:h-40 object-contain drop-shadow-2xl">
        </div>

        <div class="space-y-1">
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-widest uppercase drop-shadow-md">Secretaría de la Defensa Nacional</h1>
            <h2 class="text-lg md:text-xl font-semibold text-gray-300 tracking-wider uppercase">Dirección General de Educación Militar</h2>
            <h3 class="text-sm md:text-base text-gray-400 tracking-wider uppercase">Rectoría de la Universidad del Ejército, Fuerza Aérea y Guardia Nacional</h3>
            
            <div class="pt-4 pb-1">
                <h4 class="text-xl md:text-2xl font-bold text-green-500 uppercase tracking-widest border-b-2 border-green-700 pb-2 inline-block">
                    Escuela Militar de Transmisiones
                </h4>
            </div>
            <p class="text-base text-gray-300 mt-2 font-medium tracking-wide">Licenciatura en Tecnologías de la Información y Comunicaciones</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md relative z-10 mb-8 overflow-hidden border border-gray-700">
        
        <div class="bg-gradient-to-b from-green-600 to-green-800 p-4 text-center border-b-4 border-green-900">
            <h2 class="text-lg font-bold text-white uppercase tracking-wider flex items-center justify-center gap-2">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Acceso Confidencial - EMT
            </h2>
        </div>

        <div class="p-6 bg-gray-50">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-600 text-red-800 p-3 mb-4 text-sm font-bold shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.token') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Token de Autenticación:</label>
                    <textarea name="token" rows="3" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-green-700 focus:ring-1 focus:ring-green-700 font-mono text-sm text-gray-700 shadow-inner" placeholder="Pegue su token de 32 bits aquí..." required></textarea>
                </div>

                <button type="submit" class="w-full bg-green-800 text-white font-bold py-3 px-4 rounded hover:bg-green-900 transition flex justify-center items-center gap-2 shadow-lg border-b-4 border-green-950 hover:border-green-900 active:border-b-0 active:mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    INGRESAR AL SISTEMA
                </button>
            </form>
        </div>
    </div>

    <div class="text-center text-gray-400 text-xs md:text-sm bg-gray-800 p-5 rounded-lg shadow-inner border border-gray-700 w-full max-w-2xl mt-auto">
        <p class="uppercase font-bold text-gray-300 mb-3 border-b border-gray-600 pb-2 inline-block tracking-widest">Presentado por</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6 text-left w-max mx-auto">
            <p><span class="font-bold text-green-500">Grado:</span> <span class="text-gray-200">Sgto. 2/o. de Cdtes.</span></p>
            <p><span class="font-bold text-green-500">Nombre:</span> <span class="text-gray-200">José de Jesús García Bello.</span></p>
            <p><span class="font-bold text-green-500">Matrícula:</span> <span class="text-gray-200">(D-7495657)</span></p>
            <p><span class="font-bold text-green-500">Cargo:</span> <span class="text-gray-200">Cmte. 1/er. Ptn. 1/a Secc. 2/a Cía. Cdtes.</span></p>
        </div>
    </div>

</body>
</html>