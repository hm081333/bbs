<?php

namespace App\Casts;

use App\Models\BaseModel;
use App\Models\OptionItem;
use App\Utils\Tools;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class OptionItems implements CastsAttributes
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
        $append_key = "{$key}_texts";
        $model->accept($append_key);
        $texts = [];
        $value = Tools::jsonDecode($value) ?? [];
        foreach ($value as $item) {
            $texts[] = empty($item) ? '' : OptionItem::getValue($item);
        }
        $model->setAttribute($append_key, $texts);
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
        $save_value = [];
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!empty($item)) {
                    $save_value[] = (int)$item;
                }
            }
            unset($value, $item);
        } else {
            $save_value[] = $value;
        }
        return Tools::jsonEncode($save_value);
    }
}
