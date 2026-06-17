<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'auditable_label',
        'before_values',
        'after_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before_values' => 'array',
        'after_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
