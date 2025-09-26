<?php
require __DIR__ . '/vendor/autoload.php';

// Simple check: prints "yes" if User implements the Tymon JWTSubject contract
$implements = is_subclass_of(\App\Models\User::class, \Tymon\JWTAuth\Contracts\JWTSubject::class);
echo $implements ? "yes\n" : "no\n";
