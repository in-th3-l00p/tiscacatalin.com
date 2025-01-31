<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiCommand extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'command',
    ];

    public function permissions(): HasMany {
        return $this->hasMany(Permission::class);
    }
}
