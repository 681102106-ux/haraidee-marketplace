<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // ==========================================
            // 🔥 TENANCY & CUSTOM FIELDS (ตาม ER Diagram)
            // ==========================================
            $table->uuid('university_id'); 
            $table->enum('role', ['super_admin', 'uni_admin', 'student'])->default('student');
            $table->string('profile_image')->nullable();
            $table->boolean('is_verified')->default(false);
            
            // ==========================================
            // STANDARD LARAVEL FIELDS
            // ==========================================
            $table->string('name');
            $table->string('email'); // ลบ ->unique() ออกจากตรงนี้!
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // ==========================================
            // COMPOSITE INDEXES
            // ==========================================
            // สร้าง Unique Index คู่กัน (ป้องกันอีเมลซ้ำใน ม. เดียวกัน)
            $table->unique(['university_id', 'email'], 'unique_user_per_university');
        });

        // ... (ตาราง password_reset_tokens และ sessions ปล่อยไว้เหมือนเดิมครับ)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};