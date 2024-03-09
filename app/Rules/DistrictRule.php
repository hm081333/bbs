<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class DistrictRule implements ValidationRule
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
        return \App\Models\System\AdministrativeDivision::where('id', $value)
            ->where('level', 2)
            ->select('id')
            ->exists();
    }

}
