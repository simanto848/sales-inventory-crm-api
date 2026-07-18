<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'location'])]
class Branch extends Model
{
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'branch_products')
            ->withPivot('stock_quantity')
            ->withTimestamps();
    }
}
