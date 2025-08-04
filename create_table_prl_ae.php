<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    Schema::create('prl_aes', function (Blueprint $table) {
        $table->id();
        $table->date('data_alteracao');
        $table->string('produto');
        $table->string('lote');
        $table->decimal('quantidade_atual', 10, 2);
        $table->decimal('quantidade_nova', 10, 2);
        $table->string('unidade');
        $table->enum('tipo_alteracao', ['entrada', 'saida', 'ajuste'])->default('ajuste');
        $table->text('motivo');
        $table->text('observacoes')->nullable();
        $table->unsignedBigInteger('responsavel')->nullable();
        $table->unsignedBigInteger('aprovado_por')->nullable();
        $table->datetime('data_aprovacao')->nullable();
        $table->unsignedBigInteger('rejeitado_por')->nullable();
        $table->datetime('data_rejeicao')->nullable();
        $table->text('motivo_rejeicao')->nullable();
        $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
        $table->timestamps();
    });
    
    echo "Tabela prl_aes criada com sucesso!\n";
} catch (Exception $e) {
    echo "Erro ao criar tabela: " . $e->getMessage() . "\n";
} 