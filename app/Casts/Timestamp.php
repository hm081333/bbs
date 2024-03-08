<?php

namespace App\Casts;

use App\Exceptions\Server\Exception;
use App\Utils\Tools;
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
     * @return \Carbon\Carbon
     */
    public function get($model, string $key, mixed $value, array $attributes)
    {
        return Tools::timeToCarbon($value);
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
    public function set($model, string $key, mixed $value, array $attributes)
    {
        $value = Tools::timeToCarbon($value);
        if ($value) return $value->timestamp;
        return null;
    }
}
