<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class OptionItemRule implements ValidationRule
{
    /**
     * 选项编码
     *
     * @var bool|string
     */
    private $option_code;

    /**
     * Create a new rule instance.
     *
     * @param bool|string $option_code 选项编码
     */
    public function __construct($option_code = false)
    {
        $this->option_code = is_string($option_code) ? trim($option_code) : $option_code;
    }

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
            $fail('validation.option_item')->translate();
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
        return $value === null || \App\Models\System\SystemOptionItem::where('id', $value)
                ->when(!empty($this->option_code), function (\Illuminate\Database\Eloquent\Builder $query) use ($attribute) {
                    $query->where('code', is_bool($this->option_code) ? $attribute : $this->option_code);
                })
                ->select(['id'])
                ->exists();
    }

}
