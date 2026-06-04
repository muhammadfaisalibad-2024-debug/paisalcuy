<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MenuKantin;
use App\Models\VendorKantin;

$vendor = VendorKantin::first();
echo "Vendor Count: " . VendorKantin::count() . "\n";
if ($vendor) {
    echo "First Vendor: {$vendor->nama_vendor} (ID: {$vendor->idvendor})\n";
    $menus = MenuKantin::where('idvendor', $vendor->idvendor)->get();
    echo "Menu Count: " . $menus->count() . "\n";
    foreach ($menus as $m) {
        echo "  - {$m->nama_menu} (Rp {$m->harga})\n";
    }
} else {
    echo "No vendors found!\n";
}
