<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Files implements Rule
{
    /**
     * Determine if the validation rule passes.
     * @param string $attribute 校验字段名
     * @param mixed $value 字段传入参数
     * @return bool
     */
    public function passes($attribute, $value)
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
        return \App\Models\File::whereIn('path', $files)
            ->select(['id'])
            ->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '上传文件错误';
        return trans('validation.exists');
    }
}
