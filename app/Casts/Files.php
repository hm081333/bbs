<?php

namespace App\Casts;

use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Files implements CastsAttributes
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
        $value = Tools::json_decode($value) ?? [];
        return array_map(function ($path) {
            return [
                'path' => $path,
                'url' => Tools::storageAsset($path),
            ];
        }, $value);
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
        foreach ($value as $item) {
            if (!empty($item) && isset($item['path']) && !empty($item['path'])) $save_value[] = $item['path'];
        }
        return Tools::json_encode($save_value);
    }
}
