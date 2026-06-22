<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * OWASP A09 — Security Logging and Monitoring
 */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'event', 'description', 'ip_address', 'user_agent', 'metadata',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Rekam event keamanan dari request yang sedang berjalan.
     */
    public static function record(string $event, ?string $description = null, array $metadata = []): void
    {
        try {
            $request = app(Request::class);
            static::create([
                'user_id'     => auth()->id(),
                'event'       => $event,
                'description' => $description,
                'ip_address'  => $request->ip(),
                'user_agent'  => substr($request->userAgent() ?? '', 0, 500),
                'metadata'    => $metadata ?: null,
            ]);
        } catch (\Throwable) {
            // Jangan sampai error logging menghentikan request utama
        }
    }
}
