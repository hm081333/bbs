<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class FileRule implements ValidationRule
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
            // $fail('validation.exists')->translate();
            $fail('上传文件错误');
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
        if (!is_array($value) || !Arr::isAssoc($value)) return false;
        return empty($value['path']) || \App\Models\System\SystemFile::where('path', $value['path'])
                ->select(['id'])
                ->exists();
    }
}
