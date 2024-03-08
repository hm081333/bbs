<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class File implements Rule
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
        return empty($value['path']) || \App\Models\System\SystemFile::where('path', $value['path'])
                ->select(['id'])
                ->exists();
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
