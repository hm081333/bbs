<?php
declare (strict_types=1);

namespace app\sign\controller;

use app\BaseController;
use library\exception\InternalServerErrorException;
use think\Request;

class JdSign extends BaseController
{
    /**
     * 列表数据
     * @desc      获取列表数据
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        $user = $this->request->getCurrentUser(true);
        $this->where['user_id'] = $user['id'];
        $returnData = parent::listData();
        $returnData['data']['rows'] = $returnData['data']['rows']->map(function (\app\model\JdSign $row) {
            $row['sign_key_name'] = $row->jd_sign_item['name'];
            $row['sign_key_status'] = $row->jd_sign_item['status'];
            unset($row->jd_sign_item);
            return $row;
        });
        return $returnData;
    }

    /**
     * 列表数据 不分页
     * @desc      获取列表数据 不分页
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function allListData()
    {
        $user = $this->request->getCurrentUser(true);
        $this->where['user_id'] = $user['id'];
        return parent::allListData();
    }

    /**
     * 更新 会员选择的 京东签到项目
     * @param int $jd_user_id 京东用户ID
     * @param array $open_signs 选择的签到项
     */
    public function doInfo(int $jd_user_id, array $open_signs = [])
    {

        $user = $this->request->getCurrentUser(true);
        $user_id = intval($user['id']);

        $jd_user_info = $this->modelJdUser->where([
            ['id', '=', $jd_user_id],
            ['user_id', '=', $user_id],
        ])->find();
        if (!$jd_user_info) throw new InternalServerErrorException(T('找不到该京东用户'));
        $update_all_where = [
            ['user_id', '=', $user_id],
            ['jd_user_id', '=', $jd_user_info['id']],
            ['status', '=', 1],
        ];
        $update_all_data = [
            'status' => 0,
            'edit_time' => time(),
        ];
        if (!empty($open_signs)) {
            foreach ($open_signs as $sign_key) {
                $sign_info = $this->modelJdSign->where([
                    ['user_id', '=', $user_id],
                    ['jd_user_id', '=', $jd_user_info['id']],
                    ['sign_key', '=', $sign_key],
                ])->findOrEmpty();
                // 不存在该签到条目
                if ($sign_info->isEmpty()) {
                    $sign_info->appendData([
                        'user_id' => $user_id,
                        'jd_user_id' => $jd_user_info['id'],
                        'sign_key' => $sign_key,
                        'status' => 1,
                        'add_time' => time(),
                        'edit_time' => time(),
                    ]);
                    $sign_info->save();
                } else {
                    if ($sign_info->status != 1) {
                        $sign_info->appendData([
                            'status' => 1,
                            'edit_time' => time()
                        ]);
                        $sign_info->save();
                    }
                }
            }
            $update_all_where[] = ['sign_key', 'NOT IN', $open_signs];
        }
        $this->modelJdSign->where($update_all_where)->update($update_all_data);
        return success('设置成功');
    }

    /**
     * 更改签到状态
     * @param int $id
     * @param $status
     */
    public function changeSignStatus(int $id, $status)
    {
        //'id' => ['name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '签到ID'],
        //'status' => ['name' => 'status', 'type' => 'enum', 'range' => [0, 1], 'require' => true, 'desc' => '签到状态'],
        $this->modelJdSign->where([
            ['id', '=', $id]
        ])->update([
            'status' => intval($status)
        ]);
        return success('操作成功');
    }
}
