<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ProvinceCityDistrict implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute 校验字段名
     * @param mixed  $value     字段传入参数
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) return false;
        if (!is_array($value)) $value = explode(',', $value);
        foreach ($value as $level => $id) {
            if (!\App\Models\System\AdministrativeDivision::where('id', $id)
                ->where('level', $level)
                ->select('id')
                ->exists()) return false;
        }
        return true;
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
