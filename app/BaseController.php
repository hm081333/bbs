<?php
declare (strict_types=1);

namespace app;

use app\middleware\Auth;
use think\App;
use think\Exception;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 * @property \app\model\Admin $modelAdmin Admin
 * @property \app\model\BaiDuId $modelBaiDuId BaiDuId
 * @property \app\model\Chat $modelChat Chat
 * @property \app\model\ChatMessage $modelChatMessage ChatMessage
 * @property \app\model\Common $modelCommon Common
 * @property \app\model\Cron $modelCron Cron
 * @property \app\model\Delivery $modelDelivery Delivery
 * @property \app\model\Friend $modelFriend Friend
 * @property \app\model\Ip $modelIp Ip
 * @property \app\model\JdSign $modelJdSign JdSign
 * @property \app\model\JdSignItem $modelJdSignItem JdSignItem
 * @property \app\model\JdSignLog $modelJdSignLog JdSignLog
 * @property \app\model\JdUser $modelJdUser JdUser
 * @property \app\model\Logistics $modelLogistics Logistics
 * @property \app\model\LogisticsCompany $modelLogisticsCompany LogisticsCompany
 * @property \app\model\Message $modelMessage Message
 * @property \app\model\Reply $modelReply Reply
 * @property \app\model\Setting $modelSetting Setting
 * @property \app\model\Subject $modelSubject Subject
 * @property \app\model\TieBa $modelTieBa TieBa
 * @property \app\model\Topic $modelTopic Topic
 * @property \app\model\User $modelUser User
 * Class BaseController
 * @package app
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var Request
     */
    protected $request;

    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        // 请求令牌支持
        Auth::class,
    ];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    public function __get($name)
    {
        if (strpos($name, 'model') !== false) {
            $model_class = config("model.{$name}");
            if (class_exists($model_class)) {
                $this->$name = new $model_class;
                return $this->$name;
            }
        }
        throw new Exception('非法调用不存在函数');
    }

    //region 基础接口
    public function index()
    {
        return success('数据格式', [
            'title' => 'Hello',
            'version' => $this->app->version(),
            'time' => $this->request->time() ?? time(),
        ]);
    }

    /**
     * @return \think\Model
     */
    private function getModel()
    {
        // 拆解当前使用的类名
        $classInfo = explode('\\', get_called_class());
        // 当前使用的类名
        $className = end($classInfo);
        $class = '\\app\\model\\' . $className;
        if ($classInfo == $class) {
            var_dump(123);
            die;
        }
        return new $class;
    }

    protected $where = [];

    /**
     * 列表数据
     * @desc      获取列表数据
     * @return \think\response\Json    数据列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        // 开始位置
        $offset = $this->request->param('offset/d', 0);
        // 数量
        $limit = $this->request->param('limit/d', 10);
        // 查询字段
        $field = $this->request->param('field', '*');
        // 查询条件
        $where = $this->request->param('where', []);
        $where = array_merge($where, $this->where);
        // 排序方式
        $order = $this->request->param('order', 'id desc');
        $total = $this->getModel()->where($where)->count();
        $rows = [];
        if ($total) {
            $rows = $this->getModel()->where($where)->field($field)->order($order)->limit($offset, $limit)->select();
        }
        $this->where = [];
        return success('', ['total' => $total, 'rows' => $rows, 'offset' => $offset, 'limit' => $limit]);
    }

    /**
     * 列表数据 不分页
     * @desc      获取列表数据 不分页
     * @return \think\response\Json    数据列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @exception 400 非法请求，参数传递错误
     */
    public function allListData()
    {
        // 查询字段
        $field = $this->request->param('field', '*');
        // 查询条件
        $where = $this->request->param('where', []);
        $where = array_merge($where, $this->where);
        // 排序方式
        $order = $this->request->param('order', 'id desc');
        $list = $this->getModel()->where($where)->field($field)->order($order)->select();
        $this->where = [];
        return success('', $list);
    }

    /**
     * 详情数据
     * @desc      获取详情数据
     * @return \think\response\Json    数据数组
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        // 查询ID
        $id = $this->request->param('id/d', 0);
        // 查询字段
        $field = $this->request->param('field', '*');
        return success('', $this->getModel()->where($id)->field($field)->find());
    }

    /**
     * 删除数据
     * @desc 删除数据
     * @return \think\response\Json
     */
    public function delInfo()
    {
        // 查询ID
        $id = $this->request->param('id/d', 0);
        return success('', $this->getModel()->where($id)->delete());
    }
    //endregion
}
