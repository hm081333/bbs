<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

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
        if (empty($value)) return true;
        if (!is_array($value)) return false;
        if (Arr::isAssoc($value)) $value = [$value];
        $files = [];
        foreach ($value as $file) {
            if (!is_array($file) || !Arr::isAssoc($file)) return false;
            // 不能存在空路径
            if (empty($file['path'])) return false;
            // distinct 唯一
            if (in_array($file['path'], $files)) return false;
            // 合成数组用于校验文件是否都存在
            $files[] = $file['path'];
        }
        return \App\Models\System\SystemFile::whereIn('path', $files)
                ->select(['id'])
                ->count() === count($files);
    }

}
