<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use library\exception\BadRequestException;
use library\exception\InternalServerErrorException;
use think\facade\Db;
use think\facade\Log;

/**
 * Class System
 * @package app\admin\controller
 */
class System extends BaseController
{

    /**
     * 备份数据
     * @desc 备份数据
     */
    public function backup()
    {
        $data = $this->request->post();
        if (empty($data['password'])) throw new BadRequestException('请输入管理员密码');
        $admin = $this->modelAdmin->field('password')->where('id', 1)->find();
        if (!pwd_verify($data['password'], $admin['password'])) throw new BadRequestException('密码错误');
        set_time_limit(0);
        ignore_user_abort(true);
        $db = config('database.connections.' . config('database.default'));
        $dir = root_path('backup/data') . date('Ym', time()) . '/';
        if (!file_exists($dir)) {
            createDir($dir);
        }
        $file_name = date('Ymd-His', time()) . '.sql';
        $file = $dir . $file_name;
        $return_val = true;
        system("mysqldump -h{$db['hostname']} -P{$db['hostport']} -u{$db['username']} -p{$db['password']} {$db['database']} > {$file}", $return_val);
        if ($return_val) throw new InternalServerErrorException('备份失败');
        return success('备份成功');
    }

    /**
     * 还原数据
     * @desc 还原数据
     */
    public function restore()
    {
        $data = $this->request->post();
        if (empty($data['password'])) throw new BadRequestException('请输入管理员密码');
        $admin = $this->modelAdmin->field('password')->where('id', 1)->find();
        if (!pwd_verify($data['password'], $admin['password'])) throw new BadRequestException('密码错误');
        set_time_limit(0);
        ignore_user_abort(true);
        $db = config('database.connections.' . config('database.default'));
        $file = root_path('backup/data') . $data['name'];
        if (!file_exists($file)) {
            throw new BadRequestException('找不到该文件');
        }
        $return_val = true;
        system("mysql -h{$db['hostname']} -P{$db['hostport']} -u{$db['username']} -p{$db['password']} {$db['database']} < {$file}", $return_val);
        if ($return_val) throw new InternalServerErrorException('还原失败');
        return success('还原成功');
    }

    /**
     * 重置系统
     * @desc 重置系统
     */
    public function reset()
    {
        $data = $this->request->post();
        if (empty($data['password'])) throw new BadRequestException('请输入管理员密码');
        $admin = $this->modelAdmin->field('password')->where('id', 1)->find();
        if (!pwd_verify($data['password'], $admin['password'])) throw new BadRequestException('密码错误');
        set_time_limit(0);
        ignore_user_abort(true);
        /* 数据库配置 */
        /* 当前使用的数据库 */
        $db = config('database.connections.' . config('database.default'));
        /* 利用 SHOW TABLES 查询所有表 */
        $tables = Db::query("SHOW TABLES;");
        /* 查询结果key值为表名 */
        $key_name = "Tables_in_" . $db['database'];
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
        Db::execute($sql);
        /* 删除文件 */
        $file_paths = [
            'static/upload/pics',// 删除图片
            'static/upload/wechat',// 删除微信图片
            'static/upload/neditor',// 删除富文本上传内容
        ];
        foreach ($file_paths as $file_path) {
            emptyDir(public_path($file_path));
        }
        /* 删除缓存文件 */
        emptyDir(runtime_path('cache'));
        /* 删除日志文件 */
        // \Common\emptyDir(API_ROOT . '/runtime/log');
        /* 写入日志 */
        Log::debug('重置系统');
        return success('清空成功');
    }

    /**
     * 备份文件列表
     * @desc 备份文件列表
     */
    public function backupList()
    {
        return $this->dirFile(root_path('backup/data'));
    }

    /**
     * 遍历目录。。。无限遍历--注意超时！
     * @param        $path
     * @param string $dir_name
     * @param int $i
     * @param array $all
     * @return array
     *      sort() 函数用于对数组单元从低到高进行排序。
     *      rsort() 函数用于对数组单元从高到低进行排序。
     *      asort() 函数用于对数组单元从低到高进行排序并保持索引关系。
     *      arsort() 函数用于对数组单元从高到低进行排序并保持索引关系。
     *      ksort() 函数用于对数组单元按照键名从低到高进行排序。
     *      krsort() 函数用于对数组单元按照键名从高到低进行排序。
     */
    private function dirFile($path, $dir_name = '', $i = 0, $all = [])
    {
        $dir = opendir($path);//打开目录
        while (($file = readdir($dir)) != false) {
            //逐个文件读取，添加!=false条件，是为避免有文件或目录的名称为0
            if ($file == '.' || $file == '..') {//判断是否为.或..，默认都会有
                continue;
            }
            if (is_dir($path . '/' . $file)) {//如果为目录
                $rs = $this->dirFile($path . '/' . $file, $file);//继续读取该目录下的目录或文件
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

}
