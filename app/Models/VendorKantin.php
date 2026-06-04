<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorKantin extends Model
{
    protected $table = 'vendor';
    protected $primaryKey = 'idvendor';

    protected $fillable = [
        'nama_vendor',
    ];
}
