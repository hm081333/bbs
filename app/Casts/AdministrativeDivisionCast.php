<?php

namespace App\Casts;

use App\Models\BaseModel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AdministrativeDivisionCast implements CastsAttributes
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
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        $value = (int)$value;
        $append_key = "{$key}_text";
        $append_attr_key = "{$key}_attr_text";
        $model->accept([$append_key, $append_attr_key]);
        $model->setAttribute($append_key, empty($value) ? '' : \App\Models\System\AdministrativeDivision::getValue($value, 'name'));
        $model->setAttribute($append_attr_key, empty($value) ? '' : \App\Models\System\AdministrativeDivision::getValue($value, 'attr'));
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
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return (int)$value;
    }
}
