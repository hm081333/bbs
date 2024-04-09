<?php

namespace Database\Seeders;

use App\Models\Adv;
use App\Models\AdvCategory;
use App\Utils\Tools;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');//禁用外键约束
        AdvCategory::truncate();
        Adv::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');//启用外键约束
        $adv_categories = [
            [
                'name' => 'H5用户端-商城首页顶部轮播图',
                'code' => 'h5_home_shop_index_top',
                'advs' => [
                    [
                        'title' => 'swiper1',
                        'image' => Tools::file()->setRemoteFile('https://cdn.uviewui.com/uview/swiper/swiper1.png')->save('adv'),
                    ],
                    [
                        'title' => 'swiper2',
                        'image' => Tools::file()->setRemoteFile('https://cdn.uviewui.com/uview/swiper/swiper2.png')->save('adv'),
                    ],
                    [
                        'title' => 'swiper3',
                        'image' => Tools::file()->setRemoteFile('https://cdn.uviewui.com/uview/swiper/swiper3.png')->save('adv'),
                    ],
                ],
            ],
            [
                'name' => 'H5用户端-论坛首页顶部轮播图',
                'code' => 'h5_home_forum_index_top',
                'advs' => [
                    [
                        'title' => 'swiper1',
                        'image' => Tools::file()->setRemoteFile('https://cdn.uviewui.com/uview/swiper/swiper1.png')->save('adv'),
                    ],
                    [
                        'title' => 'swiper2',
                        'image' => Tools::file()->setRemoteFile('https://cdn.uviewui.com/uview/swiper/swiper2.png')->save('adv'),
                    ],
                    [
                        'title' => 'swiper3',
                        'image' => Tools::file()->setRemoteFile('https://cdn.uviewui.com/uview/swiper/swiper3.png')->save('adv'),
                    ],
                ],
            ],
        ];
        foreach ($adv_categories as $adv_category) {
            AdvCategory::make()->saveData($adv_category);
        }
    }
}
