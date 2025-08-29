<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "-- resumen general --\n";
$summary = DB::select("SELECT count(*) as total, min(created_at) as mn, max(created_at) as mx FROM pedidos");
print_r($summary[0]);

echo "\n-- top 20 grupos (id_usuario, estado, fecha) por cnt --\n";
$groups = DB::select("SELECT id_usuario, estado, to_char(fecha_pedido,'YYYY-MM-DD') as fecha, count(*) as cnt FROM pedidos GROUP BY id_usuario, estado, to_char(fecha_pedido,'YYYY-MM-DD') ORDER BY cnt DESC LIMIT 20");
foreach ($groups as $g) {
    print_r((array)$g);
}

echo "\n-- cuentas por estado --\n";
$byState = DB::select("SELECT estado, count(*) as cnt FROM pedidos GROUP BY estado ORDER BY cnt DESC");
foreach ($byState as $s) {
    print_r((array)$s);
}

echo "\n-- ejemplos (limit 10) pedidos recientes --\n";
$examples = DB::select("SELECT id, id_usuario, estado, fecha_pedido, created_at FROM pedidos ORDER BY created_at DESC LIMIT 10");
foreach ($examples as $e) {
    print_r((array)$e);
}

echo "\nDone.\n";
