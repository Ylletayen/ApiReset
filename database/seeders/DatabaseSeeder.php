<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrafficSample;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiamos la tabla por si acaso
        TrafficSample::truncate();

        $muestras = [
            [
                'sample_id' => 'RF-EXP-2026-001', 'researcher_id' => 'ID-01', 'partition' => 'Entrenamiento',
                'http_method' => 'GET', 'payload_length' => 0, 'critical_chars' => 0, 'is_consistent' => 1,
                'ground_truth' => 'Legítimo', 'prediction' => 'Legítimo', 'probability' => 0.98, 'latency_ms' => 45, 'validation_result' => 'Éxito'
            ],
            [
                'sample_id' => 'RF-EXP-2026-002', 'researcher_id' => 'ID-01', 'partition' => 'Entrenamiento',
                'http_method' => 'POST', 'payload_length' => 450, 'critical_chars' => 15, 'is_consistent' => 1,
                'ground_truth' => 'Ataque SQLi', 'prediction' => 'Ataque SQLi', 'probability' => 0.95, 'latency_ms' => 120, 'validation_result' => 'Éxito'
            ],
            [
                'sample_id' => 'RF-EXP-2026-003', 'researcher_id' => 'ID-01', 'partition' => 'Entrenamiento',
                'http_method' => 'GET', 'payload_length' => 12, 'critical_chars' => 0, 'is_consistent' => 0,
                'ground_truth' => 'Ataque BOLA', 'prediction' => 'Ataque BOLA', 'probability' => 0.89, 'latency_ms' => 85, 'validation_result' => 'Éxito'
            ],
            [
                'sample_id' => 'RF-EXP-2026-004', 'researcher_id' => 'ID-01', 'partition' => 'Entrenamiento',
                'http_method' => 'POST', 'payload_length' => 80, 'critical_chars' => 8, 'is_consistent' => 1,
                'ground_truth' => 'Ataque XSS', 'prediction' => 'Ataque XSS', 'probability' => 0.91, 'latency_ms' => 95, 'validation_result' => 'Éxito'
            ],
            [
                'sample_id' => 'RF-EXP-2026-005', 'researcher_id' => 'ID-01', 'partition' => 'Prueba',
                'http_method' => 'PUT', 'payload_length' => 1250, 'critical_chars' => 8, 'is_consistent' => 0,
                'ground_truth' => 'Ataque SQLi', 'prediction' => 'Legítimo', 'probability' => 0.65, 'latency_ms' => 110, 'validation_result' => 'Falso Negativo'
            ],
        ];

        foreach ($muestras as $muestra) {
            TrafficSample::create($muestra);
        }
    }
}