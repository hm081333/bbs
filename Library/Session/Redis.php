<?php


namespace Library\Session;

use function Common\DI;

/**
 * SESSION Redis存储方式
 * Class Redis
 * @package Library\Session
 */
class Redis implements \SessionHandlerInterface
{
    //session-lifetime
    private $lifeTime;
    private $defaultLifeTime = 3600;

    public function open($savePath, $sessName)
    {
        // 存活时间
        $this->lifeTime = get_cfg_var("session.gc_maxlifetime");
        return true;
    }

    public function read($sessID)
    {
        // DI()->logger->debug("read|{$sessID}");
        $data = DI()->cache->get('session:' . $sessID);
        return $data ? $data : '';
    }

    public function write($sessID, $sessData)
    {
        // DI()->logger->debug("write|{$sessID}|{$this->lifeTime}", $sessData);
        // new session-expire-time
        DI()->cache->set('session:' . $sessID, $sessData, $this->lifeTime ?? $this->defaultLifeTime);
        return true;
    }

    public function getSessionId(string $client_id)
    {
        $data = DI()->cache->get('session_id:' . $client_id);
        return $data ? $data : null;
    }

    public function setSessionId(string $client_id, string $session_id)
    {
        // DI()->logger->debug("session_id|{$client_id}|{$session_id}|{$this->lifeTime}");
        // 暂定保存一小时
        DI()->cache->set('session_id:' . $client_id, $session_id, $this->lifeTime ?? $this->defaultLifeTime);
        return true;
    }

    public function delSessionId(string $client_id)
    {
        DI()->cache->delete('session_id:' . $client_id);
        return true;
    }

    public function clearSessionId()
    {
        // $res = DI()->cache->delete('session_id:*');
        DI()->cache->flushDB();
    }

    public function clearSession()
    {
        $res = DI()->cache->delete('session:*');
        var_dump($res);
        die;
    }

    public function destroy($sessID)
    {
        // DI()->cache->delete('session:' . $sessID);
        return true;
    }

    public function close()
    {
        return true;
    }

    public function gc($maxlifetime)
    {
        return true;
    }
}
