<?php


namespace Common\Api;

use PhalApi\Api;

/**
 * 系统基础服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Base extends Api
{
    use Common;

    protected $session_user = null;
    protected $session_admin = null;

    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = $this->commonRules();
        $rules['index'] = [
            'username' => ['name' => 'username', 'default' => 'PhalApi', 'desc' => '用户名'],
        ];
        return $rules;
    }

    /**
     * 基础服务构造函数
     */
    public function __construct()
    {
    }

    /**
     * 用户身份验证
     * @desc 可由开发人员根据需要重载，此通用操作一般可以使用委托或者放置在应用接口基类
     * @throws \Library\Exception\BadRequestException
     */
    protected function userCheck()
    {
        parent::userCheck();
        if (!IS_CLI) {
            switch (MODULE) {
                case 'bbs':
                    $this->session_user = \Common\Domain\User::getCurrentUser();// 获取登录状态
                    break;
                case 'sign':
                    $this->session_user = \Common\Domain\User::getCurrentUser(true);// 获取登录状态
                    break;
                case 'admin':
                    $this->session_admin = \Common\Domain\Admin::getCurrentAdmin(true);// 获取登录状态
                    break;
                case 'common':
                    $this->session_user = \Common\Domain\User::getCurrentUser();// 获取会员登录状态
                    $this->session_admin = \Common\Domain\Admin::getCurrentAdmin();// 获取管理员登录状态
                    break;
            }
        }
    }

    /**
     * 默认接口服务
     * @return array
     * @ignore
     */
    public function index()
    {
        return [
            'title' => 'Hello ' . $this->username,
            'version' => PHALAPI_VERSION,
            'time' => $_SERVER['REQUEST_TIME'],
        ];
    }


}
