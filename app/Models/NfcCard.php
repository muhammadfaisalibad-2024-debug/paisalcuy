<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NfcCard extends Model
{
    protected $table = 'nfc_cards';

    protected $fillable = [
        'serial',
        'owner_name',
        'meta',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'nfc_card_id');
    }
}
