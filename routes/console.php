<?php

use App\Utils\GuzzleHttp;
use App\Utils\Juhe\Calendar;
use App\Utils\Tools;
use App\Utils\WeChat\OfficialAccount;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('testa', function () {
    $where = ['id', '=', 1];
    @[$column, $operator, $value, $boolean] = $where;
    dd($column, $operator, $value, $boolean);
    die;
    dd(OfficialAccount::updateAllUserInfo());
    $start_year = '2007';
    $http = new GuzzleHttp();
    //https://sousuo.www.gov.cn/sousuo/search.shtml?code=17da70961a7&dataTypeId=107&searchWord=国务院办公厅关于2024年部分节假日安排的通知
    $content = $http->singleRequest('get', 'https://www.gov.cn/gongbao/2023/issue_10806/202311/content_6913823.html')->getBody()->getContents();
    // $content = $http->singleRequest('get', 'https://www.gov.cn/gongbao/content/2023/content_5736714.htm')->getBody()->getContents();
    // $content = Tools::compress_html($content);
    preg_match_all('/<p.*?\/p>/', $content, $matches);
    $texts = array_map(function ($html) {
        $text = trim(strip_tags($html));
        return preg_match('/^[一二三四五六七八九十]+、/', $text) ? str_replace("\u{3000}", ' ', $text) : null;
    }, $matches[0]);
    $texts = array_values(array_filter($texts));
    dd($texts);
    dd(123);
    $year = Calendar::year('2024');
    foreach ($year['data']['holiday_list'] as $datum) {
        $startday = Tools::timeToCarbon($datum['startday']);
        dd(Calendar::month($startday->format('Y-m')));

    }
    dd(123);
})->purpose('cesi');
