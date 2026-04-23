<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RF-API Sentinel') - Dashboard de Seguridad</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .glass-panel {
            background: rgba(31, 41, 55, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(75, 85, 99, 0.3);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-900 text-gray-100 font-sans p-6">

    <div class="max-w-7xl mx-auto">
        @yield('content')
    </div>

    <script>
        // Inicializar Iconos
        lucide.createIcons();

        // Lógica de Pestañas
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'bg-green-600', 'text-white');
                btn.classList.add('bg-gray-800', 'text-gray-400');
            });

            document.getElementById(tabId).classList.add('active');
            const activeBtn = document.getElementById('btn-' + tabId);
            
            if (tabId === 'sistema') {
                activeBtn.classList.add('bg-green-600', 'text-white');
            } else {
                activeBtn.classList.add('bg-blue-600', 'text-white');
            }
        }
    </script>
</body>
</html>