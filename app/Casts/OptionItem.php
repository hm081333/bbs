<?php

namespace App\Casts;

use App\Models\BaseModel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class OptionItem implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param BaseModel $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $value = (int)$value;
        $append_key = "{$key}_text";
        $model->accept($append_key);
        $model->setAttribute($append_key, empty($value) ? '' : \App\Models\OptionItem::getValue($value));
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param BaseModel $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return (int)$value;
    }
}
