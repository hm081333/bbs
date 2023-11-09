<?php

namespace App\Utils\Register;

use App\Exceptions\Server\InternalServerErrorException;
use Illuminate\Database\Eloquent\Model;

/**
 * 模型映射类
 *
 * @property \App\Models\AuthModel $modelAuthModel AuthModel
 * @property \App\Models\BaiduId $modelBaiduId BaiduId
 * @property \App\Models\BaseModel $modelBaseModel BaseModel
 * @property \App\Models\File $modelFile File
 * @property \App\Models\Fund\Fund $modelFundFund Fund
 * @property \App\Models\Fund\FundNetValue $modelFundFundNetValue FundNetValue
 * @property \App\Models\Fund\FundValuation $modelFundFundValuation FundValuation
 * @property \App\Models\Option $modelOption Option
 * @property \App\Models\OptionItem $modelOptionItem OptionItem
 * @property \App\Models\User $modelUser User
 * @property \App\Models\UserFund $modelUserFund UserFund
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
        $this->model_map = config('model_map', []);
        $this->model_column_map = config('model_column_map', []);
    }

    public function __get($name)
    {
        $names = explode('_', $name);
        $model_name = $names[0] ?? '';
        if (!empty($names[1])) {
            if ($names[1] == 'columns' || $names[1] == 'column') {
                if (isset($this->model_column_map[$model_name])) {
                    $model_columns = $this->model_column_map[$model_name];
                    if (!empty($names[2]) && ($names[2] == 'key' || $names[2] == 'name')) return array_keys($model_columns);
                    return $model_columns;
                }
            } else if ($names[1] == 'table') {
                $model = $this->getModel($model_name, false);
                return (!empty($names[2]) ? $model->getConnection()->getTablePrefix() : '') . $model->getTable();
            }
        }
        return $this->getModel($model_name);
    }

    /**
     * 获取模型实例
     *
     * @param string $model_name 模型别名
     * @param bool   $new_model  是否克隆返回新实例
     *
     * @return Model
     * @throws InternalServerErrorException
     */
    private function getModel(string $model_name, bool $new_model = true): Model
    {
        if (isset($this->model_map[$model_name])) {
            $model_class = $this->model_map[$model_name];
            if (class_exists($model_class)) {
                $this->$model_name = new $model_class;
                return $new_model ? clone $this->$model_name : $this->$model_name;
            }
        }
        throw new InternalServerErrorException('非法调用不存在函数');
    }

}
