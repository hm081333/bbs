<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Carbon;

class Timestamp implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return empty($value) ? null : ($value instanceof \Carbon\Carbon ? $value : (filter_var($value, FILTER_VALIDATE_INT) !== false ? (strlen((string)$value) === 10 ? Carbon::createFromTimestamp($value) : throw new \App\Exceptions\Server\Exception('时间戳格式错误')) : Carbon::parse($value)));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return empty($value) ? null : (filter_var($value, FILTER_VALIDATE_INT) !== false ? (strlen((string)$value) === 10 ? $value : throw new \App\Exceptions\Server\Exception('时间戳格式错误')) : ($value instanceof \Carbon\Carbon ? $value->timestamp : Carbon::parse($value)->timestamp));
    }
}
