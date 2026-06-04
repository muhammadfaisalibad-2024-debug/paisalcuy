<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuKantin extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'idmenu';

    protected $fillable = [
        'nama_menu',
        'harga',
        'path_gambar',
        'idvendor',
    ];
}
