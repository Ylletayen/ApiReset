<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('traffic_samples', function (Blueprint $table) {
            $table->id();
            // Control Administrativo
            $table->string('sample_id')->unique();
            $table->string('researcher_id');
            $table->string('partition');
            $table->date('eval_date')->nullable();
            $table->time('eval_time')->nullable();
            
            // Contexto Técnico (¡NUEVOS!)
            $table->string('origin_host')->nullable();
            $table->string('endpoint')->nullable();
            $table->string('protocol_hash')->nullable();
            
            // Mediciones Técnicas
            $table->string('http_method');
            $table->integer('payload_length');
            $table->integer('critical_chars');
            $table->boolean('is_consistent');
            $table->string('frequency_rate')->nullable();
            
            // Resultados del Diagnóstico
            $table->string('ground_truth');
            $table->string('prediction');
            $table->float('probability');
            $table->integer('latency_ms');
            $table->string('validation_result');
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('traffic_samples');
    }
};