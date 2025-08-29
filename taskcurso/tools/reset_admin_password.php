<?php

// Usage: php tools/reset_admin_password.php admin@demo.com newpassword

$cwd = __DIR__ . '/..';
chdir($cwd);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

if ($argc < 3) {
    echo "Usage: php tools/reset_admin_password.php <email> <new_password>\n";
    exit(1);
}

$email = $argv[1];
$newPassword = $argv[2];

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User with email {$email} not found.\n";
    exit(2);
}

$user->password = Hash::make($newPassword);
$user->save();

echo "Password for {$email} updated successfully.\n";

return 0;
