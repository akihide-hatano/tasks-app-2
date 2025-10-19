<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1) 固定ログイン用ユーザー（毎回同じで便利）
        $fixed = [
            ['name' => 'デモ 太郎',  'email' => 'demo@example.com'],
            ['name' => 'PM 花子',    'email' => 'pm@example.com'],
            ['name' => '管理者 次郎','email' => 'admin@example.com'],
        ];

        foreach ($fixed as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name'              => $u['name'],
                    'password'          => Hash::make('password'), // 全員 password
                    'email_verified_at' => now(),
                    'remember_token'    => Str::random(10),
                ]
            );
        }

        // 2) 追加の一般ユーザー（日本語名・未認証も混ぜる）
        // faker_locale を ja_JP にしておくと日本語名になります（config/app.php）
        User::factory()
            ->count(30)
            ->state(fn () => [
                // 30% は未認証ユーザーにしてバリエーション
                'email_verified_at' => fake()->boolean(70) ? now() : null,
            ])
            ->create();
    }
}
