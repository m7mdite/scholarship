<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');

        // قائمة الأدمن بأسمائهم
        $admins = [
            ['name' => 'محمدصادق', 'email' => 'm@gmail.com'],
            ['name' => 'عبدالله', 'email' => 'abduallah@gmail.com'],
            ['name' => 'سهام', 'email' => 'siham@gmail.com'],
            ['name' => 'زينة', 'email' => 'zeina@gmail.com'],
            ['name' => 'نتالي', 'email' => 'nataly@gmail.com'],
        ];

        foreach ($admins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        // إنشاء 10 مستخدمين عاديين
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('تم إنشاء 5 أدمن و 10 مستخدمين عاديين بنجاح.');
    }
}