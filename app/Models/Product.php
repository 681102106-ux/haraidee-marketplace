<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Manipulations;
class Product extends Model implements HasMedia
{
    // เรียกใช้งาน Trait ทั้งหมดที่จำเป็น
    use SoftDeletes, BelongsToTenant, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'category_id',
        'condition_id',
        'title',
        'description',
        'price',
        'status',
        'is_boosted',
        'views_count',
    ];

    /**
     * ==========================================
     * Relationships (อิงตาม ER Diagram)
     * ==========================================
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * ==========================================
     * Spatie MediaLibrary Conversions
     * ==========================================
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // สร้างเวอร์ชัน 'optimized' ทำงานผ่าน Background Queue อัตโนมัติ
        $this->addMediaConversion('optimized')
            ->format('webp')
            ->quality(80)
            ->fit(Fit::Max, 1200, 1200)
            ->performOnCollections('product_images');
    }
   
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists', 'product_id', 'user_id')->withTimestamps();
    }
}