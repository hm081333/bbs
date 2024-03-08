<?php

namespace Database\Seeders;

use App\Models\System\SystemLanguage;
use App\Utils\Tools;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        config(['app.no_sql_log' => true]);
        $now_time = Tools::time();
        $language_list = collect([
            [
                'key' => 'id_id',
                'name' => 'Bahasa Indonesia',
                'locale' => 'id',
            ],
            [
                'key' => 'de_de',
                'name' => 'Deutsch',
                'locale' => 'de',
            ],
            [
                'key' => 'en_us',
                'name' => 'English',
                'locale' => 'en',
            ],
            [
                'key' => 'es_xl',
                'name' => 'Español',
                'locale' => 'es',
            ],
            [
                'key' => 'fr_fr',
                'name' => 'Français',
                'locale' => 'fr',
            ],
            [
                'key' => 'pt_br',
                'name' => 'Português',
                'locale' => 'pt_BR',
            ],
            [
                'key' => 'vi_vn',
                'name' => 'Tiếng Việt',
                'locale' => 'vi',
            ],
            [
                'key' => 'th_th',
                'name' => 'ไทย',
                'locale' => 'th',
            ],
            [
                'key' => 'ko_kr',
                'name' => 'ไทย',
                'locale' => 'ko',
            ],
            [
                'key' => 'ja_jp',
                'name' => '日本語',
                'locale' => 'ja',
            ],
            [
                'key' => 'zh_cn',
                'name' => '简体中文',
                'locale' => 'zh_CN',
            ],
            [
                'key' => 'zh_tw',
                'name' => '繁體中文',
                'locale' => 'zh_TW',
            ],
        ]);
        SystemLanguage::insert($language_list
            ->whereNotIn('key', SystemLanguage::whereIn('key', $language_list->pluck('key'))
                ->select('key')
                ->pluck('key'))
            ->map(function ($language) use ($now_time) {
                $language['created_at'] = $now_time;
                $language['updated_at'] = $now_time;
                return $language;
            })->toArray());
    }
}
