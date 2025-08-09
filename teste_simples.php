<?php

echo "=== TESTE SIMPLES LOTE  ===\n";

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$service = new App\Services\SGFPService();
$result = $service->verificarLoteExiste('320IP0602');

echo "Lote testado: 320IP0602\n";
echo "Atividade: " . $result['atividade'] . "\n";
echo "Data Produção: " . ($result['data_producao'] ?: 'Vazia') . "\n";
echo "Data Validade: " . ($result['data_validade'] ?: 'Vazia') . "\n";
echo "Concentração: " . $result['concentracao'] . "\n";
echo "Volume: " . $result['volume'] . "\n";
echo "Atividade Específica: " . $result['atividade_especifica'] . "\n";

echo "\nCONCLUSÃO:\n";
if ($result['atividade'] == '0' && empty($result['data_producao'])) {
    echo "❌ Lote '320IP0602' NÃO encontrado no banco ou sem dados\n";
} else {
    echo "✅ Lote '320IP0602' encontrado com dados!\n";
}

echo "\n=== INSTRUÇÕES DE USO ===\n";
echo "Para testar outros lotes, edite a linha:\n";
echo "\$result = \$service->verificarLoteExiste('SEU_LOTE_AQUI');\n";
echo "\nPara executar: php teste_simples.php\n";