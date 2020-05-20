<?php


namespace Library\Session;

/**
 * SESSION 存储
 * abstract class Basic
 * @package Library\Session
 */
abstract class Basic implements \SessionHandlerInterface
{
    //session-lifetime
    private $lifeTime;

    abstract protected function cache();

    public function open($savePath, $sessName)
    {
        // 存活时间
        $this->lifeTime = get_cfg_var("session.gc_maxlifetime");
        return true;
    }

    public function read($sessID)
    {
        // DI()->logger->debug("read|{$sessID}");
        $data = $this->cache()->get('session:' . $sessID);
        return $data ? $data : '';
    }

    public function write($sessID, $sessData)
    {
        // DI()->logger->debug("write|{$sessID}|{$this->lifeTime}", $sessData);
        // new session-expire-time
        if (IS_CLI) {
            $this->cache()->set('session:' . $sessID, $sessData, 86400);
        } else {
            $this->cache()->set('session:' . $sessID, $sessData, $this->lifeTime);
        }
        return true;
    }

    public function getSessionId(string $client_id)
    {
        $data = $this->cache()->get('session_id:' . $client_id);
        return $data ? $data : null;
    }

    public function setSessionId(string $client_id, string $session_id)
    {
        // DI()->logger->debug("session_id|{$client_id}|{$session_id}|{$this->lifeTime}");
        if (IS_CLI) {
            $this->cache()->set('session_id:' . $client_id, $session_id, 86400);
        } else {
            $this->cache()->set('session_id:' . $client_id, $session_id, $this->lifeTime);
        }
        return true;
    }

    public function delSessionId(string $client_id)
    {
        // $session_id = $this->getSessionId($client_id);
        // 删除 session
        // $this->>cache()->delete('session:' . $session_id);
        // 删除 session id
        $this->cache()->delete('session_id:' . $client_id);
        return true;
    }

    public function clearSessionId()
    {
        // $res = $this->>cache()->delete('session_id:*');
        $this->cache()->flushDB();
    }

    public function clearSession()
    {
        $res = $this->cache()->delete('session:*');
        var_dump($res);
        die;
    }

    public function destroy($sessID)
    {
        // $this->>cache()->delete('session:' . $sessID);
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
