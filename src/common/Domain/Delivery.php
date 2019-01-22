<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * 物流信息 领域层
 * Class Delivery
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Delivery
{
    use Common;
    
    /**
     * 获取物流状态码对应的物流状态信息
     * @param bool $state
     * @return array|mixed|string
     */
    public static function getStateName($state = FALSE)
    {
        $status = [
            '0' => '在途',
            '1' => '揽件',
            '2' => '疑难',
            '3' => '签收',
            '4' => '退签',
            '5' => '派件',
            '6' => '退回',
        ];
        if ($state === FALSE) {
            return $status;
        }
        if ($state === NULL) {
            // return '物流信息不存在';
            return '不存在';
        }
        return $status[$state] ?? '';
    }
    
    /**
     * 查询数据
     * @param int $limit 每次查询条数
     * @param int $offset 开始位置
     * @param array $where 查询条件
     * @param string $field 字段
     * @param string $order 排序
     * @return array a 返回结果集
     * @return int a.total 总条数
     * @return array a.rows 当前查询结果集
     */
    public static function getList($limit, $offset, $where = [], $field = '*', $order = 'id desc', $count = '*', $id = NULL)
    {
        $model = self::getModel();
        $list = $model->getList($limit, $offset, $where, $order, $field, $id, $count);
        array_walk($list['rows'], function (&$value) {
            $value = \Common\arr_unix_formatter($value);// 格式化数组中的时间戳
            $value['state_name'] = self::getStateName($value['state']);// 物流状态
            return $value;
        });
        return $list;
    }
    
    /**
     * 获取物流信息
     * @param $id
     * @return string|array
     * @throws \ErrorException
     * @throws \Exception\BadRequestException
     * @throws \PhalApi_Exception_BadRequest
     */
    public static function getDeliveryInfo($id)
    {
        $delivery = self::getInfo($id);// 获取物流信息
        if (empty($delivery)) {
            throw new \PhalApi_Exception_BadRequest(\PhalApi\T('找不到该物流信息'));
        }
        $delivery['last_message'] = unserialize($delivery['last_message']);// 历史查询信息
        if (!empty($delivery['end_time']) && !empty($delivery['last_message'])) {// 该物流已经结束 且 有历史信息
            return $delivery['last_message'];// 返回历史物流信息
        } else {
            $logistics = self::getDomain('Logistics')::queryLogistics($delivery['code'], $delivery['sn']);// 调取接口 获取物流最新信息
            $update = [];
            if ($logistics['status'] != 200) {// 返回物流错误
                if (empty($delivery['last_message'])) {// 没有历史查询信息
                    throw new \ErrorException(\PhalApi\T($logistics['message']));// 抛出错误信息
                } else {
                    return $delivery['last_message'];// 返回历史物流信息
                }
            }
            $logistics['log_name'] = $delivery['log_name'];// 物流公司名称
            $logistics['state_name'] = self::getStateName($logistics['state']);// 状态名称
            $update['last_message'] = serialize($logistics);// 本次查询信息 保存到数据库
            $update['last_time'] = NOW_TIME;// 更新最后查询时间
            if ($delivery['state'] != $logistics['state'] && empty($delivery['end_time'])) {// 物流状态 与 数据库保存的状态 不一致 且 物流未结束
                $update['state'] = $logistics['state'];// 更新最新物流状态
                if ($logistics['state'] == 3) {
                    //$update['end_time'] = NOW_TIME;
                    $update['end_time'] = strtotime($logistics['data'][0]['time']);// 更新物流结束时间
                }
            }
            $update['id'] = $delivery['id'];
            self::doUpdate($update);
        }
        \PhalApi\DI()->response->setMsg(\PhalApi\T('获取成功'));
        return $logistics;// 返回物流信息
    }
    
    /**
     * 添加、编辑物流信息
     * @param $data
     * @return bool
     * @throws \Exception\BadRequestException
     * @throws \Exception\InternalServerErrorException
     */
    public static function doInfo($data)
    {
        $user = self::getDomain('User')::getCurrentUser(TRUE);
        $logistics_model = self::getModel('Logistics');
        $log = $logistics_model->getInfo(['code' => $data['code']], 'code,name');
        if (!$log) {
            throw new \Exception\BadRequestException(\PhalApi\T('不存在该快递公司代码，请联系管理员'));
        }
        $insert_update = [];
        $insert_update['id'] = $data['id'];// 物流ID
        $insert_update['code'] = $log['code'];// 物流公司编号
        $insert_update['log_name'] = $log['name'];// 物流公司名称
        $insert_update['sn'] = $data['sn'];// 物流单号
        $insert_update['memo'] = $data['memo'];// 备注
        $insert_update['user_id'] = $user['id'];// 所属会员
        if (!$data['id']) {
            $insert_update['add_time'] = NOW_TIME;// 添加时间
            $insert_update['edit_time'] = NOW_TIME;// 编辑时间
            $update_log_used = $logistics_model->update($log['id'], ['used' => new \NotORM_Literal('used + 1')]);
            if ($update_log_used === FALSE) {
                throw new \Exception\InternalServerErrorException(\PhalApi\T('添加失败'));
            }
        } else {
            $insert_update['edit_time'] = NOW_TIME;// 编辑时间
        }
        self::doUpdate($insert_update);
        return TRUE;
    }
    
    
}
