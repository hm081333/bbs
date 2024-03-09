<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class ProvinceCityDistrictRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string   $attribute
     * @param mixed    $value
     * @param \Closure $fail
     *
     * @return void
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $fail('validation.exists')->translate();
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute 校验字段名
     * @param mixed  $value     字段传入参数
     *
     * @return bool
     */
    public function passes(string $attribute, mixed $value): bool
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

}
