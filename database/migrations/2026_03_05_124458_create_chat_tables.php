<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ตารางเก็บห้องแชท
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // กฎเหล็ก: 1 สินค้า + 1 คนซื้อ + 1 คนขาย = มีได้แค่ห้องแชทเดียว
            $table->unique(['buyer_id', 'seller_id', 'product_id'], 'unique_conversation');
        });

        // 2. ตารางเก็บข้อความในแชท
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['text', 'image', 'file', 'call_log'])->default('text');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            
            // ใช้แค่ created_at ตาม ERD ของคุณ
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};