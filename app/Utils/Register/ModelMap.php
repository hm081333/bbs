<?php

namespace App\Utils\Register;

use App\Exceptions\Server\InternalServerErrorException;
use Illuminate\Database\Eloquent\Model;

/**
 * 模型映射类
 *
 * @property \App\Models\AuthModel $AuthModel AuthModel
 * @property \App\Models\BaiduId $BaiduId BaiduId
 * @property \App\Models\BaseModel $BaseModel BaseModel
 * @property \App\Models\File $File File
 * @property \App\Models\Fund\Fund $FundFund Fund
 * @property \App\Models\Fund\FundNetValue $FundFundNetValue FundNetValue
 * @property \App\Models\Fund\FundValuation $FundFundValuation FundValuation
 * @property \App\Models\Option $Option Option
 * @property \App\Models\OptionItem $OptionItem OptionItem
 * @property \App\Models\User $User User
 * @property \App\Models\UserFund $UserFund UserFund
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
