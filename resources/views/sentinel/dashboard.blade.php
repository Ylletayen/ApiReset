@extends('layouts.app')

@section('content')

    {{-- Notificación de éxito --}}
    @if(session('success'))
        <div class="mb-4 bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    {{-- NUEVO: Notificación de Errores --}}
    @if($errors->any())
        <div class="mb-4 bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded relative">
            <div class="flex items-center gap-2 mb-1 font-bold">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                Se encontraron problemas:
            </div>
            <ul class="list-disc pl-8 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <header class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-800 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i data-lucide="shield-check" class="text-blue-500 w-7 h-7"></i>
                RF-API Sentinel <span class="text-xs bg-blue-500/20 text-blue-400 px-2 py-1 rounded ml-2">v2.0 (2026)</span>
            </h1>
            <p class="text-gray-400 text-sm mt-1">
                Diagnóstico de Amenazas mediante Random Forest & Consistencia OpenAPI
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="switchTab('monitor')" id="btn-monitor" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-blue-600 text-white flex items-center gap-2">
                <i data-lucide="activity" class="w-4 h-4"></i> Monitor
            </button>
            <button onclick="switchTab('ficha')" id="btn-ficha" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:bg-gray-700 flex items-center gap-2">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Ficha Experimental
            </button>
            <button onclick="switchTab('sistema')" id="btn-sistema" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:bg-gray-700 flex items-center gap-2">
                <i data-lucide="server" class="w-4 h-4"></i> Producción
            </button>
            <button onclick="switchTab('entrenamiento')" id="btn-entrenamiento" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:bg-gray-700 flex items-center gap-2">
                <i data-lucide="brain-circuit" class="w-4 h-4"></i> Entrenamiento
            </button>
        </div>
    </header>

    {{-- KPIs Dinámicos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach([
            ['Eficacia (F1-Score)', $stats['f1_score'], 'blue', 'activity', 'Balance global'],
            ['Precisión', $stats['precision'], 'green', 'shield-check', 'Positivos correctos'],
            ['Sensibilidad (Recall)', $stats['recall'], 'orange', 'shield-alert', 'Detección de amenazas'],
            ['Latencia Promedio', $stats['avg_latency'], 'purple', 'clock', 'Tiempo de inferencia']
        ] as $item)
        <div class="glass-panel p-4 rounded-xl flex items-center space-x-4 border-l-4 border-{{ $item[2] }}-500">
            <div class="p-3 rounded-lg bg-{{ $item[2] }}-500/20 text-{{ $item[2] }}-400">
                <i data-lucide="{{ $item[3] }}" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-gray-400 text-sm font-medium">{{ $item[0] }}</h3>
                <p class="text-2xl font-bold text-white">{{ $item[1] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $item[4] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- TAB 1: MONITOR --}}
    <div id="monitor" class="tab-content active">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 glass-panel rounded-xl overflow-hidden shadow-2xl">
                <div class="p-4 border-b border-gray-700 bg-gray-800/30">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <i data-lucide="terminal" class="text-gray-400 w-5 h-5"></i> Tráfico Real (Random Forest)
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-900/80 text-gray-400 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">ID Muestra</th>
                                <th class="px-4 py-3">Método</th>
                                <th class="px-4 py-3">Longitud</th>
                                <th class="px-4 py-3">Chars Críticos</th>
                                <th class="px-4 py-3">OpenAPI</th>
                                <th class="px-4 py-3">Predicción RF</th>
                                <th class="px-4 py-3">Certeza</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($samples as $sample)
                            <tr class="hover:bg-gray-800/40">
                                <td class="px-4 py-3 font-mono text-blue-400">{{ $sample->sample_id }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $sample->http_method == 'POST' ? 'text-green-400' : 'text-blue-400' }} font-bold">
                                        {{ $sample->http_method }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $sample->payload_length }} Bytes</td>
                                <td class="px-4 py-3 {{ $sample->critical_chars > 0 ? 'text-red-400' : 'text-gray-400' }}">
                                    {{ $sample->critical_chars }}
                                </td>
                                <td class="px-4 py-3 {{ $sample->is_consistent ? 'text-green-400' : 'text-red-400' }} font-medium">
                                    {{ $sample->is_consistent ? '✓ Válida' : '✗ Inválida' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs border border-red-500/30">
                                        {{ $sample->prediction }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-300">{{ number_format($sample->probability * 100, 0) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Sidebar estático por ahora --}}
            <div class="glass-panel rounded-xl p-5 shadow-2xl">
                <h2 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <i data-lucide="pie-chart" class="text-blue-400 w-5 h-5"></i> Clasificación de Amenazas
                </h2>
                <div class="space-y-6 text-xs">
                    <p class="text-gray-400 leading-relaxed italic">
                        Análisis basado en {{ $samples->count() }} muestras recolectadas experimentalmente. 
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('sentinel.partials.ficha')
    @include('sentinel.partials.produccion')
    @include('sentinel.partials.entrenamiento')

@endsection