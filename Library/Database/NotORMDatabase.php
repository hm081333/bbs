<?php

namespace Library\Database;

use PDO;
use PDOException;
use PhalApi\Exception\InternalServerErrorException;

/**
 *
 * Class NotORMDatabase
 * @author LYi-Ho 2018-12-29 14:13:42
 */
class NotORMDatabase extends \PhalApi\Database\NotORMDatabase
{
    /**
     * @param array   $configs 数据库配置
     * @param boolean $debug   是否开启调试模式
     */
    public function __construct(array $configs, bool $debug = false)
    {
        parent::__construct($configs, $debug);
    }

    /**
     * 针对MySQL的PDO链接，如果需要采用其他数据库，可重载此函数
     * @param array $dbCfg 数据库配置
     * @return PDO
     */
    protected function createPDOBy($dbCfg)
    {
        $dsn = sprintf('mysql:dbname=%s;host=%s;port=%d', $dbCfg['name'], $dbCfg['host'] ?? 'localhost', $dbCfg['port'] ?? 3306);
        $charset = $dbCfg['charset'] ?? 'UTF8';
        $option = $dbCfg['option'] ?? [];
        if (IS_CLI) {
            $option[PDO::ATTR_PERSISTENT] = true;
        }

        // 支持sql server
        if (!empty($dbCfg['type']) && strtolower($dbCfg['type']) == 'sqlserver') {
            $dsn = sprintf('sqlsrv:Server=%s,%d;Database=%s', $dbCfg['host'] ?? 'localhost', $dbCfg['port'] ?? 1433, $dbCfg['name']);
        }

        $pdo = new PDO(
            $dsn,
            $dbCfg['user'],
            $dbCfg['password'],
            $option
        );
        $pdo->exec("SET NAMES '{$charset}'");


        return $pdo;
    }

    /**
     * 获取 PDO连接
     * @param string $dbKey 数据库表名唯一KEY
     * @return PDO
     * @throws InternalServerErrorException
     */
    protected function getPdo($dbKey)
    {
        $pdo = parent::getPdo($dbKey);
        if (!$this->ping($pdo)) {
            // 连接断开，重连
            unset($this->_pdos[$dbKey]);
            return $this->getPdo($dbKey);
        }
        return $pdo;
    }

    /**
     * 检测连接实例响应
     * @param PDO $pdo 连接实例
     * @return bool
     */
    public function ping($pdo)
    {
        try {
            $pdo->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
                return false;
            }
        }
        return true;
    }

}
