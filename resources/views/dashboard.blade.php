<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Electoral Argentina 2025</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">
            Sistema de Carga y Conteo de Comicios Argentina 2025
        </h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card Importación -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">📥 Importación</h2>
                <ul class="space-y-2">
                    <li><a href="{{ route('provincias.index') }}" class="text-blue-600 hover:underline">Provincias</a></li>
                    <li><a href="{{ route('listas.index') }}" class="text-blue-600 hover:underline">Listas</a></li>
                    <li><a href="{{ route('candidatos.index') }}" class="text-blue-600 hover:underline">Candidatos</a></li>
                    <li><a href="{{ route('mesas.index') }}" class="text-blue-600 hover:underline">Mesas</a></li>
                    <li><a href="{{ route('telegramas.index') }}" class="text-blue-600 hover:underline">Telegramas</a></li>
                </ul>
            </div>
            
            <!-- Card Resultados -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">📊 Resultados</h2>
                <ul class="space-y-2">
                    <li><a href="{{ route('resultados.nacional') }}" class="text-blue-600 hover:underline">Nacional</a></li>
                    <li><a href="{{ route('provincias.index') }}" class="text-blue-600 hover:underline">Por Provincia</a></li>
                </ul>
            </div>
            
            <!-- Card Ayuda -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">❓ Ayuda</h2>
                <p class="text-gray-600 text-sm">
                    Sistema de gestión electoral para carga de telegramas y generación de reportes.
                </p>
            </div>
        </div>
    </div>
</body>
</html>