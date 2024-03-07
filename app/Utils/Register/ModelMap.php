<?php

namespace App\Utils\Register;

use App\Exceptions\Server\InternalServerErrorException;

/**
 * 模型映射类
 *
 * @property-read \App\Models\BaiduId BaiduId App\Models\BaiduId
 * @property-read \App\Models\Fund\Fund FundFund App\Models\Fund\Fund
 * @property-read \App\Models\Fund\FundNetValue FundFundNetValue App\Models\Fund\FundNetValue
 * @property-read \App\Models\Fund\FundValuation FundFundValuation App\Models\Fund\FundValuation
 * @property-read \App\Models\Intel\IntelProduct IntelIntelProduct App\Models\Intel\IntelProduct
 * @property-read \App\Models\Intel\IntelProductCategory IntelIntelProductCategory App\Models\Intel\IntelProductCategory
 * @property-read \App\Models\Intel\IntelProductSeries IntelIntelProductSeries App\Models\Intel\IntelProductSeries
 * @property-read \App\Models\Intel\IntelProductSpec IntelIntelProductSpec App\Models\Intel\IntelProductSpec
 * @property-read \App\Models\Mongodb\AccessLog MongodbAccessLog App\Models\Mongodb\AccessLog
 * @property-read \App\Models\Mongodb\SqlLog MongodbSqlLog App\Models\Mongodb\SqlLog
 * @property-read \App\Models\System\AdministrativeDivision SystemAdministrativeDivision App\Models\System\AdministrativeDivision
 * @property-read \App\Models\System\SystemFile SystemSystemFile App\Models\System\SystemFile
 * @property-read \App\Models\System\SystemLanguage SystemSystemLanguage App\Models\System\SystemLanguage
 * @property-read \App\Models\System\SystemOption SystemSystemOption App\Models\System\SystemOption
 * @property-read \App\Models\System\SystemOptionItem SystemSystemOptionItem App\Models\System\SystemOptionItem
 * @property-read \App\Models\User User App\Models\User
 * @property-read \App\Models\UserFund UserFund App\Models\UserFund
 * @package App\Utils\Register
 * @class ModelMap
 */
class ModelMap
{
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|array
     */
    private array $config;

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
