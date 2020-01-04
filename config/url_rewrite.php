<?php
/**
 * 处理访问模块
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 14:41
 */

// 只有通过入口访问需要处理
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($uri, '.php') === false || strpos($uri, 'index.php') !== false) {
    if (isset($_REQUEST['data']) && isset($_POST['data'])) {
        if ($data = $di->crypt->decrypt($_REQUEST['data'])) {
            $_REQUEST = $_POST = json_decode($data, true);
        }
    }
    $service = $_REQUEST['s'] ?? ($_REQUEST['s'] = 'Common.Base.Index');// 访问服务参数
    $service = explode('.', $service);// 拆解访问参数
    $moduleRule = $di->config->get('sys.moduleRule');// 模块过滤规则
    if (count($service) == 3) {// 对应PhalApi原来的访问方式
        // 当前访问模块
        defined('MODULE') || define('MODULE', strtolower($service[0]));
    } else if (count($service) == 2) {// 新的模块化访问方式
        // $pattern = '/\/([\w]+)@?/';
        $pattern = '/([\w]+)*([\?\=][\s\S]*)?$/';
        preg_match($pattern, $uri, $matches);
        // 重定向的模块名称
        $module = $matches[1] ?? '';
        // 当前访问模块
        defined('MODULE') || define('MODULE', strtolower(empty($module) ? $moduleRule['default'] : $module));
    } else {
        // $return = false;
        defined('MODULE') || define('MODULE', '');
    }
    // 当前访问模块 命名空间名称
    defined('NAME_SPACE') || define('NAME_SPACE', $moduleRule['prefix'][MODULE] ?? '');
    // 不存在与列表中 - 非法访问
    if (!NAME_SPACE) {
        \Common\fourZeroFour();
        exit();
    }
    // 重构访问服务参数
    if (count($service) == 2) {
        $service = array_merge([NAME_SPACE], $service);// 命名空间名称放在首位
        $_REQUEST['s'] = implode('.', $service);// 重构访问服务参数
    }
    // 清除上面使用过的变量
    unset($module, $moduleRule, $service);

    // \PhalApi\DI()->request = new \PhalApi\Request($_REQUEST);// 重新定义请求数据
}

