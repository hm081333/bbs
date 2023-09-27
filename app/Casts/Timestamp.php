<?php

namespace App\Casts;

use App\Exceptions\Server\Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Timestamp implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return Carbon
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return empty($value) ? null : ($value instanceof \Carbon\Carbon ? $value : (filter_var($value, FILTER_VALIDATE_INT) !== false ? (strlen((string)$value) === 10 ? Carbon::createFromTimestamp($value) : throw new Exception('时间戳格式错误')) : Carbon::parse($value)));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return empty($value) ? null : (filter_var($value, FILTER_VALIDATE_INT) !== false ? (strlen((string)$value) === 10 ? $value : throw new Exception('时间戳格式错误')) : ($value instanceof \Carbon\Carbon ? $value->timestamp : Carbon::parse($value)->timestamp));
    }
}
