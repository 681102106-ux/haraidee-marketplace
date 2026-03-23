<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    use BelongsToTenant;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function products()
    {
       return $this->hasMany(Product::class);
    }
}
