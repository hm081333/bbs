<?php

namespace App\Utils\Register;

use App\Exceptions\Server\InternalServerErrorException;

/**
 * 模型映射类
 *
 * @property \App\Models\BaiduId $BaiduId App\Models\BaiduId
 * @property \App\Models\File $File App\Models\File
 * @property \App\Models\Fund\Fund $FundFund App\Models\Fund\Fund
 * @property \App\Models\Fund\FundNetValue $FundFundNetValue App\Models\Fund\FundNetValue
 * @property \App\Models\Fund\FundValuation $FundFundValuation App\Models\Fund\FundValuation
 * @property \App\Models\Option $Option App\Models\Option
 * @property \App\Models\OptionItem $OptionItem App\Models\OptionItem
 * @property \App\Models\User $User App\Models\User
 * @property \App\Models\UserFund $UserFund App\Models\UserFund
 * Class ModelMap
 */
class ModelMap
{
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|array
     */
    private array $model_map;
    private array $model_column_map;

    public function __construct()
    {
        $this->config = config('model_map', []);
    }

    public function __get($name)
    {
        $names = explode('_', $name);
        $model_alias = $names[0] ?? '';
        if (isset($this->config[$model_alias])) {
            $model_info = $this->config[$model_alias];
            if (!empty($names[1])) {
                if ($names[1] == 'columns' || $names[1] == 'column') {
                    if (isset($model_info['column'])) {
                        if (!empty($names[2]) && ($names[2] == 'key' || $names[2] == 'name')) return array_keys($model_info['column']);
                        return $model_info['column'];
                    }
                } else if ($names[1] == 'table') {
                    return empty($names[2]) ? $model_info['table'] : $model_info['table_full_name'];
                }
            }
            if (class_exists($model_info['model'])) return new $model_info['model'];
        }
        throw new InternalServerErrorException('非法调用不存在函数');
    }


}
