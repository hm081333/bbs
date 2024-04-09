<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class HtmlCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // return empty($value) ? null : html_entity_decode($value);
        return empty($value) ? null : htmlspecialchars_decode($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // return empty($value) ? null : htmlentities($value);
        return empty($value) ? null : htmlspecialchars($value);
    }
}
