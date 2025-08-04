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
        Schema::create('prl_fps', function (Blueprint $table) {
            $table->id();
            $table->date('data_producao');
            $table->string('produto');
            $table->string('lote');
            $table->decimal('quantidade', 10, 2);
            $table->string('unidade');
            $table->enum('status', ['pendente', 'em_producao', 'concluido', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('responsavel')->nullable();
            $table->datetime('data_inicio')->nullable();
            $table->datetime('data_fim')->nullable();
            $table->decimal('temperatura', 5, 2)->nullable();
            $table->decimal('umidade', 5, 2)->nullable();
            $table->decimal('ph', 3, 2)->nullable();
            $table->text('condicoes_especiais')->nullable();
            $table->unsignedBigInteger('aprovado_por')->nullable();
            $table->datetime('data_aprovacao')->nullable();
            $table->unsignedBigInteger('rejeitado_por')->nullable();
            $table->datetime('data_rejeicao')->nullable();
            $table->text('motivo_rejeicao')->nullable();
            $table->timestamps();

            $table->foreign('responsavel')->references('id')->on('users')->onDelete('no action');
            $table->foreign('aprovado_por')->references('id')->on('users')->onDelete('no action');
            $table->foreign('rejeitado_por')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prl_fps');
    }
};
