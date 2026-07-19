<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\HasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory as HasFactoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_active',
        'purchase_frequency',
        'last_purchase_date',
        'assigned_employee_id',
    ];
    use HasFactoryTrait;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_purchase_date' => 'date',
            'purchase_frequency' => 'integer',
        ];
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}