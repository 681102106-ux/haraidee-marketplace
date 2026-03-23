<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class University extends BaseTenant
{
    use SoftDeletes, HasDomains;

    // 🔥 1. บังคับให้ใช้ตาราง universities (ห้ามไปใช้ tenants)
    protected $table = 'universities';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'domain',
        'primary_color',
        'logo_path',
        'is_active',
        'data',
    ];

    // 🔥 2. บอกแพ็กเกจว่า "นี่คือคอลัมน์จริงใน Database นะ! อย่าเอามันไปยัดลง JSON!"
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'domain',
            'primary_color',
            'logo_path',
            'is_active',
        ];
    }
}