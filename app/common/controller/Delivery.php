<?php
declare (strict_types=1);

namespace app\common\controller;

use app\BaseController;
use ErrorException;
use library\exception\BadRequestException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\Request;
use think\response\Json;

class Delivery extends BaseController
{
    /**
     * 列表数据
     * @desc      获取列表数据
     * @return Json    数据列表
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
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
        // 排序方式
        $order = $this->request->param('order', 'id desc');
        $user = $this->request->getCurrentUser(true);
        $where['user_id'] = $user['id'];
        $total = $this->modelDelivery->where($where)->count();
        $rows = [];
        if ($total) {
            $rows = $this->modelDelivery
                ->where($where)
                ->field($field)
                ->order($order)
                ->limit($offset, $limit)
                ->select()
                ->append([
                    'state_name',
                ])
                ->toArray();
        }
        return success('', ['total' => $total, 'rows' => $rows, 'offset' => $offset, 'limit' => $limit]);
    }

    /**
     * 物流详情数据
     * @return Json
     * @throws BadRequestException
     * @throws ErrorException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function infoData()
    {
        // 查询ID
        $id = $this->request->param('id/d', 0);
        // 获取物流信息
        $delivery = $this->modelDelivery->where(['id' => $id])->find();
        if (empty($delivery)) throw new BadRequestException('找不到该物流信息');
        // 该物流已经结束 且 有历史信息，返回历史物流信息
        if (!empty($delivery['end_time']) && !empty($delivery['last_message'])) return success('获取成功', $delivery['last_message']);
        // 调取接口 获取物流最新信息
        $logistics = curl()->json_get('http://www.kuaidi100.com/query?type=' . $delivery['code'] . '&postid=' . $delivery['sn']);
        // 返回物流错误
        if ($logistics['status'] != 200) {
            // 没有历史查询信息
            if (empty($delivery['last_message'])) throw new ErrorException($logistics['message']);
            // 返回历史物流信息
            return success('获取成功', $delivery['last_message']);
        }
        // 物流公司名称
        $logistics['log_name'] = $delivery['log_name'];
        // 状态名称
        $logistics['state_name'] = $this->modelDelivery->getStateName($logistics['state']);
        // 本次查询信息 保存到数据库
        $delivery['last_message'] = $logistics;
        // 更新最后查询时间
        $delivery['last_time'] = time();
        if ($delivery['state'] != $logistics['state'] && empty($delivery['end_time'])) {// 物流状态 与 数据库保存的状态 不一致 且 物流未结束
            // 更新最新物流状态
            $delivery['state'] = $logistics['state'];
            if ($logistics['state'] == 3) {
                // 更新物流结束时间
                $delivery['end_time'] = strtotime($logistics['data'][0]['time']);
            }
        }
        $delivery->save();
        // 返回物流信息
        return success('获取成功', $logistics);
    }

    /**
     * 添加、编辑物流信息
     * @return Json
     * @throws BadRequestException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function doInfo()
    {
        // ID
        $id = $this->request->post('id/d', 0);
        // 快递单号
        $sn = $this->request->post('sn');
        // 物流公司编码
        $code = $this->request->post('code');
        // 备注
        $memo = $this->request->post('memo');
        // 当前登陆用户信息
        $user = $this->request->getCurrentUser(true);
        $logistics = $this->modelLogistics->field('code,name')->where(['code' => $code])->find();
        if (!$logistics) throw new BadRequestException('不存在该快递公司代码，请联系管理员');
        $delivery = $this->modelDelivery->where(['id' => $id])->findOrEmpty();
        if ($delivery->isEmpty()) {
            // 所属会员
            $delivery->user_id = $user['id'];
        }
        if ($delivery->code != $logistics['code'] || $delivery->sn != $sn) {
            // 上次查询信息
            $delivery->last_message = '';
            // 最后查询时间
            $delivery->last_time = '';
            // 物流状态
            $delivery->state = 0;
            // 物流结束时间
            $delivery->end_time = 0;
            // 物流公司编号
            $delivery->code = $logistics['code'];
            // 物流公司名称
            $delivery->log_name = $logistics['name'];
            // 物流单号
            $delivery->sn = $sn;
        }
        // 备注
        $delivery->memo = $memo;
        if (!$id) {
            $logistics->used = Db::raw('used + 1');
            $logistics->save();
        }
        $delivery->save();
        return success('添加成功');
    }
}
