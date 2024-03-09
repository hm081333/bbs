<?php

namespace App\Casts;

use App\Models\BaseModel;
use App\Models\System\SystemOptionItem;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class OptionItemCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param BaseModel $model
     * @param string    $key
     * @param mixed     $value
     * @param array     $attributes
     *
     * @return mixed
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        $value = (int)$value;
        $append_key = "{$key}_text";
        $model->accept($append_key);
        $model->setAttribute($append_key, empty($value) ? '' : SystemOptionItem::getValue($value));
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param BaseModel $model
     * @param string    $key
     * @param mixed     $value
     * @param array     $attributes
     *
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return (int)$value;
    }
}
