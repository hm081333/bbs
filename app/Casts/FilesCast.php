<?php

namespace App\Casts;

use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class FilesCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param BaseModel $model
     * @param string    $key
     * @param mixed     $value
     * @param array     $attributes
     *
     * @return array
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return array_map(function ($path) {
            return [
                'path' => $path,
                'url' => Tools::storageAsset($path),
            ];
        }, is_array($value) ? $value : (Tools::jsonDecode($value) ?? []));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param BaseModel $model
     * @param string    $key
     * @param array     $value
     * @param array     $attributes
     *
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (Arr::isAssoc($value)) $value = [$value];
        $save_value = [];
        foreach ($value as $item) {
            if (!empty($item) && isset($item['path']) && !empty($item['path'])) $save_value[] = $item['path'];
        }
        return Tools::jsonEncode($save_value);
    }
}
