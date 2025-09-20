<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RefreshToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'user_agent',
        'ip_address'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    public static function cleanExpired(): void
    {
        static::where('expires_at', '<', Carbon::now())->delete();
    }
}
