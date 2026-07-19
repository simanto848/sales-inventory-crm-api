<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'kpi_score'];
    protected $hidden = ['password', 'remember_token'];
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'kpi_score' => 'integer',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'employee_id');
    }

    public function assignedCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'assigned_employee_id');
    }
}