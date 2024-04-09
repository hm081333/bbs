<?php

namespace Database\Seeders;

use App\Models\System\SystemOption;
use App\Models\System\SystemOptionItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');//禁用外键约束
        SystemOption::truncate();
        SystemOptionItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');//启用外键约束
        $options = [
            [
                'name' => '论坛主题分类',
                'code' => 'forum_topic_classification',
                'items' => [
                    ['value' => '普通'],
                    ['value' => '资深'],
                    ['value' => '大师'],
                    ['value' => '明星'],
                ],
            ],
        ];
        foreach ($options as $option) {
            SystemOption::make()->saveData($option);
        }
    }
}
