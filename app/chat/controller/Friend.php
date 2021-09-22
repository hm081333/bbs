<?php
declare (strict_types=1);

namespace app\chat\controller;

use app\BaseController;
use Overtrue\Pinyin\Pinyin;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;

class Friend extends BaseController
{
    /**
     * 列表数据
     * @desc      获取列表数据
     * @return \think\response\Json    数据列表
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        // 查询字段
        $field = $this->request->param('field', '*');
        // 查询条件
        $where = $this->request->param('where', []);

        $user = $this->request->getCurrentUser(true);
        $pinyin = new Pinyin();
        $where['user_id'] = $user['id'];
        $list = $this->modelFriend->with([
            'friend'
        ])->field('friend_id')->where($where)->select();
        $list = $list->map(function (\app\model\Friend $row) use ($pinyin) {
            return [
                'user_id' => $row->friend['id'],
                'nick_name' => $row->friend['nick_name'],
                'logo' => $row->friend['logo'],
                'pinyin' => $pinyin->abbr($row->friend['nick_name'], PINYIN_NO_TONE | PINYIN_KEEP_NUMBER | PINYIN_KEEP_ENGLISH),
            ];
        });
        return success('', $list);
    }
}
