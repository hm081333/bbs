<?php

namespace Database\Seeders;

use App\Models\Article\Article;
use App\Models\Article\ArticleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');//禁用外键约束
        ArticleCategory::truncate();
        Article::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');//启用外键约束
        $categorys = [
            [
                'title' => '系统相关',
                'code' => 'system_related',
                'articles' => [
                    [
                        'title' => '用户协议',
                        'content' => '用户协议',
                        'desc' => '用户协议',
                        'code' => 'user_agreement',
                    ],
                    [
                        'title' => '隐私政策',
                        'content' => '隐私政策',
                        'desc' => '隐私政策',
                        'code' => 'privacy_policy',
                    ],
                    [
                        'title' => '关于我们',
                        'content' => '关于我们',
                        'desc' => '关于我们',
                        'code' => 'about_us',
                    ],
                ],
            ],
            [
                'title' => '帮助中心',
                'code' => 'help_center',
                'children' => [
                    [
                        'title' => '平台使用指南',
                        'articles' => [
                            [
                                'title' => '平台使用指南1',
                                'content' => '平台使用指南1',
                                'desc' => '平台使用指南1',
                            ],
                            [
                                'title' => '平台使用指南2',
                                'content' => '平台使用指南2',
                                'desc' => '平台使用指南2',
                            ],
                            [
                                'title' => '平台使用指南3',
                                'content' => '平台使用指南3',
                                'desc' => '平台使用指南3',
                            ],
                        ],
                    ],
                    [
                        'title' => '常见问题描述',
                        'articles' => [
                            [
                                'title' => '常见问题描述1',
                                'content' => '常见问题描述1',
                                'desc' => '常见问题描述1',
                            ],
                            [
                                'title' => '常见问题描述2',
                                'content' => '常见问题描述2',
                                'desc' => '常见问题描述2',
                            ],
                            [
                                'title' => '常见问题描述3',
                                'content' => '常见问题描述3',
                                'desc' => '常见问题描述3',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => '新闻动态',
                'code' => 'news_trends',
                'articles' => [
                    [
                        'title' => '新闻动态1',
                        'content' => '新闻动态1',
                        'desc' => '新闻动态1',
                    ],
                    [
                        'title' => '新闻动态2',
                        'content' => '新闻动态2',
                        'desc' => '新闻动态2',
                    ],
                    [
                        'title' => '新闻动态3',
                        'content' => '新闻动态3',
                        'desc' => '新闻动态3',
                    ],
                ],
            ],
            [
                'title' => '滚动消息',
                'code' => 'rolling_message',
                'articles' => [
                    [
                        'title' => '滚动消息1',
                        'content' => '滚动消息1',
                        'desc' => '滚动消息1',
                    ],
                    [
                        'title' => '滚动消息2',
                        'content' => '滚动消息2',
                        'desc' => '滚动消息2',
                    ],
                    [
                        'title' => '滚动消息3',
                        'content' => '滚动消息3',
                        'desc' => '滚动消息3',
                    ],
                ],
            ],
        ];
        foreach ($categorys as $category) {
            ArticleCategory::make()->saveData($category);
        }
    }
}
