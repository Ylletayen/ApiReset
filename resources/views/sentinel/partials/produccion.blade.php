<div id="sistema" class="tab-content max-w-4xl mx-auto glass-panel rounded-xl shadow-2xl">
    <form action="{{ route('sentinel.settings.update') }}" method="POST">
        @csrf
        <div class="p-6 border-b border-gray-700 flex items-center justify-between bg-gray-800/30">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="server" class="text-green-500 w-6 h-6"></i> 
                    Configuración de Despliegue (Gateway)
                </h2>
                <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-wider">Interfaz de control para el motor de inferencia en tiempo real.</p>
            </div>
            <span class="flex items-center gap-2 text-[10px] bg-green-500/10 text-green-400 px-3 py-1 rounded-full border border-green-500/20">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></span>
                SISTEMA ACTIVO
            </span>
        </div>

        <div class="p-8 space-y-10">
            <section>
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="text-xs font-bold text-green-400 uppercase tracking-[0.2em]">1. Entorno de Ejecución C++</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-semibold text-gray-500 uppercase">Ruta del Binario RF (.exe / .so)</label>
                        <div class="relative">
                            <i data-lucide="terminal" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600"></i>
                            <input type="text" name="cpp_path" value="{{ cache('sentinel_cpp_path', '/usr/local/bin/rf_classifier_cpp') }}" 
                                   class="w-full bg-black/40 border border-gray-800 rounded-lg pl-10 pr-4 py-2.5 text-sm text-blue-300 font-mono focus:border-green-500 outline-none transition-all">
                        </div>
                        <p class="text-[9px] text-gray-600 italic">Archivo ejecutable del modelo Random Forest compilado.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-semibold text-gray-500 uppercase">Manual OpenAPI (swagger.json)</label>
                        <div class="relative">
                            <i data-lucide="file-json" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600"></i>
                            <input type="text" name="swagger_path" value="{{ cache('sentinel_swagger_path', '/var/www/api/swagger.json') }}" 
                                   class="w-full bg-black/40 border border-gray-800 rounded-lg pl-10 pr-4 py-2.5 text-sm text-green-300 font-mono focus:border-green-500 outline-none transition-all">
                        </div>
                        <p class="text-[9px] text-gray-600 italic">Referencia para el Flag de Consistencia[cite: 13].</p>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="text-xs font-bold text-green-400 uppercase tracking-[0.2em]">2. Políticas de Mitigación (IPS)</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="ips_policy" value="block" class="peer sr-only" {{ cache('sentinel_ips_policy', 'block') == 'block' ? 'checked' : '' }}>
                        <div class="p-4 bg-gray-900/50 border border-gray-800 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-500/5 transition-all group-hover:bg-gray-800/50">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="shield-off" class="w-5 h-5 text-red-500"></i>
                                <span class="text-sm font-bold text-white">Modo IPS</span>
                            </div>
                            <p class="text-[10px] text-gray-500 leading-tight">Bloqueo automático (HTTP 403) ante sospecha > 90%.</p>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="ips_policy" value="silent" class="peer sr-only" {{ cache('sentinel_ips_policy', 'block') == 'silent' ? 'checked' : '' }}>
                        <div class="p-4 bg-gray-900/50 border border-gray-800 rounded-xl peer-checked:border-yellow-500 peer-checked:bg-yellow-500/5 transition-all group-hover:bg-gray-800/50">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="ghost" class="w-5 h-5 text-yellow-500"></i>
                                <span class="text-sm font-bold text-white">Modo Silencioso</span>
                            </div>
                            <p class="text-[10px] text-gray-500 leading-tight">Retorna HTTP 400 (Bad Request) sin dar detalles al atacante.</p>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="ips_policy" value="ids" class="peer sr-only" {{ cache('sentinel_ips_policy', 'block') == 'ids' ? 'checked' : '' }}>
                        <div class="p-4 bg-gray-900/50 border border-gray-800 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-500/5 transition-all group-hover:bg-gray-800/50">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="eye" class="w-5 h-5 text-blue-500"></i>
                                <span class="text-sm font-bold text-white">Modo IDS</span>
                            </div>
                            <p class="text-[10px] text-gray-500 leading-tight">Solo registra la amenaza en el log sin interrumpir la petición.</p>
                        </div>
                    </label>
                </div>
            </section>
        </div>

        <div class="p-6 bg-gray-900/50 border-t border-gray-800 flex justify-between items-center">
            <div class="text-[10px] text-gray-500 max-w-sm italic">
                * Las políticas se aplican mediante IPTables y el Gateway de Laravel de forma inmediata tras guardar.
            </div>
            <button type="submit" class="px-8 py-2.5 bg-green-600 hover:bg-green-500 text-white text-xs font-bold rounded-lg transition-all shadow-lg shadow-green-900/40 uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Aplicar Cambios
            </button>
        </div>
    </form>
</div>