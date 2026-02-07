<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50 dark:bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión Expirada | PharmaSoft</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-6">
    <div class="max-w-md w-full glass rounded-3xl p-8 shadow-2xl text-center">
        <div class="mb-6 inline-flex items-center justify-center w-20 h-20 bg-amber-100 dark:bg-amber-900/30 rounded-full text-amber-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">¡Ups! Sesión Expirada</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
            Por seguridad, tu sesión ha caducado debido a la inactividad o a múltiples refrescos. No te preocupes, tus datos están a salvo.
        </p>
        
        <div class="space-y-3">
            <a href="{{ url()->current() }}" class="block w-full py-4 px-6 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-2xl transition duration-200 shadow-lg shadow-amber-500/30 transform hover:scale-[1.02]">
                Refrescar Página
            </a>
            
            <a href="/admin" class="block w-full py-4 text-amber-600 dark:text-amber-400 font-medium hover:underline transition">
                Ir al Dashboard
            </a>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-400">
            PharmaSoft 2.0 &bull; Sistema Seguro de Monitoreo
        </div>
    </div>
</body>
</html>
