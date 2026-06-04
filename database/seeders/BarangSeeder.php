<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan dulu (trigger tidak jalan saat truncate, pakai delete)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('tb_barang')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $items = [
            ['nama_barang' => 'Baju Batik Wanita',        'harga' => 85000,  'satuan' => 'pcs',  'kategori' => 'Pakaian',   'stok' => 50],
            ['nama_barang' => 'Celana Jeans Pria',         'harga' => 120000, 'satuan' => 'pcs',  'kategori' => 'Pakaian',   'stok' => 30],
            ['nama_barang' => 'Keripik Singkong 250gr',    'harga' => 15000,  'satuan' => 'bks',  'kategori' => 'Makanan',   'stok' => 100],
            ['nama_barang' => 'Sambal Bajak Kemasan',      'harga' => 22000,  'satuan' => 'btl',  'kategori' => 'Makanan',   'stok' => 75],
            ['nama_barang' => 'Tas Anyaman Rotan',         'harga' => 145000, 'satuan' => 'pcs',  'kategori' => 'Kerajinan', 'stok' => 20],
            ['nama_barang' => 'Tempat Pensil Kayu',        'harga' => 35000,  'satuan' => 'pcs',  'kategori' => 'Kerajinan', 'stok' => 40],
            ['nama_barang' => 'Sabun Herbal Lavender',     'harga' => 18000,  'satuan' => 'pcs',  'kategori' => 'Kosmetik',  'stok' => 60],
            ['nama_barang' => 'Minyak Kelapa Murni 200ml', 'harga' => 45000,  'satuan' => 'btl',  'kategori' => 'Kosmetik',  'stok' => 35],
            ['nama_barang' => 'Kue Nastar Toples',         'harga' => 65000,  'satuan' => 'tpls', 'kategori' => 'Makanan',   'stok' => 25],
            ['nama_barang' => 'Gelang Manik-Manik',        'harga' => 27500,  'satuan' => 'pcs',  'kategori' => 'Aksesoris', 'stok' => 80],
            ['nama_barang' => 'Dompet Kulit Wanita',       'harga' => 95000,  'satuan' => 'pcs',  'kategori' => 'Aksesoris', 'stok' => 15],
            ['nama_barang' => 'Kopi Robusta Bubuk 200gr',  'harga' => 32000,  'satuan' => 'bks',  'kategori' => 'Minuman',   'stok' => 90],
        ];

        foreach ($items as $item) {
            // id_barang diisi dummy dulu, trigger akan override
            DB::table('tb_barang')->insert(array_merge($item, [
                'id_barang'   => 'TEMP',
                'deskripsi'   => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));
        }
    }
}
