<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Executar migração
$output = $kernel->call('migrate');

echo "Migration output: " . $output . "\n"; 