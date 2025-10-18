<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Arr;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //学習タスクのカタログ
        $catalog = [
            'Python入門：基礎文法ドリル',
            'Python：リスト内包表記を写経10問',
            'Python：例外処理とwith文の整理',
            'アルゴリズム：二分探索を実装＆計算量確認',
            'アルゴリズム：ダイクストラ法をPythonで',
            'アルゴリズム：Union-Find（素集合データ構造）実装',
            'データ構造：スタック/キュー/Deque自作',
            'SQL演習：JOIN/GROUP BY/集計30問',
            'PostgreSQL：インデックスとEXPLAINの基礎',
            'HTTP：RESTのCRUDとステータスコード要点メモ',
            'Laravel：FormRequestバリデーション整理',
            'Laravel：Eloquentリレーションを図にする',
            'Laravel：Factory/Seeder整備（テストデータ）',
            'Laravel：Featureテスト（CRUD）を追加',
            'PHP：型・readonly・enumの復習（8.1+）',
            'PHPUnit：データプロバイダでパラメトリックテスト',
            'パフォーマンス：N+1検知とwith()適用演習',
            'セキュリティ：CSRF/XSSの基本確認',
            '正規表現：よく使う10パターン写経',
            'Docker/Sail：サービス構成図解',
            'Git：rebase/cherry-pick練習',
            'CI：GitHub Actionsでphpunitを走らせる',
            'Clean Architecture：要点読書メモ',
            'SOLID原則：各原則のサンプル実装',
            'リファクタリング：命名と関数抽出の練習',
            'Postman：APIコレクション作成と環境変数',
            'API設計：エラーレスポンスの共通化',
            'ログ/監視：laravel.logと失敗時の手順書',
            'ドキュメント：READMEにセットアップ手順追加',
            'ふりかえり：1日の学びを5行で言語化',
        ];
     // 既存ユーザー全員に、3〜8件ずつ付与
        User::query()->chunkById(100, function ($users) use ($catalog) {
            foreach ($users as $user) {
                Task::factory()
                    ->for($user)
                    ->count(random_int(3, 8))
                    ->state(function () use ($catalog) {
                        // 作成日時を直近60日でばらす／完了率は3割ほど
                        $daysAgo   = random_int(0, 60);
                        $timestamp = now()->subDays($daysAgo)->setTime(random_int(8, 23), random_int(0, 59));

                        return [
                            'title'      => Arr::random($catalog),
                            'is_done'    => fake()->boolean(30), // 30% の確率で完了
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    })
                    ->create();
            }
        });
    }
    }
