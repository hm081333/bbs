<?php

namespace App\Providers;

use App\Exceptions\Request\BadRequestException;
use App\Models\System\SystemOptionItem;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class BuilderServiceProvider extends ServiceProvider
{
    /**
     * 引导服务。
     *
     * @return void
     */
    public function boot(): void
    {
        /**
         * 分页函数
         */
        Builder::macro('getPage', function ($columns = ['*']): array {
            /* @var $this Builder */
            // 每页显示数量
            $pageSize = request()->input('limit', $this->getModel()->getPerPage());
            // 页码
            $pageNo = request()->input('page', 1);
            // 搜索字段
            $search_field = request()->input('search_field');
            // 搜索关键字
            $this->searchInput($search_field, 'like', 'search_keyword');
            //region 解析前端传递的查询规则
            $this->selectByInput();
            $this->whereByInput();
            $this->orderByInput();
            //endregion
            // 调用分页查询
            $results = ($total = $this->toBase()->getCountForPagination())
                ? $this->forPage($pageNo, $pageSize)->get($columns)
                : $this->getModel()->newCollection();
            return [
                'list' => $results,
                'total' => intval($total),
                'limit' => intval($pageSize),
                'page' => intval($pageNo),
            ];
        });

        /**
         * 根据输入查询列
         */
        Builder::macro('selectByInput', function (): Builder {
            /* @var $this Builder */
            //region 解析前端传递的查询列
            // 请求携带 的 查询列
            $selectInput = request()->input('select');
            if (!empty($selectInput)) $this->select($selectInput);
            unset($selectInput);
            //endregion
            return $this;
        });

        /**
         * 输入查询条件
         */
        Builder::macro('whereByInput', function (): Builder {
            /* @var $this Builder */
            //region 解析前端传递的查询条件
            $wheres = [];
            // 请求携带 的 查询条件
            $whereInput = request()->input('where');
            /*$whereInput = [
                'column' => [
                    'id',
                ],
                'operator' => [
                    'id' => 'like',
                ],
                'value' => [
                    'id' => '123',
                ],
                ['id', '=', 1],
            ];*/
            if (!empty($whereInput)) {
                $columns = $whereInput['column'] ?? [];
                $operators = $whereInput['operator'] ?? [];
                $values = $whereInput['value'] ?? [];
                unset($whereInput['column'], $whereInput['operator'], $whereInput['value']);
                if ($columns) {
                    foreach ($columns as $column) {
                        $operator = $operators[$column] ?? '=';
                        $value = $values[$column] ?? null;
                        if (array_key_exists($column, $values)) $wheres[] = [$column, $operator, $value];
                    }
                    unset($column, $operator, $value);
                }
                unset($columns, $operators, $values);
                if (!empty($whereInput) && Arr::isList($whereInput)) {
                    foreach ($whereInput as $where) {
                        if (is_array($where) && count($where) >= 2) {
                            if (count($where) == 2) $where = [$where[0], '=', $where[1]];
                            $wheres[] = $where;
                        } else {
                            Log::error('未知查询条件参数', $where);
                        }
                    }
                }
                unset($whereInput, $where);
            }
            // 存在查询条件
            if (!empty($wheres)) {
                // 循环并添加查询条件
                foreach ($wheres as $where) {
                    // [$column, $operator, $value] = $where;
                    @[$column, $operator, $value, $boolean] = $where;
                    // $column, $operator = null, $value = null, $boolean = 'and'
                    $this->search($column, $operator, $value, $boolean ?? 'and');
                }
            }
            unset($wheres, $where);
            //endregion
            return $this;
        });

        /**
         * 输入查询排序
         */
        Builder::macro('orderByInput', function (): Builder {
            /* @var $this Builder */
            //region 解析前端传递的排序规则
            // 现有排序规则
            $orders = $this->getQuery()->orders;
            // 清空现有排序
            $this->reorder();
            // 后添加的排序会替换前面添加的排序
            $orders = empty($orders) ? [] : array_combine(array_column($orders, 'column'), array_column($orders, 'direction'));
            // 请求携带 的 排序条件
            $orderInput = request()->input('order');
            if (!empty($orderInput)) {
                // 解出排序数组
                if (!is_array($orderInput)) $orderInput = explode(',', $orderInput);
                foreach ($orderInput as $key => $order) {
                    $column = '';
                    $direction = '';
                    if (is_int($key)) {
                        if (is_string($order)) {
                            // 匹配 +/- 和 asc/desc
                            preg_match('/([^\s+-]+)\s?([+\-]|(([Dd][Ee]|[Aa])[Ss][Cc]))?/', $order, $matches);
                            // 排序字段
                            $column = $matches[1];
                            // 排序顺序
                            $direction = strtolower($matches[2] ?? 'asc');
                            unset($matches);
                        } else if (is_array($order)) {
                            [$column, $direction] = $order;
                        } else {
                            Log::error('未知排序参数', ['key' => $key, 'order' => $order]);
                        }
                    } else if (is_string($key)) {
                        // 排序字段
                        $column = $key;
                        // 排序顺序
                        $direction = $order;
                    } else {
                        Log::error('未知排序参数', ['key' => $key, 'order' => $order]);
                    }
                    $column = trim($column);
                    $direction = match (trim($direction)) {
                        '-', 'desc' => 'desc',
                        default => 'asc',
                    };
                    if (!empty($column) && !empty($direction)) $orders[$column] = $direction;
                }
                unset($orderInput, $key, $order, $column, $direction);
            }
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

        /**
         * 根据参数，嵌套查询函数
         */
        Builder::macro('search', function ($search_fields, $operator = null, $value = null, $boolean = 'and'): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_fields)) return $this;
            $args = func_get_args();
            $value = $args[2] ?? $args[1];
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));
            unset($args[0]);
            if (is_string($search_fields)) $search_fields = explode('.', $search_fields);
            // 取出查询字段数组中首个字段
            $search_field = array_shift($search_fields);
            if (!empty($search_fields)) {// 查询字段数组中仍然有值，表示本次为关联查询
                /* @var $RelationModel BelongsTo|HasMany|HasOne */
                $RelationModel = $this->getModel()->$search_field();
                if ($RelationModel instanceof BelongsTo) {
                    $where_key = $RelationModel->getForeignKeyName();
                    $select_key = $RelationModel->getOwnerKeyName();
                } else {
                    $where_key = $RelationModel->getLocalKeyName();
                    $select_key = $RelationModel->getForeignKeyName();
                }
                // 重复查询下一个字段或表名
                return $this->whereIn($where_key, $RelationModel->getModel()->select($select_key)->search($search_fields, ...$args));
                // $this->whereHas($search_field, function (Builder $query) use ($search_fields, $args) {
                //     $query->search($search_fields, ...$args);
                // });
            }
            // 单一字段，非关联，最后一次查询
            $operator_method = Str::camel('where_' . str_replace(' ', '_', $operator));
            // if ($operator == 'like') return $this->whereLike($search_field, $value, $boolean);
            return method_exists($this, $operator_method) ? call_user_func([$this, $operator_method], $search_field, $value, $boolean) : $this->where($search_field, $operator, $value, $boolean);
        });

        /**
         * 根据参数，模糊查询指定字段函数
         */
        Builder::macro('whereLike', function (string $search_field, string $value, $boolean = 'and'): Builder {
            /* @var $this Builder */
            // 搜索字段为空 或者 模糊查询值为空 不进行查询
            if (empty($search_field) || empty($value)) return $this;
            return $this->where($search_field, 'like', "%{$value}%", $boolean);
        });

        /**
         * 根据请求，嵌套查询函数
         */
        Builder::macro('searchInput', function (string|null $search_field, $operator = null, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $input_field = $args[2] ?? $args[1] ?? $args[0];
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));
            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(isset($search_data), fn(Builder $query) => $query->search($search_field, $operator, $search_data));
        });

        /**
         * 根据请求，查询指定字段函数
         */
        Builder::macro('whereInput', function (string $search_field, $operator = null, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $input_field = $args[2] ?? $args[1] ?? $args[0];
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));

            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(isset($search_data), fn(Builder $query) => $query->search($search_field, $operator, $search_data));
        });

        /**
         * 根据请求，查询指定字段函数
         */
        Builder::macro('whereInputOptionItem', function (string $search_field, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            // 获取搜索值
            $option_item_id = request()->input($input_field ?? $search_field);
            return $this->when(isset($option_item_id) && !empty($option_item_id), function (Builder $query) use ($search_field, $option_item_id) {
                $option_item_value = SystemOptionItem::getValue($option_item_id);
                $arr = explode('-', $option_item_value);
                if (count($arr) > 1) return $query->whereIn($search_field, $arr);
                return $query->where($search_field, '=', $option_item_value);
            });
        });

        /**
         * 根据请求，模糊查询指定字段函数
         */
        Builder::macro('whereInputLike', function (string $search_field, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(!empty($search_data), fn(Builder $query) => $query->whereLike($search_field, $search_data));
        });

        /**
         * 根据参数，查询时间戳
         */
        Builder::macro('whereTimestamp', function ($search_field, $operator = null, $value = null, $boolean = 'and'): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $value = $args[2] ?? $args[1];
            if (!empty($value)) $value = Tools::timeToCarbon($value)->timestamp;
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));
            // if ($operator == 'like') return $this->whereLike($search_field, $value, $boolean);
            return $this->search($search_field, $operator, $value, $boolean);
        });

        /**
         * 根据用户ID查询
         */
        Builder::macro('whereUserId', function ($search_field = 'user_id', $value = null, $boolean = 'and'): Builder {
            /* @var $this Builder */
            $args = func_get_args();
            if (count($args) && count($args) < 2 && is_numeric($args[0])) {
                $search_field = 'user_id';
                $value = $args[0];
            }
            if (empty($value)) $value = Tools::auth()->id('user');
            return $this->where($search_field, '=', $value, $boolean);
        });

        /**
         * 根据参数，查询一条数据或抛出
         */
        Builder::macro('firstOrThrow', function (string $thr_str = '数据异常') {
            /* @var $this Builder */
            return $this->firstOr(fn() => throw new BadRequestException($thr_str));
        });

        /**
         * 根据主键，查询一条数据或抛出
         */
        Builder::macro('findOrThrow', function ($id, array|string $columns = ['*'], ?string $thr_str = null) {
            /* @var $this Builder */
            $args = func_get_args();
            $thr_str = $args[2] ?? $args[1] ?? '数据异常';
            $columns = isset($args[2]) ? $args[1] : ['*'];
            return $this->findOr($id, $columns, fn() => throw new BadRequestException($thr_str));
        });

        /*Builder::macro('name', function () {
        });*/
    }

}
