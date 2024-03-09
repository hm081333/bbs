<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class FilesRule implements ValidationRule
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
        if (!is_array($value)) return false;
        $files = [];
        foreach ($value as $file) {
            if (!is_array($file)) return false;
            if (empty($file['path'])) continue;
            // distinct 唯一
            if (in_array($file['path'], $files)) return false;
            $files[] = $file['path'];
        }
        return \App\Models\System\SystemFile::whereIn('path', $files)
            ->select(['id'])
            ->count();
    }

}
