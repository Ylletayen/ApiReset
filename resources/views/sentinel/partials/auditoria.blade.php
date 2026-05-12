<div id="auditoria" class="tab-content max-w-5xl mx-auto glass-panel rounded-xl shadow-2xl overflow-hidden">
    <div class="p-6 border-b border-gray-700 bg-gray-800/30 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i data-lucide="search" class="text-blue-500 w-6 h-6"></i> Escáner de Auditoría Externa
            </h2>
            <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-wider">Análisis de archivos públicos y endpoints mediante IA.</p>
        </div>
    </div>

    <div class="p-8 space-y-6">
        <form id="auditForm" class="flex flex-col gap-4">
            <div class="flex gap-4">
                <div class="relative flex-1">
                    <i data-lucide="globe" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500"></i>
                    <input type="url" id="target_url" name="target_url" required placeholder="https://ejemplo.com" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-sm text-white focus:border-blue-500 outline-none">
                </div>
                <button type="submit" id="btnAudit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-2">
                    <i data-lucide="zap" id="iconAudit"></i>
                    <span id="textAudit">Iniciar Auditoría</span>
                </button>
            </div>
            
            <label class="flex items-center gap-2 cursor-pointer w-fit p-2 hover:bg-gray-800/50 rounded-lg transition-colors">
                <input type="checkbox" id="beta_run" name="beta_run" class="w-4 h-4 rounded bg-gray-900 border-gray-700 text-blue-500 focus:ring-blue-500">
                <span class="text-xs text-blue-300 font-medium">Ejecutar Corrida Beta (Fuzzing Pasivo de Vulnerabilidades)</span>
                <i data-lucide="shield-alert" class="w-4 h-4 text-yellow-500 ml-1" title="Genera mutaciones de ataque internamente sin enviarlas al servidor destino."></i>
            </label>
        </form>

        <div class="p-4 bg-gray-900/80 border border-gray-700 rounded-lg">
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                <i data-lucide="book-open" class="w-4 h-4"></i> Glosario de Resultados
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 text-xs text-gray-400">
                <p><strong class="text-blue-400 block mb-1">Recurso / Archivo</strong> Endpoint descubierto. <span class="text-purple-400 block mt-1 text-[10px]">[BETA] = Mutación Fuzzer.</span></p>
                <p><strong class="text-blue-400 block mb-1">Peso (B) / Símbolos</strong> Longitud y caracteres peligrosos.</p>
                <p><strong class="text-blue-400 block mb-1">Vulnerabilidad API</strong> Vector de ataque (OWASP) al que este recurso es débil por su estructura.</p>
                <p><strong class="text-blue-400 block mb-1">Predicción IA</strong> Diagnóstico del algoritmo.</p>
                <p><strong class="text-blue-400 block mb-1">Riesgo</strong> Certeza matemática (>80% es crítico).</p>
            </div>
        </div>

        <div id="auditSummary" class="hidden p-3 bg-blue-500/10 border border-blue-500/30 rounded-lg flex items-center justify-between">
            <span id="summaryText" class="text-xs text-blue-300 font-mono"></span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-900 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Recurso / Archivo</th>
                        <th class="px-4 py-3 text-center">Peso</th>
                        <th class="px-4 py-3 text-center">Símbolos</th>
                        <th class="px-4 py-3">Vulnerabilidad API</th> <th class="px-4 py-3">Predicción IA</th>
                        <th class="px-4 py-3">Riesgo</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody" class="divide-y divide-gray-800 text-gray-400">
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center italic">Ingrese una URL para iniciar.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('auditForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnAudit');
    const icon = document.getElementById('iconAudit');
    const text = document.getElementById('textAudit');
    const tbody = document.getElementById('auditTableBody');
    const summary = document.getElementById('auditSummary');
    const summaryText = document.getElementById('summaryText');
    const urlInput = document.getElementById('target_url').value;
    const isBeta = document.getElementById('beta_run').checked;

    // Estado: Cargando
    btn.disabled = true;
    text.innerText = isBeta ? 'Ejecutando Fuzzing...' : 'Rastreando...';
    icon.classList.add('animate-spin');
    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-10 text-center animate-pulse">Analizando recursos con Random Forest...</td></tr>';

    try {
        const response = await fetch("{{ route('sentinel.audit') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ target_url: urlInput, beta_run: isBeta })
        });

        const result = await response.json();
        console.log("Datos recibidos:", result); // DEPURACIÓN

        if (result.success && result.data) {
            tbody.innerHTML = ''; 
            
            result.data.forEach(res => {
                const betaTag = res.is_simulated ? '<span class="ml-2 px-1 py-0.5 bg-purple-500/20 text-purple-400 text-[8px] rounded border border-purple-500/30">BETA</span>' : '';
                
                const row = `
                    <tr class="hover:bg-gray-800/20 transition-colors border-b border-gray-800">
                        <td class="px-4 py-3 font-mono text-blue-300 break-all text-[10px]">${res.path || 'Unknown'} ${betaTag}</td>
                        <td class="px-4 py-3 text-center text-gray-500">${res.length || 0}</td>
                        <td class="px-4 py-3 text-center ${res.chars > 5 ? 'text-red-400 font-bold' : 'text-gray-500'}">${res.chars || 0}</td>
                        <td class="px-4 py-3 text-[10px] text-gray-400 uppercase tracking-tighter">${res.vuln_type || 'N/A'}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] border ${res.prediction === 'Legítimo' ? 'border-green-500/30 text-green-400 bg-green-500/10' : 'border-red-500/30 text-red-400 bg-red-500/10'}">
                                ${res.prediction || 'Pendiente'}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="w-full bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full ${res.prob > 0.8 ? 'bg-red-500' : 'bg-yellow-500'}" style="width: ${(res.prob || 0) * 100}%"></div>
                            </div>
                            <div class="text-[8px] text-right mt-1 text-gray-600">${((res.prob || 0) * 100).toFixed(1)}%</div>
                        </td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            summary.classList.remove('hidden');
            summaryText.innerText = `Análisis completado para: ${result.url} (${result.data.length} recursos encontrados)`;

        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-10 text-center text-red-400">Error: ${result.message}</td></tr>`;
        }
    } catch (error) {
        console.error(error);
        tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-10 text-center text-red-400">Fallo en la comunicación con el servidor.</td></tr>';
    } finally {
        btn.disabled = false;
        text.innerText = 'Iniciar Auditoría';
        icon.classList.remove('animate-spin');
    }
});
</script>