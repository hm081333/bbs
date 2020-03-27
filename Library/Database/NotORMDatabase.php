<?php

namespace Library\Database;

use PDO;

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

}
