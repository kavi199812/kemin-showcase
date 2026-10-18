<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Keys that should be encrypted at rest if applicable.
     * Note: presenter_pin_hash is an argon2id hash, which is a one-way cryptographic hash.
     */
    /**
     * @var list<string>
     */
    protected static array $encryptedKeys = [
        // Any specific sensitive API keys or secrets can be encrypted here
    ];

    public function getValueAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (in_array($this->key, self::$encryptedKeys, true)) {
            try {
                return decrypt($value);
            } catch (\Throwable $e) {
                return $value;
            }
        }

        return $value;
    }

    public function setValueAttribute(?string $value): void
    {
        if ($value !== null && in_array($this->attributes['key'] ?? $this->key, self::$encryptedKeys, true)) {
            $this->attributes['value'] = encrypt($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}
