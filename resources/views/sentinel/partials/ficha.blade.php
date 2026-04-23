<div id="ficha" class="tab-content max-w-5xl mx-auto glass-panel rounded-xl shadow-2xl overflow-hidden">
    <div class="p-6 border-b border-gray-700 bg-gray-800/50 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-white uppercase tracking-tight italic">Ficha de Recolección de Datos Experimentales</h2>
            <p class="text-xs text-gray-400 mt-1">Documento de control administrativo y técnico para el dataset[cite: 4, 5].</p>
        </div>
        <i data-lucide="clipboard-list" class="text-blue-500 w-8 h-8"></i>
    </div>
    
    <form action="{{ route('sentinel.store') }}" method="POST" class="p-6 space-y-8">
        @csrf
        
        <section>
            <div class="flex items-center gap-2 mb-4 border-b border-gray-800 pb-2">
                <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">1</span>
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest">Control Administrativo [cite: 5]</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Investigador [cite: 6]</label>
                    <input type="text" name="researcher_id" required placeholder="ID-Investigador" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:border-blue-500 outline-none">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Partición Dataset [cite: 7, 8]</label>
                    <select name="partition" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                        <option value="Entrenamiento">Entrenamiento</option>
                        <option value="Prueba">Prueba</option>
                        <option value="Validación Final">Validación Final</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Fecha y Hora de Captura [cite: 10, 11]</label>
                    <div class="flex gap-2">
                        <input type="date" name="eval_date" value="{{ date('Y-m-d') }}" class="w-1/2 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                        <input type="time" step="1" name="eval_time" value="{{ date('H:i:s') }}" class="w-1/2 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-center gap-2 mb-4 border-b border-gray-800 pb-2">
                <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">2</span>
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest">Contexto Técnico [cite: 12]</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Host de Origen (IP) [cite: 13]</label>
                    <input type="text" name="origin_host" placeholder="192.168.1.XX" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Endpoint Destino [cite: 13]</label>
                    <input type="text" name="endpoint" placeholder="/api/v1/update" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Protocolo / Hash OpenAPI [cite: 13]</label>
                    <input type="text" name="protocol_hash" placeholder="HTTP/1.1 - a8f5c2..." class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-center gap-2 mb-4 border-b border-gray-800 pb-2">
                <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">3</span>
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest">Mediciones (Matriz) [cite: 14]</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Método HTTP [cite: 15]</label>
                    <select name="http_method" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                        <option value="GET">1 (GET)</option>
                        <option value="POST">2 (POST)</option>
                        <option value="PUT">3 (PUT)</option>
                        <option value="DELETE">4 (DELETE)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Longitud (Bytes) [cite: 15]</label>
                    <input type="number" name="payload_length" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Chars Críticos [cite: 15]</label>
                    <input type="number" name="critical_chars" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Flag Consistencia [cite: 15]</label>
                    <select name="is_consistent" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                        <option value="1">[1] Cumple esquema</option>
                        <option value="0">[0] Inconsistente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Tasa Frecuencia [cite: 15]</label>
                    <input type="number" name="frequency_rate" placeholder="req/sec" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                </div>
            </div>
        </section>

        <section class="bg-gray-900/30 p-5 rounded-xl border border-gray-700/50">
            <div class="flex items-center gap-2 mb-4">
                <span class="bg-purple-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">4</span>
                <h3 class="text-xs font-bold text-purple-400 uppercase tracking-widest">Resultados y Validación [cite: 16]</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Clasificación Manual (Ground Truth) [cite: 17]</label>
                    <select name="ground_truth" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-2.5 text-sm text-white outline-none">
                        <option value="Legítimo">Legítimo</option>
                        <option value="Ataque SQLi">Ataque SQLi</option>
                        <option value="Ataque XSS">Ataque XSS</option>
                        <option value="Ataque BOLA">Ataque BOLA</option>
                    </select>
                </div>
                <div class="text-[11px] text-gray-500 leading-relaxed italic flex items-center bg-black/20 p-3 rounded-lg border border-white/5">
                    <i data-lucide="cpu" class="w-4 h-4 mr-2 text-purple-500"></i>
                    Al procesar, el algoritmo Random Forest generará la **Predicción**, **Probabilidad** y **Latencia** de forma automática según la matriz de variables[cite: 17].
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-[10px] font-medium text-gray-500 mb-2 uppercase">Notas Cualitativas [cite: 18, 19]</label>
                <textarea name="notes" rows="2" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-2 text-sm text-white outline-none" placeholder="Describa comportamientos anómalos o detalles de la carga útil..."></textarea>
            </div>
        </section>

        <div class="flex justify-end gap-4 pt-4 border-t border-gray-800">
            <button type="reset" class="px-6 py-2 text-xs text-gray-500 hover:text-white uppercase font-bold transition-colors">Limpiar Formulario</button>
            <button type="submit" class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-lg shadow-blue-900/40 uppercase tracking-widest transition-all">
                Registrar en Matriz y Evaluar
            </button>
        </div>
    </form>
</div>