<div id="entrenamiento" class="tab-content max-w-5xl mx-auto glass-panel rounded-xl shadow-2xl">
    <div class="p-6 border-b border-gray-700 flex items-center justify-between bg-gray-800/30">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i data-lucide="brain-circuit" class="text-purple-500 w-6 h-6"></i> 
                Laboratorio de Entrenamiento (ML Ops)
            </h2>
            <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-wider">Gestión del Dataset y Archivos del Modelo Random Forest.</p>
        </div>
        <span class="bg-purple-500/20 text-purple-400 px-3 py-1 rounded-full text-xs border border-purple-500/30 font-mono">
            V.2.0-Ready
        </span>
    </div>

    <div class="p-8 space-y-8">
        
        <section class="bg-gray-900/50 p-6 rounded-xl border border-gray-800">
            <h3 class="text-sm font-bold text-blue-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                <i data-lucide="file-code-2" class="w-4 h-4"></i> Archivo del Modelo
            </h3>
            
            <div class="flex flex-col md:flex-row gap-6 items-center">
                <div class="flex-1">
                    @if(cache()->has('sentinel_model_path'))
                        <div class="p-3 bg-green-500/10 border border-green-500/30 rounded-lg flex items-center gap-3">
                            <i data-lucide="check-circle" class="text-green-500 w-5 h-5"></i>
                            <div>
                                <p class="text-xs text-green-400 font-bold uppercase">Modelo Cargado y Activo</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ cache('sentinel_model_name', 'modelo_rf.pkl') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg flex items-center gap-3">
                            <i data-lucide="alert-triangle" class="text-yellow-500 w-5 h-5"></i>
                            <div>
                                <p class="text-xs text-yellow-400 font-bold uppercase">Sin modelo en producción</p>
                                <p class="text-[10px] text-gray-400">Sube el archivo compilado (.pkl, .so) para activar el IPS.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex-1 w-full">
                    <form id="uploadForm" action="{{ route('sentinel.model.upload') }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                        @csrf
                        <input type="file" name="model_file" id="modelFileInput" required class="block w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 bg-gray-800 border border-gray-700 rounded-lg cursor-pointer">
                        <button type="button" onclick="checkBeforeUpload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors shadow-lg shadow-blue-900/40">
                            Subir
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="bg-black/20 p-6 rounded-xl border border-purple-500/20">
            <h3 class="text-sm font-bold text-purple-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                <i data-lucide="cpu" class="w-4 h-4"></i> Reentrenamiento Automático
            </h3>
            <p class="text-xs text-gray-400 mb-6 leading-relaxed">
                Ejecuta el script en segundo plano para generar un nuevo modelo basado en las <strong>{{ $samples->count() }} muestras</strong> recolectadas actualmente.
            </p>
            
            <form id="trainForm" action="{{ route('sentinel.train') }}" method="POST" class="flex justify-between items-center border-t border-gray-800 pt-6" onsubmit="startTrainingAnimation(event)">
                @csrf
                <div class="space-y-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hyper_tuning" class="rounded bg-gray-900 border-gray-700 text-purple-500 focus:ring-purple-500">
                        <span class="text-xs text-gray-300">Aplicar Hyperparameter Tuning (Requiere más tiempo)</span>
                    </label>
                </div>
                <button type="submit" id="btnTrain" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-all shadow-[0_0_15px_rgba(147,51,234,0.3)] uppercase tracking-widest flex items-center gap-2 min-w-[220px] justify-center">
                    <i data-lucide="play" id="iconTrain" class="w-4 h-4"></i> 
                    <span id="textTrain">Iniciar Entrenamiento</span>
                </button>
            </form>
        </section>
    </div>
</div>

<div id="overwriteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/80 backdrop-blur-sm">
    <div class="bg-gray-800 border border-red-500/50 rounded-xl max-w-md w-full p-6 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="modalContent">
        <div class="flex items-center gap-3 mb-4 text-red-400">
            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            <h3 class="text-lg font-bold">¡Advertencia de Sobrescritura!</h3>
        </div>
        <p class="text-sm text-gray-300 mb-6 leading-relaxed">
            Ya existe un modelo Random Forest activo en producción. Si subes este nuevo archivo, el anterior será eliminado permanentemente. ¿Deseas hacer una copia de seguridad antes de continuar?
        </p>
        <div class="flex flex-col gap-3">
            <a href="{{ route('sentinel.model.export') }}" class="w-full text-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-bold rounded-lg transition-colors border border-gray-600 flex items-center justify-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i> Respaldar Modelo Actual
            </a>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 px-4 py-2 text-gray-400 hover:text-white text-sm font-bold transition-colors">
                    Cancelar
                </button>
                <button onclick="forceUpload()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors shadow-lg shadow-red-900/40">
                    Sobrescribir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables de estado inyectadas desde Laravel
    const hasExistingModel = {{ cache()->has('sentinel_model_path') ? 'true' : 'false' }};

    function checkBeforeUpload() {
        const fileInput = document.getElementById('modelFileInput');
        if (!fileInput.value) {
            alert('Por favor selecciona un archivo primero.');
            return;
        }

        if (hasExistingModel) {
            // Mostrar Modal
            const modal = document.getElementById('overwriteModal');
            const modalContent = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            // Subir directo si no hay modelo
            forceUpload();
        }
    }

    function closeModal() {
        const modal = document.getElementById('overwriteModal');
        const modalContent = document.getElementById('modalContent');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function forceUpload() {
        document.getElementById('uploadForm').submit();
    }

    // Animación de Entrenamiento en Segundo Plano
    function startTrainingAnimation(e) {
        // No prevenimos el submit (e.preventDefault()), dejamos que Laravel procese,
        // pero cambiamos la interfaz inmediatamente para dar feedback al usuario.
        
        const btn = document.getElementById('btnTrain');
        const icon = document.getElementById('iconTrain');
        const text = document.getElementById('textTrain');

        // Deshabilitar botón para evitar doble clic
        btn.disabled = true;
        btn.classList.remove('hover:bg-purple-700');
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        // Cambiar texto e icono (simulando spinner)
        text.innerText = 'Trabajando en 2do Plano...';
        icon.setAttribute('data-lucide', 'loader');
        icon.classList.add('animate-spin');
        lucide.createIcons(); // Refrescar icono
    }
</script>