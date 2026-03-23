<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('university_id'); 
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // ใช้ unsignedBigInteger ให้ตรงกับ id ของ categories/conditions 
            // *หมายเหตุ: หาก id ตาราง categories เป็นแค่ int ให้เปลี่ยนเป็น unsignedInteger
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('condition_id');

            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('status')->default('active'); // active, sold, banned
            $table->boolean('is_boosted')->default(false);
            $table->integer('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes(); // deleted_at

            // ==========================================
            // 🔥 PERFORMANCE OPTIMIZATION (Composite Indexes)
            // ==========================================

            // 1. Index สำหรับหน้า Feed หลัก (เรียงตาม มหาวิทยาลัย -> สถานะ -> วันที่ลงขาย)
            // รองรับ Query: where('university_id', X)->where('status', 'active')->orderBy('created_at', 'desc')
            $table->index(['university_id', 'status', 'created_at'], 'idx_feed_display');

            // 2. Index สำหรับเวลา User กดกรองหาสินค้าตามหมวดหมู่หรือสภาพสินค้า
            // รองรับ Query: where('university_id', X)->where('category_id', Y)
            $table->index(['university_id', 'category_id'], 'idx_category_filter');

            // 3. Index สำหรับดูสินค้าของ User แต่ละคน (Profile Page)
            $table->index(['university_id', 'user_id'], 'idx_user_products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
