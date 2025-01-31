<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_command_id',
        'permission_name',
    ];

    public function apiCommand(): BelongsTo {
        return $this->belongsTo(ApiCommand::class);
    }
}
