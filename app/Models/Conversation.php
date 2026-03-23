<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['product_id', 'buyer_id', 'seller_id'];

    // ห้องแชทนี้ เป็นของสินค้าอะไร?
    public function product() {
        return $this->belongsTo(Product::class);
    }

    // ใครคือคนซื้อ?
    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // ใครคือคนขาย?
    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // ดึงข้อความทั้งหมดในห้องแชทนี้
    public function messages() {
        return $this->hasMany(Message::class);
    }
}