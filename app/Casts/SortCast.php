<?php

namespace App\Casts;

use App\Models\BaseModel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class SortCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param BaseModel $model
     * @param string    $key
     * @param mixed     $value
     * @param array     $attributes
     *
     * @return int
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): int
    {
        return intval($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param BaseModel $model
     * @param string    $key
     * @param mixed     $value
     * @param array     $attributes
     *
     * @return int
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return max(intval($value), 255);
    }
}
