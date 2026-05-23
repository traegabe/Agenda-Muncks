<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Users: ' . App\Models\User::count() . PHP_EOL;
foreach (App\Models\User::all() as $u) {
    echo $u->name . ' - ' . $u->email . PHP_EOL;
}
echo PHP_EOL;

echo 'Veiculos: ' . App\Models\Agenda\Veiculo::count() . PHP_EOL;
foreach (App\Models\Agenda\Veiculo::all() as $v) {
    echo $v->placa . ' - ' . $v->modelo . PHP_EOL;
}
echo PHP_EOL;

echo 'All good!' . PHP_EOL;
