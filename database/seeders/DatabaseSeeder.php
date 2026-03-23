<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Condition;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
    
        // 1. สร้างมหาวิทยาลัยตั้งต้น (จุฬาฯ)
        $chula = University::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Chulalongkorn University',
            'domain' => 'chula.localhost',
            'primary_color' => '#FF66B2',
            'is_active' => true,
        ]);

        // 🔥 เพิ่มบรรทัดนี้! เพื่อบอกแพ็กเกจ Tenancy ให้บันทึกโดเมนนี้เข้าระบบด้วย
        $chula->domains()->create(['domain' => 'chula.localhost']);

        // 2. สร้างบัญชี Super Admin ประจำระบบ (เอาไว้ล็อกอินเป็นพระเจ้า)
        User::create([
            'university_id' => $chula->id,
            'name' => 'God Admin',
            'email' => 'admin@haraidee.com',
            'password' => Hash::make('password'), // รหัสผ่านคือ password
            'role' => 'super_admin',
        ]);

        // 3. สร้างบัญชี Student ธรรมดาไว้เทสต์
        User::create([
            'university_id' => $chula->id,
            'name' => 'Demo Student',
            'email' => 'student@chula.ac.th',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
        
        $this->command->info('🎉 สร้างข้อมูลตั้งต้นสำเร็จ! (จุฬาฯ + Super Admin + Student)');

        // ==========================================
        // 3. สร้างข้อมูลพื้นฐาน (Starter Pack) ให้จุฬาฯ
        // ==========================================
        
        // หมวดหมู่สินค้าพื้นฐาน
        $defaultCategories = ['อุปกรณ์การเรียน', 'หนังสือ/ชีทเรียน', 'อุปกรณ์ไอที', 'เสื้อผ้านักศึกษา', 'หอพัก/ของใช้', 'อื่นๆ'];
        foreach ($defaultCategories as $index => $cat) { // 🔥 เพิ่ม $index ตรงนี้
            Category::create([
                'university_id' => $chula->id,
                'name' => $cat,
                'slug' => 'cat-' . ($index + 1) // 🔥 เปลี่ยนตรงนี้เป็น cat-1, cat-2...
            ]);
        }

        // สภาพสินค้าพื้นฐาน
        $defaultConditions = ['มือหนึ่ง (ของใหม่)', 'มือสอง (สภาพนางฟ้า)', 'มือสอง (สภาพใช้งาน)'];
        foreach ($defaultConditions as $index => $cond) { // 🔥 เพิ่ม $index ตรงนี้
            Condition::create([
                'university_id' => $chula->id,
                'name' => $cond,
                'slug' => 'cond-' . ($index + 1) // 🔥 เปลี่ยนตรงนี้เป็น cond-1, cond-2...
            ]);
        }
        
        $this->command->info('🎉 สร้างข้อมูลตั้งต้นสำเร็จ! (พร้อม Starter Pack หมวดหมู่สินค้า)');
    
    }
}