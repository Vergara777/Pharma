<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTablePreference extends Model
{
    protected $fillable = [
        'user_id',
        'table_name',
        'column_toggles',
        'per_page',
    ];

    protected $casts = [
        'column_toggles' => 'array',
        'per_page' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
