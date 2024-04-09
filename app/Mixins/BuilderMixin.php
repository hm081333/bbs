<?php

namespace App\Mixins;

use App\Exceptions\Request\BadRequestException;
use App\Models\System\SystemOptionItem;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BuilderMixin
{
    /**
     * 分页函数
     *
     * @return \Closure
     */
    public function getPage()
    {
        return function ($columns = ['*']): array {
            /* @var $this Builder */
            // 每页显示数量
            $pageSize = request()->input('limit', $this->getModel()->getPerPage());
            // 页码
            $pageNo = request()->input('page', 1);
            // 搜索字段
            $search_field = request()->input('search_field');
            // 搜索关键字
            $this->searchInput($search_field, 'like', 'search_keyword');
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
        };
    }

    /**
     * 排序函数
     *
     * @return \Closure
     */
    public function orderByInput()
    {
        return function (): Builder {
            /* @var $this Builder */
            //region 解析前端传递的排序规则
            // 现有排序规则
            $orders = $this->getQuery()->orders;
            // 后添加的排序会替换前面添加的排序
            $orders = empty($orders) ? [] : array_combine(array_column($orders, 'column'), array_column($orders, 'direction'));
            // 请求携带 的 排序条件
            $orderBys = request()->input('orderBy');
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
        };
    }

    /**
     * 根据参数，嵌套查询函数
     *
     * @return \Closure
     */
    public function search()
    {
        return function ($search_fields, $operator = null, $value = null, $boolean = 'and'): Builder {
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
            if ($operator == 'like') return $this->whereLike($search_field, $value, $boolean);
            return $this->where($search_field, $operator, $value, $boolean);
        };
    }

    /**
     * 根据请求，嵌套查询函数
     *
     * @return \Closure
     */
    public function searchInput()
    {
        return function (string|null $search_field, $operator = null, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $input_field = $args[2] ?? $args[1] ?? $args[0];
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));
            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(isset($search_data), fn(Builder $query) => $query->search($search_field, $operator, $search_data));
        };
    }

    /**
     * 根据请求，查询指定字段函数
     *
     * @return \Closure
     */
    public function whereInput()
    {
        return function (string $search_field, $operator = null, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $input_field = $args[2] ?? $args[1] ?? $args[0];
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));

            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(isset($search_data), fn(Builder $query) => match ($operator) {
                'like' => $query->whereLike($search_field, $operator, $search_data),
                'in' => $query->whereIn($search_field, $search_data),
                default => $query->where($search_field, $operator, $search_data),
            });
        };
    }

    /**
     * 根据请求，查询指定字段函数
     *
     * @return \Closure
     */
    public function whereInputOptionItem()
    {
        return function (string $search_field, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            // 获取搜索值
            $option_item_id = request()->input($input_field ?? $search_field);
            return $this->when(isset($option_item_id) && !empty($option_item_id), function (Builder $query) use ($search_field, $option_item_id) {
                $option_item_value = SystemOptionItem::getValue($option_item_id);
                $arr = explode('-', $option_item_value);
                if (count($arr) > 1) return $query->whereIn($search_field, $arr);
                return $query->where($search_field, $option_item_value);
            });
        };
    }

    /**
     * 根据请求，模糊查询指定字段函数
     *
     * @return \Closure
     */
    public function whereInputLike()
    {
        return function (string $search_field, $input_field = null): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            // 获取搜索值
            $search_data = request()->input($input_field ?? $search_field);
            return $this->when(!empty($search_data), fn(Builder $query) => $query->whereLike($search_field, $search_data));
        };
    }

    /**
     * 根据参数，模糊查询指定字段函数
     *
     * @return \Closure
     */
    public function whereLike()
    {
        return function (string $search_field, string $value, $boolean = 'and'): Builder {
            /* @var $this Builder */
            // 搜索字段为空 或者 模糊查询值为空 不进行查询
            if (empty($search_field) || empty($value)) return $this;
            return $this->where($search_field, 'like', "%{$value}%", $boolean);
        };
    }

    /**
     * 根据参数，查询时间戳
     *
     * @return \Closure
     */
    public function whereTimestamp()
    {
        return function ($search_field, $operator = null, $value = null, $boolean = 'and'): Builder {
            /* @var $this Builder */
            // 搜索字段 为空 不进行查询
            if (empty($search_field)) return $this;
            $args = func_get_args();
            $value = $args[2] ?? $args[1];
            if (!empty($value)) $value = Tools::timeToCarbon($value)->timestamp;
            $operator = trim(strtolower(isset($args[2]) ? $args[1] : '='));
            if ($operator == 'like') return $this->whereLike($search_field, $value, $boolean);
            return $this->where($search_field, $operator, $value, $boolean);
        };
    }

    /**
     * 根据用户ID查询
     *
     * @return \Closure
     */
    public function whereUserId()
    {
        return function ($search_field = 'user_id', $value = null, $boolean = 'and'): Builder {
            /* @var $this Builder */
            $args = func_get_args();
            if (count($args) && count($args) < 2 && is_numeric($args[0])) {
                $search_field = 'user_id';
                $value = $args[0];
            }
            if (empty($value)) $value = Tools::auth()->id('user');
            return $this->where($search_field, '=', $value, $boolean);
        };
    }

    /**
     * 根据参数，查询一条数据或抛出
     *
     * @return \Closure
     */
    public function firstOrThrow()
    {
        return function (string $thr_str = '数据异常') {
            /* @var $this Builder */
            return $this->firstOr(fn() => throw new BadRequestException($thr_str));
        };
    }

    /**
     * 根据主键，查询一条数据或抛出
     *
     * @return \Closure
     */
    public function findOrThrow()
    {
        return function ($id, array|string $columns = ['*'], ?string $thr_str = null) {
            /* @var $this Builder */
            $args = func_get_args();
            $thr_str = $args[2] ?? $args[1] ?? '数据异常';
            $columns = isset($args[2]) ? $args[1] : ['*'];
            return $this->findOr($id, $columns, fn() => throw new BadRequestException($thr_str));
        };
    }

}
