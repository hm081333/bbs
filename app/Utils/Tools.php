<?php

namespace App\Utils;

use App\Exceptions\Request\BadRequestException;
use App\Models\BaseModel;
use App\Utils\Aliyun\Oss;
use Closure;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Throwable;
use function igbinary_serialize;
use function igbinary_unserialize;
use function request;
use function serialize;
use function unserialize;

class Tools
{
    //region 自定义方法
    /**
     * JSON编码
     * @param mixed $array
     * @return false|string
     */
    public static function json_encode(mixed $array): false|string
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * JSON反编码
     * @param string $json
     * @return mixed
     */
    public static function json_decode(string $json): mixed
    {
        return json_decode($json, true);
    }

    /**
     * 序列化
     * @param $value
     * @return string|null
     */
    public static function serialize($value)
    {
        if (function_exists('igbinary_serialize')) {
            return igbinary_serialize($value);
        } else {
            return serialize($value);
        }

    }

    /**
     * 反序列化
     * @param $value
     * @return string|null
     */
    public static function unserialize($value)
    {
        if (function_exists('igbinary_unserialize')) {
            return igbinary_unserialize($value);
        } else {
            return unserialize($value);
        }

    }

    /**
     * 产生随机字串，可用来自动生成密码
     * 默认长度6位 字母和数字混合 支持中文
     * @param string $len 长度
     * @param string $type 字串类型
     *                         0 字母 1 数字 其它 混合
     * @param string $addChars 额外字符
     * @return string
     */
    public static function randString($len = 6, $type = '', $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijkmnpqrstuvwxyz' . $addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
                break;
            case 5:
                $chars = 'abcdefghijkmnpqrstuvwxyz23456789';
                break;
            case 6:
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZ23456789';
                break;
            default:
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {//位数过长重复字符串一定次数
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $len);
        } else {
            // 中文随机字
            for ($i = 0; $i < $len; $i++) {
                $str .= static::msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1, 'utf-8', false);
            }
        }
        return $str;
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    public static function msubstr($str, $start = 0, $length = 0, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        else if (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . '...' : $slice;
    }

    /**
     * 获取设备类型
     * @return int
     */
    public static function getDeviceType()
    {
        $is_mobile = static::isMobile();
        $type = !$is_mobile ? 0 : 1;
        //全部变成小写字母
        $agent = strtolower(request()->server('HTTP_USER_AGENT'));
        //分别进行判断
        if (strpos($agent, 'iphone') !== false || strpos($agent, 'ipad') !== false) {
            $type = 2;
        }
        if (strpos($agent, 'android') !== false) {
            $type = 3;
        }
        return $type;
    }

    /**
     * 判断是否移动设备
     * @return bool
     */
    public static function isMobile()
    {
        $server = request()->server();
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($server['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($server['HTTP_VIA'])) {
            return stristr($server['HTTP_VIA'], "wap") ? true : false;// 找不到为flase,否则为TRUE
        }
        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset ($server['HTTP_USER_AGENT'])) {
            $clientkeywords = [
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($server['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset ($server['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($server['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($server['HTTP_ACCEPT'], 'text/html') === false || (strpos($server['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($server['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取设备类型名称
     * @param false $type
     * @return string|string[]
     */
    public static function getDeviceTypeName($type = false)
    {
        $arr = [
            0 => 'PC',
            1 => 'H5',
            2 => 'iPhone',
            3 => '安卓',
        ];
        return $type === false ? $arr : ($arr[$type] ?? '未知');
    }

    /**
     * 判断是否生产环境
     * @return bool
     */
    public static function isProduction(): bool
    {
        return config('app.env', 'production') === 'production';
    }

    /**
     * 判断是否调试模式
     * @return bool
     */
    public static function isDebug(): bool
    {
        return config('app.debug', false);
    }

    /**
     * 获取队列名
     * @param $num
     * @return string
     */
    public static function getQueueName($num)
    {
        return 'queue' . ($num % config('queue.count'));
    }

    /**
     * 获取当前请求的时间戳
     * @access public
     * @param bool $float 是否使用浮点类型
     * @return integer|float
     */
    public static function time(bool $float = false): float|int
    {
        return $float ? request()->server('REQUEST_TIME_FLOAT') : request()->server('REQUEST_TIME');
    }

    /**
     * 获取当前请求的时间
     * @access public
     * @return Carbon
     */
    public static function now(): Carbon
    {
        return Carbon::parse(date('Y-m-d H:i:s', static::time()));
    }

    /**
     * 获取当前请求的日期
     * @access public
     * @return Carbon
     */
    public static function today(): Carbon
    {
        return Carbon::parse(date('Y-m-d', static::time()));
    }

    /**
     * 父子关系的数组转换成树形结构数据
     * @param $data
     * @return mixed
     */
    public static function translateDataToTree($data)
    {
        $parent = array_filter($data, function ($row) {
            return $row['pid'] == 0;
        });
        $children = array_filter($data, function ($row) {
            return $row['pid'] > 0;
        });
        return static::_translateDataToTree($parent, $children);
    }

    /**
     * 父子关系的数组转换成树形结构数据（递归）
     * @param $parent
     * @param $children
     * @return mixed
     */
    public static function _translateDataToTree($parent, $children)
    {
        foreach ($parent as &$item) {
            foreach ($children as $index => $current) {
                if ($current['pid'] == $item['id']) {
                    unset($current['pid']);
                    unset($children[$index]);
                    $currents = static::_translateDataToTree([$current], $children);
                    if (!isset($item['children'])) $item['children'] = [];
                    $item['children'][] = $currents[0];
                    unset($currents);
                }
            }
            unset($item['pid']);
        }
        return $parent;
    }

    /**
     * 事务锁
     * @desc 用于控制器层
     * @param Closure $callback
     * @param int $attempts
     * @return void
     * @throws Throwable
     */
    public static function transaction(Closure $callback, int $attempts = 1)
    {
        DB::transaction(function ($connection) use ($callback) {
            $callback($connection);
        }, $attempts);
    }

    /**
     * 并发锁
     * @desc 用于控制器层
     * @param Closure $callback
     * @param string $unique
     * @return void
     * @throws Throwable
     */
    public static function concurrent(Closure $callback, $unique = 'all')
    {
        // 锁缓存KEY，最好是使用Redis缓存
        $cache_key = 'concurrent:' . Route::current()->uri() . ':' . $unique;
        // 锁存在
        //if (Cache::has($cache_key)) throw new BadRequestException('服务器繁忙，请重试');
        // 锁持续半个小时
        Cache::put($cache_key, 1, 30 * 60);
        try {
            $callback();
        } catch (Exception $exception) {
            // 解锁
            Cache::forget($cache_key);
            throw $exception;
        }
        // 解锁
        Cache::forget($cache_key);
    }

    /**
     * 模型别称
     * @param Model|string $model
     * @return string
     */
    public static function modelAlias(Model|string $model)
    {
        $model_name = is_object($model) ? get_class($model) : $model;
        return implode('', explode('\\', str_replace('App\\Models', '', $model_name)));
    }
    //endregion

    //region 目录，文件

    /**
     * 扫描目录文件
     * @param string $dir 需要扫描的目录
     * @return array
     */
    public static function scanFile(string $dir)
    {
        $file_arr = scandir($dir);
        $new_arr = [];
        foreach ($file_arr as $item) {
            if ($item != ".." && $item != ".") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $item)) {
                    $new_arr[$item] = static::scanFile($dir . DIRECTORY_SEPARATOR . $item);
                } else {
                    $new_arr[] = $item;
                }
            }
        }
        return $new_arr;
    }

    /**
     * 根据路径创建目录或文件
     * @param string $path 需要创建目录路径
     */
    public static function createDir($path)
    {
        if (!is_dir($path) && !mkdir($path, 0777, true)) {
            Log::debug($path);
            static::createDir(dirname($path));
            mkdir($path, 0777, true);
        }
    }

    /**
     * 清空目录以及子目录等所有文件--不删除目录
     * @param $path
     * @return bool
     */
    public static function emptyDir($path)
    {
        if (!is_dir($path)) {
            return false;
        }
        $dir = opendir($path);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $path . '/' . $file;
                if (is_dir($full)) {
                    static::deleteDir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        return true;
    }

    /**
     * 删除目录以及子目录等所有文件
     * - 请注意不要删除重要目录！
     * @param string $path 需要删除目录路径
     * @return bool
     */
    public static function deleteDir($path)
    {
        static::emptyDir($path);
        rmdir($path);
        return true;
    }

    /**
     * 临时目录路径
     * @param $path
     * @return string
     */
    public static function runtimePath($path = '')
    {
        return storage_path('app/runtime') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * 备份目录路径
     * @param $path
     * @return string
     */
    public static function backupPath($path = '')
    {
        return storage_path('backup') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * 资源路径
     * @param string $path
     * @return string
     */
    public static function storageAsset($path = '')
    {
        $base_url = config('app.storage_url') ?: static::url('storage');
        return rtrim($base_url, '/') . '/' . ltrim($path, '/');
    }

    /**
     * url根目录
     * @param string $path
     * @return string
     */
    public static function url($path = '')
    {
        return asset($path);
    }

    //endregion

    //region 快捷调用方法
    public static function curl($retryTimes = 1, $timeoutMs = 3000): CUrl
    {
        return new CUrl($retryTimes, $timeoutMs);
    }

    /**
     * @param $file
     * @return File
     * @throws Exception
     */
    public static function file($file = false): File
    {
        return new File($file);
    }

    public static function aliyun_sms()
    {
        //return new Sms();
    }

    public static function aliyun_oss()
    {
        return new Oss();
    }
    //endregion

    //region 压缩解压缩二进制
    /**
     * 解密gzip二进制字符串
     * @param $str
     * @return string
     */
    public static function compress_string_decode($str)
    {
        return static::compress_binary_decode(mb_convert_encoding($str, 'ISO-8859-1', 'utf-8'));
    }

    /**
     * 解密gzip二进制
     * @param $data
     * @return string
     */
    public static function compress_binary_decode($data)
    {
        return zlib_decode($data);
    }

    /**
     * 生成gzip二进制字符串
     * @param     $str
     * @param int $encoding ZLIB_ENCODING_RAW|ZLIB_ENCODING_DEFLATE|ZLIB_ENCODING_GZIP
     * @return false|string|string[]|null
     */
    public static function compress_string_encode($str, $encoding = ZLIB_ENCODING_RAW)
    {
        return mb_convert_encoding(static::compress_binary_encode($str, $encoding), 'utf-8', 'ISO-8859-1');
    }

    /**
     * 生成gzip二进制
     * @param     $data
     * @param int $encoding ZLIB_ENCODING_RAW|ZLIB_ENCODING_DEFLATE|ZLIB_ENCODING_GZIP
     * @return string
     */
    public static function compress_binary_encode($data, $encoding = ZLIB_ENCODING_RAW)
    {
        return zlib_encode($data, $encoding);
    }

    //endregion
}