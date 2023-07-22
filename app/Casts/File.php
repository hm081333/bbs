<?php

namespace App\Casts;

use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class File implements CastsAttributes
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
        return empty($value) ? null : [
            'path' => $value,
            'url' => Tools::storageAsset($value),
        ];
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
        return $value['path'] ?? '';
    }
}
