<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'sku', 'price'])]
class Product extends Model
{
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_products')
            ->withPivot('stock_quantity')
            ->withTimestamps();
    }
}
