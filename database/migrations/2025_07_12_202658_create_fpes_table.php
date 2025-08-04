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
        Schema::create('fpes', function (Blueprint $table) {
            $table->id();
            $table->date('data_embalagem');
            $table->string('produto');
            $table->string('lote');
            $table->decimal('quantidade', 10, 2);
            $table->string('unidade');
            $table->enum('status', ['pendente', 'embalado', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('responsavel')->nullable();
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
        Schema::dropIfExists('fpes');
    }
};
