<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;


class Review extends Model
{
 use BelongsToTenant;

    protected $fillable = [
        'university_id', 'transaction_id', 'reviewer_id', 'reviewee_id', 'rating', 'comment'
    ];

    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
}
