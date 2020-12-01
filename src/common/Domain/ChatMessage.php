<?php

namespace Common\Domain;

use Library\Exception\BadRequestException;
use Library\Traits\Domain;
use Library\WorkerMan\Push;
use function Common\res_path;
use function Common\unix_formatter;

/**
 * 聊天消息 领域层
 * Class ChatMessage
 * @package Common\Domain
 * @author  LYi-Ho 2020-05-10 21:07:13
 */
class ChatMessage
{
    use Domain;

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
     * 聊天 领域层
     * @return \Common\Domain\Chat
     * @throws BadRequestException
     */
    protected function Domain_Chat()
    {
        return self::getDomain('Chat');
    }

    /**
     * 好友 领域层
     * @return \Common\Domain\Friend
     * @throws BadRequestException
     */
    protected function Domain_Friend()
    {
        return self::getDomain('Friend');
    }

    public function sendChatMessage($chat_id, $message, $send_time)
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        // var_dump($user);
        // var_dump($chat_id);
        // var_dump($message);
        // var_dump($send_time);
        $result = self::doUpdate([
            'chat_id' => $chat_id,
            'user_id' => $user['id'],
            'message' => $message,
            'add_time' => $send_time,
        ]);
        // var_dump($result);
        $message_info = [
            'message_id' => $result['id'],
            'message' => \Common\strToHtml($message),
            'type' => 'send',
            'user' => [
                'user_id' => $user['id'],
                'user_name' => $user['user_name'],
                'nick_name' => $user['nick_name'],
                'logo' => empty($user['logo']) ? '' : res_path($user['logo']),
            ],
            'add_time' => unix_formatter($send_time, true),
            'add_time_date' => unix_formatter($send_time),
            'add_time_unix' => $send_time,
        ];
        // var_dump($message_info);
        $this->sendChatMessageToClient($chat_id, $message_info);
        $this->Domain_Chat()::doUpdate([
            'id' => $chat_id,
            'last_time' => $send_time,
        ]);
        return $message_info;
    }

    public function sendChatMessageToClient($chat_id, $message_info)
    {
        $message_info['chat_id'] = $chat_id;
        $message_info['type'] = 'receive';
        $chat_info = $this->Domain_Chat()::getInfo($chat_id, 'user_ids');
        $chat_user_ids = explode(',', $chat_info['user_ids']);
        foreach ($chat_user_ids as $chat_user_id) {
            if ($chat_user_id == $message_info['user']['user_id']) {
                continue;
            }
            $chat_user_info = $this->Cache_User()->get($chat_user_id);
            if (!empty($chat_user_info)) {
                Push::pushMessage($chat_user_info['client_id'], $message_info);
            }
        }
        // var_dump($chat_user_ids);
        return true;
    }

}
