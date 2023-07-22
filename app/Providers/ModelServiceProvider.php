<?php

namespace App\Providers;

use App\Exceptions\Request\BadRequestException;
use App\Models\OptionItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /* 分页函数 */
        Builder::macro('getPage', function ($columns = ['*']): array {
            /* @var $this Builder */
            // 每页显示数量
            $pageSize = request()->input('limit', $this->getModel()->getPerPage());
            // 页码
            $pageNo = request()->input('page', 1);
            // 搜索字段
            $search_field = request()->input('search_field');
            if ($search_field) {
                // 搜索关键字
                $this->searchInput($search_field, 'like', 'search_keyword');
            }
            /*// 搜索关键字
            $search_keyword = request()->input('search_keyword');
            $this->when(!empty($search_field) && !empty($search_keyword), function (Builder $query) use ($search_field, $search_keyword) {
                $query->search($search_field, 'like', "%{$search_keyword}%");
            });*/
            //region 解析前端传递的排序规则
            $this->orderByInput();
            //endregion
            // 调用分页查询
            $results = ($total = $this->toBase()->getCountForPagination())
                ? $this->forPage($pageNo, $pageSize)->get($columns)
                : $this->model->newCollection();
            return [
                'list' => $results,
                'total' => intval($total),
                'limit' => intval($pageSize),
                'page' => intval($pageNo),
            ];
        });
        /* 排序函数 */
        Builder::macro('orderByInput', function (): Builder {
            /* @var $this Builder */
            //region 解析前端传递的排序规则
            // 现有排序规则
            $orders = $this->getQuery()->orders;
            // 后添加的排序会替换前面添加的排序
            $orders = empty($orders) ? [] : array_combine(array_column($orders, 'column'), array_column($orders, 'direction'));
            // 请求携带 的 排序条件
            $orderBys = \request()->input('orderBy');
            if (!empty($orderBys)) {
                // 解出排序数组
                if (!is_array($orderBys)) {
                    $orderBys = explode(',', $orderBys);
                } else if (!array_key_exists(0, $orderBys)) {
                    $orderBys = array_map(function ($key) use ($orderBys) {
                        return "{$key} {$orderBys[$key]}";
                    }, array_keys($orderBys));
                }
                foreach ($orderBys as $orderBy) {
                    // 匹配 +/- 和 asc/desc
                    preg_match('/([^\s+-]+)\s?([+\-]|(([Dd][Ee]|[Aa])[Ss][Cc]))?/', $orderBy, $matches);
                    // 排序字段
                    $column = trim($matches[1]);
                    // 排序顺序
                    $direction = str_replace(['+', '-'], ['asc', 'desc'], trim(strtolower($matches[2] ?? 'asc')));
                    $orders[$column] = $direction;
                }
                unset($orderBys, $orderBy, $matches, $column, $direction);
            }
            // 清空现有排序
            $this->reorder();
            // 代码没有指定排序规则时，默认使用ID倒序
            if (empty($orders)) $orders['id'] = 'desc';
            // 循环并添加排序
            foreach ($orders as $column => $direction) {
                $this->orderBy($column, $direction);
            }
            unset($orders, $column, $direction);
            //endregion
            return $this;
        });
        /* 嵌套查询函数 */
        Builder::macro('search', function ($search_fields, $operator = null, $value = null, $boolean = 'and'): Builder {
            /* @var $this Builder */
            $args = func_get_args();
            $value = $args[2] ?? $args[1];
            $operator = isset($args[2]) ? $args[1] : '=';
            unset($args[0]);
            if (is_string($search_fields)) $search_fields = explode('.', $search_fields);
            $search_field = array_shift($search_fields);
            if (empty($search_fields)) {
                // if (strtolower($operator) == 'like') $value = "%{$value}%";
                $this->where($search_field, $operator, $value, $boolean);
            } else {
                /* @var $RelationModel \Illuminate\Database\Eloquent\Relations\BelongsTo */
                /* @var $RelationModel \Illuminate\Database\Eloquent\Relations\HasMany */
                /* @var $RelationModel \Illuminate\Database\Eloquent\Relations\HasOne */
                $RelationModel = $this->getModel()->$search_field();
                if ($RelationModel instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                    $where_key = $RelationModel->getForeignKeyName();
                    $select_key = $RelationModel->getOwnerKeyName();
                } else {
                    $where_key = $RelationModel->getLocalKeyName();
                    $select_key = $RelationModel->getForeignKeyName();
                }
                $this->whereIn($where_key, $RelationModel->getModel()->select($select_key)->search($search_fields, ...$args));
                // $this->whereHas($search_field, function (Builder $query) use ($search_fields, $args) {
                //     $query->search($search_fields, ...$args);
                // });
            }
            return $this;
        });
        /* 根据请求，嵌套查询函数 */
        Builder::macro('searchInput', function (string $search_field, $operator = null, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $input_field = $args[2] ?? $args[1] ?? $args[0];
            $operator = isset($args[2]) ? $args[1] : '=';
            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(isset($search_data), function (Builder $query) use ($search_field, $operator, $search_data) {
                if (strtolower($operator) == 'like') $search_data = "%{$search_data}%";
                $query->search($search_field, $operator, $search_data);
            });
        });
        /* 根据请求，查询指定字段函数 */
        Builder::macro('whereInput', function (string $search_field, $operator = null, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $input_field = $args[2] ?? $args[1] ?? $args[0];
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));

            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(isset($search_data) && !empty($search_data), function (Builder $query) use ($search_field, $operator, $search_data) {
                switch ($operator) {
                    case 'like':
                        $query->where($search_field, $operator, "%{$search_data}%");
                        break;
                    case 'in':
                        $query->whereIn($search_field, $search_data);
                        break;
                    default:
                        $query->where($search_field, $operator, $search_data);
                        break;
                }
            });
        });
        /* 根据请求，查询指定字段函数 */
        Builder::macro('whereInputOptionItem', function (string $search_field, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            // 获取搜索值
            $option_item_id = request()->input($input_field ?? $search_field);
            return $this->when(isset($option_item_id) && !empty($option_item_id), function (Builder $query) use ($search_field, $option_item_id) {
                $option_item_value = OptionItem::getValue($option_item_id);
                $arr = explode('-', $option_item_value);
                if (count($arr) == 2) {
                    $query->whereIn($search_field, $arr);
                } else {
                    $query->where($search_field, $option_item_value);
                }
            });
        });
        /* 根据请求，模糊查询指定字段函数 */
        Builder::macro('whereInputLike', function (string $search_field, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $input_field = $args[1] ?? $args[0];
            $operator = 'like';
            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(isset($search_data) && !empty($search_data), function (Builder $query) use ($search_field, $operator, $search_data) {
                $query->where($search_field, $operator, "%{$search_data}%");
            });
        });
        Builder::macro('firstOrThrow', function (string $thr_str = '数据异常') {
            /* @var $this \Illuminate\Database\Eloquent\Builder */
            return $this->firstOr(function () use ($thr_str) {
                throw new BadRequestException($thr_str);
            });
        });
    }
}
