<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "URL Functions Test:\n";
echo "url(): " . url('test') . "\n";
echo "url('kantin/api/vendors'): " . url('kantin/api/vendors') . "\n";
echo "Expected: /kantin/api/vendors\n";
