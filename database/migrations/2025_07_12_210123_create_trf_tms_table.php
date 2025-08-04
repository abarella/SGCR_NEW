<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trf_tms', function (Blueprint $table) {
            $table->id();
            $table->date('data_transferencia');
            $table->string('material');
            $table->decimal('quantidade', 10, 2);
            $table->string('unidade');
            $table->string('origem');
            $table->string('destino');
            $table->enum('status', ['pendente', 'concluida', 'cancelada'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('responsavel')->nullable();
            $table->unsignedBigInteger('concluido_por')->nullable();
            $table->datetime('data_conclusao')->nullable();
            $table->unsignedBigInteger('cancelado_por')->nullable();
            $table->datetime('data_cancelamento')->nullable();
            $table->text('motivo_cancelamento')->nullable();
            $table->timestamps();

            $table->foreign('responsavel')->references('id')->on('users')->onDelete('no action');
            $table->foreign('concluido_por')->references('id')->on('users')->onDelete('no action');
            $table->foreign('cancelado_por')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trf_tms');
    }
};
