<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2019-01-21
 * Time: 10:04:04
 */

namespace Common\Domain;

use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;
use Library\Traits\Domain;
use function Common\createDir;
use function Common\emptyDir;
use function Common\pwd_verify;
use function PhalApi\T;

/**
 * 系统操作 领域层
 * Class System
 * @package Common\Domain
 * @author  LYi-Ho 2019-01-08 18:51:57
 */
class System
{
    use Domain;

    /**
     * 遍历目录。。。无限遍历--注意超时！
     * @param        $path
     * @param string $dir_name
     * @param int    $i
     * @param array  $all
     * @return array
     *      sort() 函数用于对数组单元从低到高进行排序。
     *      rsort() 函数用于对数组单元从高到低进行排序。
     *      asort() 函数用于对数组单元从低到高进行排序并保持索引关系。
     *      arsort() 函数用于对数组单元从高到低进行排序并保持索引关系。
     *      ksort() 函数用于对数组单元按照键名从低到高进行排序。
     *      krsort() 函数用于对数组单元按照键名从高到低进行排序。
     */
    public static function dirFile($path, $dir_name = '', $i = 0, $all = [])
    {
        $dir = opendir($path);//打开目录
        while (($file = readdir($dir)) != false) {
            //逐个文件读取，添加!=false条件，是为避免有文件或目录的名称为0
            if ($file == '.' || $file == '..') {//判断是否为.或..，默认都会有
                continue;
            }
            if (is_dir($path . '/' . $file)) {//如果为目录
                $rs = self::dirFile($path . '/' . $file, $file);//继续读取该目录下的目录或文件
                $all += $rs;
            } else {
                $i += 1;
                if ($dir_name == '') {
                    $all[$i] = [
                        'file_name' => $file,
                        'file_size' => filesize($path . '/' . $file),
                    ];
                } else {
                    $all[$dir_name][$i] = [
                        'file_name' => $file,
                        'file_size' => filesize($path . '/' . $file),
                    ];
                }
            }
        }
        closedir($dir);
        return $all;
    }

    /**
     * 还原
     * @param $data
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public static function restore($data)
    {
        self::DI()->response->setMsg(T('还原成功'));
        if (empty($data['password'])) {
            throw new BadRequestException(T('请输入管理员密码'));
        }
        $admin = self::getDomain('Admin')::getInfo(1, 'password');
        if (!pwd_verify($data['password'], $admin['password'])) {
            throw new BadRequestException(T('密码错误'));
        }
        set_time_limit(0);
        ignore_user_abort(true);
        $dbs = self::DI()->config->get('dbs.servers');
        $db = $dbs[DB];
        $dir = API_ROOT . "/data/";
        $path = $data['name'];
        $file = $dir . $path;
        if (!file_exists($file)) {
            throw new BadRequestException(T('找不到该文件'));
        }
        $return_val = true;
        system(MySQL . "mysql -u" . $db['user'] . " -p" . $db['password'] . " -h" . $db['host'] . " " . $db['name'] . " < " . $file, $return_val);
        if ($return_val) {
            throw new InternalServerErrorException(T('还原失败'));
        }
    }

    /**
     * 备份
     * @param $data
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public static function backup($data)
    {
        self::DI()->response->setMsg(T('备份成功'));
        if (empty($data['password'])) {
            throw new BadRequestException(T('请输入管理员密码'));
        }
        $admin = self::getDomain('Admin')::getInfo(1, 'password');
        if (!pwd_verify($data['password'], $admin['password'])) {
            throw new BadRequestException(T('密码错误'));
        }
        set_time_limit(0);
        ignore_user_abort(true);
        $dbs = self::DI()->config->get('dbs.servers');
        $db = $dbs[DB];
        $dir = API_ROOT . '/data/' . date('Ym', time()) . '/';
        if (!file_exists($dir)) {
            createDir($dir);
        }
        $file_name = date('Y年m月d日-H时i分s秒', time()) . '.sql';
        $file = $dir . $file_name;
        $return_val = true;
        system(MySQL . "mysqldump -u" . $db['user'] . " -p" . $db['password'] . " -h" . $db['host'] . " " . $db['name'] . " > " . $file, $return_val);
        if ($return_val) {
            throw new InternalServerErrorException(T('备份失败'));
        }
    }

    /**
     * 重置系统
     */
    public static function reset($data)
    {
        self::DI()->response->setMsg(T('清空成功'));
        if (empty($data['password'])) {
            throw new BadRequestException(T('请输入管理员密码'));
        }
        $admin = self::getDomain('Admin')::getInfo(1, 'password');
        if (!pwd_verify($data['password'], $admin['password'])) {
            throw new BadRequestException(T('密码错误'));
        }
        set_time_limit(0);
        ignore_user_abort(true);
        /* 数据库配置 */
        $dbs = self::DI()->config->get('dbs.servers');
        /* 当前使用的数据库 */
        $db = $dbs[DB];
        $model = new \Common\Model\Admin();
        /* 利用 SHOW TABLES 查询所有表 */
        $tables = $model->queryRows("SHOW TABLES;");
        /* 查询结果key值为表名 */
        $key_name = "Tables_in_" . $db['name'];
        /* 不清除的表 */
        $not_clear = [
            'ly_admin',
            'ly_setting',
            'ly_ip',
            'ly_class',
            'ly_logistics_company',
            'ly_message',
        ];
        /* 初始化清空SQL */
        $sql = "";
        array_map(function ($item) use ($key_name, $not_clear, &$sql) {
            /* 表名 */
            $table_name = $item[$key_name];
            /* 不在忽略列表中 */
            if (!in_array($table_name, $not_clear)) {
                $sql .= "truncate table {$table_name};";// 拼接清空SQL
            }
        }, $tables);
        /* 执行SQL */
        $model->queryExecute($sql);
        /* 删除文件 */
        $file_paths = [
            'static/upload/pics',// 删除图片
            'static/upload/wechat',// 删除微信图片
            'static/upload/neditor',// 删除富文本上传内容
        ];
        foreach ($file_paths as $file_path) {
            emptyDir($file_path);
        }
        /* 删除缓存文件 */
        emptyDir(API_ROOT . '/runtime/cache');
        /* 删除日志文件 */
        // \Common\emptyDir(API_ROOT . '/runtime/log');
        /* 写入日志 */
        self::DI()->logger->debug('重置系统');
    }

}
