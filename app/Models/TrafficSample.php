<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficSample extends Model
{
    use HasFactory;

    protected $table = 'traffic_samples';

    protected $fillable = [
        'sample_id',         // Código correlativo (Ej: RF-EXP-2026-001) [cite: 9]
        'researcher_id',     // ID del investigador responsable [cite: 6]
        'partition',         // Entrenamiento, Prueba o Validación [cite: 7, 8]
        
        'http_method',       // GET, POST, PUT, DELETE [cite: 1, 15]
        'payload_length',    // Longitud de carga en Bytes [cite: 2, 15]
        'critical_chars',    // Conteo de caracteres críticos (, ;, <, >, etc.) [cite: 2, 15]
        'is_consistent',     // Flag de consistencia con OpenAPI (1 o 0) [cite: 13, 15]
        
        'ground_truth',      // Clasificación manual (Etiqueta real) 
        'prediction',        // Predicción del modelo Random Forest 
        'probability',       // Probabilidad de certeza (0 a 1) 
        'latency_ms',        // Latencia de inferencia en milisegundos 
        'validation_result', // Éxito, Falso Positivo o Falso Negativo 
        
        'notes',  

        'sample_id', 'researcher_id', 'partition', 'eval_date', 'eval_time',
        'origin_host', 'endpoint', 'protocol_hash',
        'http_method', 'payload_length', 'critical_chars', 'is_consistent', 'frequency_rate',
        'ground_truth', 'prediction', 'probability', 'latency_ms', 'validation_result', 'notes'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Optimiza el manejo de datos booleanos y numéricos de la matriz.
     */
    protected $casts = [
        'is_consistent' => 'boolean',
        'payload_length' => 'integer',
        'critical_chars' => 'integer',
        'probability' => 'float',
        'latency_ms' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}