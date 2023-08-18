<?php

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund;
use App\Models\FundNetValue;
use App\Models\FundValuation;
use App\Utils\Tools;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
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
    //$now_time = Tools::now();
    ////var_dump($now_time);
    //$modelFund = new Fund;
    ////$modelFundNetValue = new FundNetValue;
    ////$fund = $modelFund
    ////    ->leftJoin($modelFundNetValue->getTable(), $modelFund->getTable() . '.id', '=', $modelFundNetValue->getTable() . '.fund_id')
    ////    ->where($modelFund->getTable() . '.code', '008960')
    ////    ->orderByDesc($modelFundNetValue->getTable() . '.net_value_time')
    ////    ->first();
    //$modelFund->where('code', '008960')->update(['updated_at' => $now_time]);
    //$fund = $modelFund->where('code', '008960')->first();
    //dd($fund->toArray());
})->purpose('cesi');
