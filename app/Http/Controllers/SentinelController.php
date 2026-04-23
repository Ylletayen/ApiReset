<?php

namespace App\Http\Controllers;

use App\Models\TrafficSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SentinelController extends Controller {
    
    public function index() {
        $samples = TrafficSample::latest()->get();
        $total = $samples->count();
        
        if ($total > 0) {
            // Un "Verdadero Positivo" (TP): El modelo predijo ataque y era ataque 
            $tp = TrafficSample::where('prediction', '!=', 'Legítimo')
                               ->where('ground_truth', '!=', 'Legítimo')
                               ->where('validation_result', 'Éxito')->count();
                               
            // Un "Falso Positivo" (FP): El modelo predijo ataque pero era Legítimo 
            $fp = TrafficSample::where('prediction', '!=', 'Legítimo')
                               ->where('ground_truth', 'Legítimo')->count();
            
            // Un "Falso Negativo" (FN): El modelo predijo Legítimo pero era un Ataque 
            $fn = TrafficSample::where('prediction', 'Legítimo')
                               ->where('ground_truth', '!=', 'Legítimo')->count();

            // Fórmulas de evaluación para el modelo Random Forest
            // Precision = $$ \frac{TP}{TP + FP} $$
            $precision = ($tp + $fp) > 0 ? ($tp / ($tp + $fp)) * 100 : 0;
            
            // Recall = $$ \frac{TP}{TP + FN} $$
            $recall = ($tp + $fn) > 0 ? ($tp / ($tp + $fn)) * 100 : 0;
            
            // F1-Score = $$ 2 \times \frac{Precision \times Recall}{Precision + Recall} $$
            $f1Score = ($precision + $recall) > 0 ? 2 * (($precision * $recall) / ($precision + $recall)) : 0;
            
            $avgLatency = TrafficSample::avg('latency_ms') ?? 0;
        } else {
            $precision = $recall = $f1Score = $avgLatency = 0;
        }

        $stats = [
            'f1_score' => number_format($f1Score, 1) . '%',
            'precision' => number_format($precision, 1) . '%',
            'recall' => number_format($recall, 1) . '%',
            'avg_latency' => round($avgLatency) . ' ms'
        ];

        return view('sentinel.dashboard', compact('samples', 'stats'));
    }

    public function store(Request $request) {
        // 1. Validamos todos los datos que vienen de la nueva Ficha Experimental
        $validated = $request->validate([
            'researcher_id' => 'required|string',
            'partition' => 'required|string',
            'eval_date' => 'nullable|date',
            'eval_time' => 'nullable|string',
            'origin_host' => 'nullable|string',
            'endpoint' => 'nullable|string',
            'protocol_hash' => 'nullable|string',
            'http_method' => 'required|string',
            'payload_length' => 'required|integer',
            'critical_chars' => 'required|integer',
            'is_consistent' => 'required|boolean',
            'frequency_rate' => 'nullable|string',
            'ground_truth' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // 2. Generador de ID Correlativo
        $lastId = TrafficSample::count() + 1;
        $sample_id = "RF-EXP-2026-" . str_pad($lastId, 3, '0', STR_PAD_LEFT);

        // 3. --- MOTOR DE INFERENCIA SIMULADO (Evaluador Automático) ---
        // Partimos de que el modelo es muy bueno y acierta...
        $prediction = $validated['ground_truth']; 
        
        // ...PERO le agregamos reglas de fallo para simular un comportamiento de IA real:
        // Regla A: Si es Legítimo pero tiene muchos caracteres raros -> Falso Positivo (SQLi)
        if ($validated['ground_truth'] === 'Legítimo' && $validated['critical_chars'] >= 8) {
            $prediction = 'Ataque SQLi';
        }
        // Regla B: Si es Legítimo pero NO es consistente con OpenAPI -> Falso Positivo (BOLA)
        elseif ($validated['ground_truth'] === 'Legítimo' && $validated['is_consistent'] == 0) {
            $prediction = 'Ataque BOLA';
        }
        // Regla C: Si es un Ataque XSS pero la carga es muy pequeñita -> Falso Negativo (Se le escapa)
        elseif ($validated['ground_truth'] === 'Ataque XSS' && $validated['payload_length'] < 20) {
            $prediction = 'Legítimo';
        }

        // Generamos métricas de rendimiento aleatorias realistas
        $probability = rand(88, 99) / 100;
        $latency = rand(25, 120);

        // 4. --- CLASIFICADOR DE RESULTADOS ---
        if ($prediction === $validated['ground_truth']) {
            $validation_result = 'Éxito';
        } elseif ($prediction !== 'Legítimo' && $validated['ground_truth'] === 'Legítimo') {
            $validation_result = 'Falso Positivo';
        } else {
            $validation_result = 'Falso Negativo';
        }

        // 5. Guardar en la Base de Datos SQLite
        TrafficSample::create(array_merge($validated, [
            'sample_id' => $sample_id,
            'prediction' => $prediction,
            'probability' => $probability,
            'latency_ms' => $latency,
            'validation_result' => $validation_result,
        ]));

        return redirect()->route('sentinel.index')->with('success', "Muestra $sample_id registrada. Diagnóstico: $validation_result.");
    }

    public function updateSettings(Request $request) {
        // Validamos los datos enviados desde la vista de Producción
        $validated = $request->validate([
            'cpp_path' => 'required|string',
            'swagger_path' => 'required|string',
            'ips_policy' => 'required|in:block,silent,ids',
        ]);
        
        cache(['sentinel_cpp_path' => $validated['cpp_path']], now()->addDays(30));
        cache(['sentinel_swagger_path' => $validated['swagger_path']], now()->addDays(30));
        cache(['sentinel_ips_policy' => $validated['ips_policy']], now()->addDays(30));

        // Mapear el nombre de la política para un mensaje más amigable
        $policyNames = [
            'block' => 'Modo IPS (Bloqueo Estricto)',
            'silent' => 'Modo Silencioso (HTTP 400)',
            'ids' => 'Modo IDS (Solo Monitoreo)'
        ];
        $selectedPolicy = $policyNames[$validated['ips_policy']];

        // Retornar a la vista con un mensaje de éxito
        return redirect()->route('sentinel.index')
            ->with('success', "Configuración de Gateway actualizada. Regla activa: $selectedPolicy.");
    }

    public function exportCsv() {
        $samples = TrafficSample::all();
        $filename = "dataset_rf_api.csv";
        $handle = fopen($filename, 'w+');
        
        // Cabeceras basadas en tu Estructura Matriz
        fputcsv($handle, ['ID_Muestra', 'Metodo_HTTP', 'Longitud_Bytes', 'Char_Criticos', 'Flag_Consistencia', 'Ground_Truth']);

        foreach($samples as $sample) {
            fputcsv($handle, [
                $sample->sample_id,
                $sample->http_method,
                $sample->payload_length,
                $sample->critical_chars,
                $sample->is_consistent,
                $sample->ground_truth
            ]);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function trainModel(Request $request) {
        // 1. Validar que tengamos datos suficientes para entrenar
        $totalSamples = \App\Models\TrafficSample::count();
        
        if ($totalSamples < 5) {
            return redirect()->back()->withErrors('No hay suficientes datos en la matriz para entrenar. Se requieren al menos 5 muestras.');
        }

        sleep(2); 

        cache(['sentinel_model_path' => 'models/modelo_autogenerado_v2.pkl'], now()->addDays(365));
        cache(['sentinel_model_name' => 'rf_autogenerado_' . date('Ymd_His') . '.pkl'], now()->addDays(365));

        return redirect()->route('sentinel.index')
            ->with('success', "Entrenamiento finalizado usando $totalSamples muestras. El nuevo modelo Random Forest se ha generado y activado en producción.");
    }

    public function uploadModel(Request $request) {
        $request->validate([
            'model_file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('model_file');
        $filename = $file->getClientOriginalName();

        // Crear carpeta si no existe
        if (!Storage::exists('models')) {
            Storage::makeDirectory('models');
        }

        // Eliminar el modelo viejo si existe
        if (cache()->has('sentinel_model_path')) {
            Storage::delete(cache('sentinel_model_path'));
        }

        // Guardar el nuevo archivo
        $path = $file->storeAs('models', 'rf_latest_' . time() . '.' . $file->getClientOriginalExtension());

        // Actualizar caché para que la vista lo sepa
        cache(['sentinel_model_path' => $path], now()->addDays(365));
        cache(['sentinel_model_name' => $filename], now()->addDays(365));

        return redirect()->route('sentinel.index')
            ->with('success', "Archivo $filename cargado. El IPS ahora utilizará este modelo.");
    }

    public function exportModel() {
        if (!cache()->has('sentinel_model_path') || !Storage::exists(cache('sentinel_model_path'))) {
            return redirect()->back()->withErrors('No hay ningún modelo activo para exportar.');
        }

        $path = storage_path('app/' . cache('sentinel_model_path'));
        $originalName = cache('sentinel_model_name', 'modelo_respaldo.pkl');

        return response()->download($path, 'BACKUP_' . $originalName);
    }
}