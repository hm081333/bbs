<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class User extends Model
{
    protected $createTime = 'reg_time';

    //region 模型关联
    public function topic()
    {
        return $this->hasMany(Topic::class);
    }

    public function reply()
    {
        return $this->hasMany(Reply::class);
    }
    //endregion

    //region 获取器
    public function getLogoAttr($value)
    {
        return empty($value) ? '' : res_path($value);
    }

    public function getPreviousLogoAttr($value)
    {
        return empty($value) ? '' : res_path($value);
    }

    public function getSexNameAttr($value)
    {
        return sexName($value);
    }
    //endregion

    //region 修改器
    public function setPasswordAttr($value, $data)
    {
        $this->set('a_pwd', opensslEncrypt($value));
        return pwd_hash($value);
        // $this->set('password', pwd_hash($value));
    }

    public function setLogoAttr($value, $data)
    {
        $previous_logo = $data['logo'];
        if ($value == res_path($data['previous_logo'])) {
            $value = $data['previous_logo'];
        }
        if (!empty($data['previous_logo']) && $value != $data['previous_logo']) {
            // 删除更早之前的头像
            @unlink(public_path($data['previous_logo']));
        }
        $this->set('previous_logo', $previous_logo);
        return $value;
    }

    //endregion

    public function getUserInfo()
    {
        if ($this->isEmpty()) return [];
        return [
            'id' => $this->getAttr('id'),
            'user_name' => $this->getAttr('user_name'),
            'email' => $this->getAttr('email'),
            'logo' => $this->getAttr('logo'),
            'previous_logo' => $this->getAttr('previous_logo'),
            'nick_name' => $this->getAttr('nick_name'),
            'real_name' => $this->getAttr('real_name'),
            'birth_time' => $this->getAttr('birth_time'),
            'sex' => $this->getAttr('sex'),
            'sex_name' => sexName($this->getAttr('sex')),
            'signature' => $this->getAttr('signature'),
        ];
    }
}
