<div id="auditoria" class="tab-content max-w-5xl mx-auto glass-panel rounded-xl shadow-2xl overflow-hidden">
    <div class="p-6 border-b border-gray-700 bg-gray-800/30">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <i data-lucide="search" class="text-blue-500 w-6 h-6"></i> Escáner de Auditoría Externa
        </h2>
    </div>

    <div class="p-8 space-y-6">
        <form id="auditForm" class="flex gap-4">
            @csrf
            <div class="relative flex-1">
                <i data-lucide="globe" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500"></i>
                <input type="url" id="target_url" name="target_url" required placeholder="https://ejemplo.com" 
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-sm text-white focus:border-blue-500 outline-none">
            </div>
            <button type="submit" id="btnAudit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-2">
                <i data-lucide="zap" id="iconAudit"></i>
                <span id="textAudit">Iniciar Auditoría</span>
            </button>
        </form>

        <div id="auditSummary" class="hidden p-3 bg-blue-500/10 border border-blue-500/30 rounded-lg flex items-center justify-between">
            <span id="summaryText" class="text-xs text-blue-300 font-mono"></span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-900 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Recurso</th>
                        <th class="px-4 py-3 text-center">Peso</th>
                        <th class="px-4 py-3 text-center">Símbolos</th>
                        <th class="px-4 py-3">Predicción IA</th>
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
    const urlInput = document.getElementById('target_url').value;

    // Estado: Cargando
    btn.disabled = true;
    text.innerText = 'Rastreando...';
    icon.classList.add('animate-spin');

    try {
        const response = await fetch("{{ route('sentinel.audit') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ target_url: urlInput })
        });

        const result = await response.json();

        if (result.success) {
            tbody.innerHTML = ''; // Limpiar tabla
            
            result.data.forEach(res => {
                const row = `
                    <tr class="hover:bg-gray-800/20 transition-colors">
                        <td class="px-4 py-3 font-mono text-blue-300">${res.path}</td>
                        <td class="px-4 py-3 text-center">${res.length}</td>
                        <td class="px-4 py-3 text-center ${res.chars > 5 ? 'text-red-400' : ''}">${res.chars}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded border ${res.prediction === 'Legítimo' ? 'border-green-500/30 text-green-400 bg-green-500/10' : 'border-red-500/30 text-red-400 bg-red-500/10'}">
                                ${res.prediction}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="w-full bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full ${res.prob > 0.8 ? 'bg-red-500' : 'bg-yellow-500'}" style="width: ${res.prob * 100}%"></div>
                            </div>
                        </td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            document.getElementById('auditSummary').classList.remove('hidden');
            document.getElementById('summaryText').innerText = `Resultados para: ${result.url} (${result.data.length} recursos)`;

        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Fallo crítico en el escáner.');
    } finally {
        // Restaurar botón
        btn.disabled = false;
        text.innerText = 'Iniciar Auditoría';
        icon.classList.remove('animate-spin');
    }
});
</script>