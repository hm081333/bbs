<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class City implements Rule
{
    /**
     * Determine if the validation rule passes.
     * @param string $attribute 校验字段名
     * @param mixed $value 字段传入参数
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return \App\Models\System\AdministrativeDivision::where('id', $value)
            ->where('level', 1)
            ->select('id')
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.exists');
    }
}
