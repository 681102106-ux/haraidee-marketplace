<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    /**
     * Boot the trait. บังคับทำงานทุกครั้งที่ Model นี้ถูกเรียกใช้
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (tenancy()->initialized) {
                $builder->where('university_id', tenant('id'));
            }
        });

        static::creating(function (Model $model) {
            if (tenancy()->initialized && ! $model->getAttribute('university_id')) {
                $model->setAttribute('university_id', tenant('id'));
            }
        });
    }

    /**
     * Helper Relationship
     */
    public function university()
    {
        return $this->belongsTo(\App\Models\University::class, 'university_id');
    }
}
