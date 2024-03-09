<?php

namespace App\Casts;

use App\Exceptions\Server\BaseServerException;
use App\Utils\Tools;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TimestampCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return \Carbon\Carbon
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return Tools::timeToCarbon($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        $value = Tools::timeToCarbon($value);
        if ($value) return $value->timestamp;
        return null;
    }
}
