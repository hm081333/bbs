<?php

namespace App\Http\Controllers\Home\User;

use App\Utils\Tools;
use Illuminate\Support\Facades\Response;

class FeedbackController extends \App\Http\Controllers\BaseController
{
    protected function getRules()
    {
        return [
            'add' => [
                'content' => ['desc' => '留言内容', 'string', 'required'],
                'contact' => ['desc' => '联系方式', 'string'],
                'images' => ['desc' => '图片', 'files'],
            ],
        ];
    }

    /**
     * 添加反馈
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Request\BadRequestException
     * @throws \App\Exceptions\Request\UnauthorizedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function add()
    {
        $user_id = Tools::auth()->id('user');
        $params = $this->getParams();
        $feedback = Tools::model()->UserUserFeedback->create([
            ...$params,
            'user_id' => $user_id,
        ]);
        // 刷新模型
        $feedback = $feedback->refresh();
        $feedback->logs()->create([
            'status' => $feedback->getAttribute('status'),
            'remark' => '用户提交',
        ]);
        return Response::api('提交成功');
    }

}
