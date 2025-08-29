<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OrderController;
use App\Models\Pedido;

$pedido = Pedido::first();
if (!$pedido) {
    echo "No pedidos to test\n";
    exit(0);
}

$req = Request::create('/admin/orders/' . $pedido->id . '/status', 'POST', ['status' => 'Entregado']);
$controller = new OrderController();
$resp = $controller->updateStatus($req, $pedido);
if (is_object($resp)) {
    echo 'Controller returned object of class: ' . get_class($resp) . "\n";
} else {
    echo 'Controller returned: ' . var_export($resp, true) . "\n";
}
