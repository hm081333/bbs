<?php


namespace Common\Api;

use Common\Domain\Admin;
use Library\Exception\BadRequestException;
use PhalApi\Api;
use function Common\DI;

/**
 * 系统基础服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Base extends Api
{
    use \Library\Traits\Api;

    /**
     * 基础服务构造函数
     */
    public function __construct()
    {
        // DI()->logger->debug('调用API', DI()->request->getService());
    }

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
     * 接口参数规则
     * @return array
     */
    public function commonRules()
    {
        return [
            'listData' => [
                'offset' => ['name' => 'offset', 'type' => 'int', 'default' => 0, 'desc' => "开始位置"],
                'limit' => ['name' => 'limit', 'type' => 'int', 'default' => PAGE_NUM, 'desc' => '数量'],
                'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
                'where' => ['name' => 'where', 'type' => 'array', 'default' => [], 'desc' => '查询条件'],
                'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id desc', 'desc' => '排序方式'],
            ],
            'allListData' => [
                'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
                'where' => ['name' => 'where', 'type' => 'array', 'desc' => '查询条件'],
                'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id desc', 'desc' => '排序方式'],
            ],
            'infoData' => [
                'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => "查询ID"],
                'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
            ],
            'delInfo' => [
                'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => "删除ID"],
            ],
        ];
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
            'time' => $_SERVER['REQUEST_TIME'] ?? time(),
        ];
    }

    /**
     * 调用一个对象中不存在的方法
     * @param $name
     * @param $arguments
     */
    function __call($name, $arguments)
    {
        var_dump($name, $arguments);
        die;
        // 注意：$name的值区分大小写
        echo "Calling object method '$name' " . implode(', ', $arguments) . "\n";
    }

    /**
     * 读取一个对象中不存在的属性
     * @param string $name
     * @return array|mixed
     * @throws BadRequestException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'session_admin':
                return $this->Domain_Admin()::getCurrentAdmin(true);
                break;
            case 'session_user':
                return $this->Domain_User()::getCurrentUser(true);
                break;
            default:
                return parent::__get($name);
                break;
        }
    }

    /**
     * 管理员 领域层
     * @return Admin
     * @throws BadRequestException
     */
    protected function Domain_Admin()
    {
        return self::getDomain('Admin');
    }

    /**
     * 用户 领域层
     * @return \Common\Domain\User
     * @throws BadRequestException
     */
    protected function Domain_User()
    {
        return self::getDomain('User');
    }

    /**
     * 用户 缓存层
     * @return \Common\Cache\User
     * @throws BadRequestException
     */
    protected function Cache_User()
    {
        return self::getCache('User');
    }

    /**
     * 列表数据
     * @desc      获取列表数据
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        return $parent_func ?? self::getDomain()::getList($this->limit, $this->offset, $this->where, $this->field, $this->order);
    }

    /**
     * 列表数据 不分页
     * @desc      获取列表数据 不分页
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function allListData()
    {
        return $parent_func ?? self::getDomain()::getListByWhere($this->where, $this->field, $this->order);
    }

    /**
     * 详情数据
     * @desc      获取详情数据
     * @return array    数据数组
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        return $parent_func ?? self::getDomain()::getInfo($this->id, $this->field);
    }

    /**
     * 删除数据
     * @desc 删除数据
     * @throws BadRequestException
     */
    public function delInfo()
    {
        return $parent_func ?? self::getDomain()::delInfo($this->id);
    }

    /**
     * 用户身份验证
     * @desc 可由开发人员根据需要重载，此通用操作一般可以使用委托或者放置在应用接口基类
     * @throws BadRequestException
     */
    protected function userCheck()
    {
        parent::userCheck();
        if (!IS_CLI) {
            // 获取header中携带的Token
            $auth = DI()->request->getHeader('Auth', '');
            foreach (explode('|', urldecode($auth)) as $item) {
                $key = substr($item, 0, 32);
                $value = substr($item, 32);
                if ($key == ADMIN_TOKEN) {
                    $this->Domain_Admin()::$admin_token = $value;
                } else if ($key == USER_TOKEN) {
                    $this->Domain_User()::$user_token = $value;
                }
            }
        }
        switch (strtolower(DI()->request->getNamespace())) {
            case 'bbs':
                // 获取会员登录状态
                $this->Domain_User()::getCurrentUser();
                break;
            case 'sign':
                // 获取会员登录状态
                $this->Domain_User()::getCurrentUser(true);
                break;
            case 'admin':
                // 获取管理员登录状态
                $this->Domain_Admin()::getCurrentAdmin(true);
                break;
            case 'common':
                // 获取会员登录状态
                $this->Domain_User()::getCurrentUser();
                // 获取管理员登录状态
                $this->Domain_Admin()::getCurrentAdmin();
                break;
        }
    }

}
