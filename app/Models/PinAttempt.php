<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ip',
        'fingerprint',
        'succeeded',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'succeeded' => 'boolean',
            'created_at' => 'datetime',
        ];
    }
}
