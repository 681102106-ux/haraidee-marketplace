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
        // 1. ตาราง Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); 
            $table->uuid('university_id'); // UUID สำหรับ Tenant
            $table->string('name');
            $table->string('slug');
            
            // Composite Unique Index ป้องกัน Category ซ้ำใน ม. เดียวกัน
            $table->unique(['university_id', 'slug'], 'unique_category_per_university');
            
            // Note: ยังไม่ทำ Foreign Key constraint ผูกกับ universities 
            // เพื่อลด Overhead ตอน Insert/Update (จัดการ Logic ใน Code แทน)
        });

        // 2. ตาราง Conditions (สภาพสินค้า)
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->uuid('university_id');
            $table->string('name');
            $table->string('slug');

            $table->unique(['university_id', 'slug'], 'unique_condition_per_university');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conditions');
        Schema::dropIfExists('categories');
    }
};
