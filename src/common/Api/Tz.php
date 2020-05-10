<?php

namespace Common\Api;

use Exception\Exception;
use PDO;
use function Common\DI;

/**
 * 探针模块接口服务
 * Tz
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Tz extends Base
{
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['serverParam'] = [
        ];
        $rules['compiledModuleDetection'] = [
        ];
        $rules['relatedParam'] = [
        ];
        $rules['enableFunction'] = [
        ];
        $rules['disableFunction'] = [
        ];
        $rules['componentSupport'] = [
        ];
        $rules['thirdPartyComponent'] = [
        ];
        $rules['databaseSupport'] = [
        ];
        $rules['testInt'] = [
        ];
        $rules['testFloat'] = [
        ];
        $rules['testIO'] = [
        ];
        $rules['phpInfo'] = [
        ];
        return $rules;
    }

    /**
     * 探针 - 服务器参数
     * @return array
     */
    public function serverParam()
    {
        $serverID = @php_uname();
        $os = explode(" ", $serverID);
        return [
            'serverID' => $serverID,
            'serverUser' => @get_current_user(),
            'serverDomain' => @$_SERVER['SERVER_NAME'],
            'serverAddr' => DIRECTORY_SEPARATOR == '/' ? @$_SERVER['SERVER_ADDR'] : @gethostbyname($_SERVER['SERVER_NAME']),
            'remoteAddr' => @$_SERVER['REMOTE_ADDR'],
            'serverOS' => $os[0],
            'kernel' => DIRECTORY_SEPARATOR == '/' ? $os[2] : $os[1],
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'],
            'serverLang' => getenv("HTTP_ACCEPT_LANGUAGE"),
            'serverPort' => $_SERVER['SERVER_PORT'],
            'serverName' => DIRECTORY_SEPARATOR == '/' ? $os[1] : $os[2],
            'absolutePath' => $_SERVER['DOCUMENT_ROOT'] ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : str_replace('\\', '/', dirname(__FILE__)),
            'adminMail' => $_SERVER['SERVER_ADMIN'],
        ];
    }

    /**
     * 探针 - 服务器实时数据
     */
    public function serverRealTimeData()
    {
        return \Common\Domain\Tz::getServerRealTimeData();
    }

    /**
     * 获取CPU使用率
     * @return array|bool
     */
    public function cpuPercentage()
    {
        $stat1 = \Common\Domain\Tz::GetCoreInformation();
        sleep(1);
        $stat2 = \Common\Domain\Tz::GetCoreInformation();
        return \Common\Domain\Tz::GetCpuPercentages($stat1, $stat2);
    }

    /**
     * 探针 - 网络使用状况
     * @return array
     */
    public function networkUsage()
    {
        return \Common\Domain\Tz::getNetworkUsage();
    }

    /**
     * 探针 - PHP已编译模块检测
     * @return array
     */
    public function compiledModuleDetection()
    {
        return get_loaded_extensions();
    }

    /**
     * 探针 - PHP相关参数
     * @return array
     */
    public function relatedParam()
    {
        return [
            "php_version" => PHP_VERSION,
            "php_run_mode" => strtoupper(php_sapi_name()),
            "memory_limit" => \Common\Domain\Tz::show("memory_limit"),
            "safe_mode" => \Common\Domain\Tz::show("safe_mode"),
            "post_max_size" => \Common\Domain\Tz::show("post_max_size"),
            "upload_max_filesize" => \Common\Domain\Tz::show("upload_max_filesize"),
            "precision" => \Common\Domain\Tz::show("precision"),
            "max_execution_time" => \Common\Domain\Tz::show("max_execution_time") . "秒",
            "default_socket_timeout" => \Common\Domain\Tz::show("default_socket_timeout") . "秒",
            "doc_root" => \Common\Domain\Tz::show("doc_root"),
            "user_dir" => \Common\Domain\Tz::show("user_dir"),
            "enable_dl" => \Common\Domain\Tz::show("enable_dl"),
            "include_path" => \Common\Domain\Tz::show("include_path"),
            "display_errors" => \Common\Domain\Tz::show("display_errors"),
            "register_globals" => \Common\Domain\Tz::show("register_globals"),
            "magic_quotes_gpc" => \Common\Domain\Tz::show("magic_quotes_gpc"),
            "short_open_tag" => \Common\Domain\Tz::show("short_open_tag"),
            "asp_tags" => \Common\Domain\Tz::show("asp_tags"),
            "ignore_repeated_errors" => \Common\Domain\Tz::show("ignore_repeated_errors"),
            "ignore_repeated_source" => \Common\Domain\Tz::show("ignore_repeated_source"),
            "report_memleaks" => \Common\Domain\Tz::show("report_memleaks"),
            "magic_quotes_runtime" => \Common\Domain\Tz::show("magic_quotes_runtime"),
            "allow_url_fopen" => \Common\Domain\Tz::show("allow_url_fopen"),
            "register_argc_argv" => \Common\Domain\Tz::show("register_argc_argv"),
            "cookie_support" => isset($_COOKIE),
            "aSpellLibrary" => \Common\Domain\Tz::isfun("aspell_check_raw"),
            "BCMath" => \Common\Domain\Tz::isfun("bcadd"),
            "PCRE" => \Common\Domain\Tz::isfun("preg_match"),
            "PDFSupport" => \Common\Domain\Tz::isfun("pdf_close"),
            "SNMPProtocol" => \Common\Domain\Tz::isfun("snmpget"),
            "VMailMgrMailProcess" => \Common\Domain\Tz::isfun("vm_adduser"),
            "curlSupport" => \Common\Domain\Tz::isfun("curl_init"),
            "SMTPSupport" => boolval(get_cfg_var("SMTP")),
            "SMTPAddr" => get_cfg_var("SMTP") ? get_cfg_var("SMTP") : false,
        ];
    }

    /**
     * 探针 - 系统所支持的所有函数
     * @return array
     */
    public function enableFunction()
    {
        $funcs = get_defined_functions();
        return $funcs['internal'] ?? [];
    }

    /**
     * 探针 - 被禁用的函数
     * @return array
     */
    public function disableFunction()
    {
        $disFuns = get_cfg_var("disable_functions");
        return explode(',', $disFuns);
    }

    /**
     * 探针 - 组件支持
     * @return array
     */
    public function componentSupport()
    {
        return [
            "ftp_support" => \Common\Domain\Tz::isfun("ftp_login"),
            "xml_support" => \Common\Domain\Tz::isfun("xml_set_object"),
            "session_support" => \Common\Domain\Tz::isfun("session_start"),
            "socket_support" => \Common\Domain\Tz::isfun("socket_accept"),
            "cal_support" => \Common\Domain\Tz::isfun("cal_days_in_month"),
            "url_fopen_support" => \Common\Domain\Tz::show("allow_url_fopen"),
            "gd_support" => function_exists('gd_info') ? @gd_info()["GD Version"] : false,
            "zlib_support" => \Common\Domain\Tz::isfun("gzclose"),
            "imap_support" => \Common\Domain\Tz::isfun("imap_close"),
            "JDToGregorian_support" => \Common\Domain\Tz::isfun("JDToGregorian"),
            "preg_match_support" => \Common\Domain\Tz::isfun("preg_match"),
            "wddx_support" => \Common\Domain\Tz::isfun("wddx_add_vars"),
            "iconv_support" => \Common\Domain\Tz::isfun("iconv"),
            "mb_string_support" => \Common\Domain\Tz::isfun("mb_eregi"),
            "bc_math_support" => \Common\Domain\Tz::isfun("bcadd"),
            "ldap_support" => \Common\Domain\Tz::isfun("ldap_close"),
            "mcrypt_support" => \Common\Domain\Tz::isfun("mcrypt_encrypt"),
            "mhash_support" => \Common\Domain\Tz::isfun("mhash_count"),
        ];
    }

    /**
     * 探针 - 第三方组件
     * @return array
     */
    public function thirdPartyComponent()
    {

        return [
            "zend_version" => empty(zend_version()) ? false : zend_version(),
            "zend_optimizer" => [
                'name' => substr(PHP_VERSION, 2, 1) > 2 ? "ZendGuardLoader[启用]" : "Zend Optimizer",
                'value' => substr(PHP_VERSION, 2, 1) > 2 ? (get_cfg_var("zend_loader.enable") ? true : false) : (function_exists('zend_optimizer_version') ? zend_optimizer_version() : ((get_cfg_var("zend_optimizer.optimization_level") || get_cfg_var("zend_extension_manager.optimizer_ts") || get_cfg_var("zend.ze1_compatibility_mode") || get_cfg_var("zend_extension_ts")) ? true : false)),
            ],
            "eAccelerator" => (phpversion('eAccelerator')) != '' ? phpversion('eAccelerator') : false,
            "ioncube" => extension_loaded('ionCube Loader') ? (ionCube_Loader_version() . "." . (int)substr(ioncube_loader_iversion(), 3, 2)) : false,
            "XCache" => (phpversion('XCache')) != '' ? phpversion('XCache') : false,
            "APC" => (phpversion('APC')) != '' ? phpversion('APC') : false,
        ];
    }

    /**
     * 探针 - 数据库支持
     * @return array
     */
    public function databaseSupport()
    {
        $dbs = DI()->config->get('dbs.servers.db_master');
        return [
            'MySQL' => \Common\Domain\Tz::isfun("mysql_close"),
            'MySQLi' => \Common\Domain\Tz::iscls("mysqli") ? mysqli_connect($dbs['host'], $dbs['user'], $dbs['password'], $dbs['name'])->server_info : false,
            'PDO' => \Common\Domain\Tz::iscls("PDO") ? (new PDO(
                sprintf('mysql:dbname=%s;host=%s;port=%d', $dbs['name'], $dbs['host'] ?? 'localhost', $dbs['port'] ?? 3306),
                $dbs['user'],
                $dbs['password'],
                $dbs['option'] ?? []
            ))->getAttribute(PDO::ATTR_SERVER_VERSION) : false,
            'ODBC' => \Common\Domain\Tz::isfun("odbc_close"),
            'Oracle' => \Common\Domain\Tz::isfun("ora_close"),
            'SQLServer' => \Common\Domain\Tz::isfun("mssql_close"),
            'dBASE' => \Common\Domain\Tz::isfun("dbase_close"),
            'mSQL' => \Common\Domain\Tz::isfun("msql_close"),
            'SQLite' => extension_loaded('sqlite3') ? true : \Common\Domain\Tz::isfun("sqlite_close"),
            'Hyperwave' => \Common\Domain\Tz::isfun("hw_close"),
            'PostgreSQL' => \Common\Domain\Tz::isfun("pg_close"),
            'Informix' => \Common\Domain\Tz::isfun("ifx_close"),
            'DBA' => \Common\Domain\Tz::isfun("dba_close"),
            'DBM' => \Common\Domain\Tz::isfun("dbmclose"),
            'FilePro' => \Common\Domain\Tz::isfun("filepro_fieldcount"),
            'SyBase' => \Common\Domain\Tz::isfun("sybase_close"),
        ];
    }

    /**
     * 探针 - 服务器性能检测 - 整型测试
     * @return int|string
     */
    public function testInt()
    {
        return \Common\Domain\Tz::testInt();
    }

    /**
     * 探针 - 服务器性能检测 - 浮点测试
     * @return int|string
     */
    public function testFloat()
    {
        return \Common\Domain\Tz::testFloat();
    }

    /**
     * 探针 - 服务器性能检测 - IO测试
     * @return string
     */
    public function testIO()
    {
        return \Common\Domain\Tz::testIO();
        // return \Common\Domain\Tz::testIO();
    }

    /**
     * php服务器的配置信息
     */
    public function phpInfo()
    {
        ob_start();
        phpinfo();
        die(ob_get_clean());
    }


}
