<?php

namespace App\Listeners;

use App\Models\Mongodb\SqlLog;
use App\Utils\Tools;
use Exception;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use function request;

class SqlListener
{
    private $time;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->time = time();
    }

    /**
     * Handle the event.
     *
     * @param QueryExecuted $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        // 只统计mysql语句
        if ($event->connectionName != 'mysql') return;
        if (!config('app.slow_query_log', false)) return;
        $log = $this->interpolateQuery($event->sql, $event->bindings);
        if ($event->time >= 1000) Log::channel('sql')->info("{$event->time}|{$log}");
        return;
        $request = request();
        $data = [
            'sql' => $log,
            'type' => strtolower(substr(ltrim($log), 0, 6)),
            'log_time' => date('Y-m-d H:i:s', $this->time),
            'exec_consumed' => $event->time,
            'connection_name' => $event->connectionName,
            'method' => 'cli',
            'url' => rtrim(Tools::url(), '/'),
            'path_info' => $request->getPathInfo(),
            'client_ip' => $request->ip(),
            'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? $this->time),
            // 'admin_id' => $this->getAuthId('admin'),
            // 'user_id' => $this->getAuthId('user'),
            // 'recommender_id' => $this->getAuthId('recommender'),
            // 'school_manager_id' => $this->getAuthId('school_manager'),
            // 'agency_manager_id' => $this->getAuthId('agency_manager'),
        ];
        $data = $this->getAuthId($data);
        if (strtolower(substr(php_sapi_name(), 0, 3)) !== 'cli') {
            $data['method'] = strtolower($request->method());
            $data['params'] = json_encode((object)$request->input(), true);
            $data['get'] = json_encode((object)$request->query(), true);
            $data['post'] = json_encode((object)$request->post(), true);
            $data['headers'] = json_encode((object)array_map(function ($header) {
                return implode(',', $header);
            }, $request->header()), true);
            $data['user_agent'] = $request->userAgent();
        } else {
            $data['url'] = array_shift($_SERVER['argv']);
            $data['path_info'] = implode(' ', $_SERVER['argv']);
        }
        try {
            SqlLog::create($data);
        } catch (Exception $e) {
            Log::error('sql语句写入MongoDB失败');
            Log::error($e);
            // throw $e;
        }
    }

    /**
     * 拼接SQL
     * @param $query    string sql字符串（带？号）
     * @param $params   array sql参数
     * @return string
     */
    public function interpolateQuery($query, $params)
    {
        $keys = [];
        $values = $params;
        $values_limit = [];
        $words_repeated = array_count_values(str_word_count($query, 1, ':_'));
        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
                $values_limit[$key] = (isset($words_repeated[':' . $key]) ? intval($words_repeated[':' . $key]) : 1);
            } else {
                $keys[] = '/[?]/';
                $values_limit = [];
            }
            if (is_object($value)) $value = (string)$value;
            if (is_string($value)) $values[$key] = "'" . $value . "'";
            if (is_array($value)) $values[$key] = "'" . implode("','", $value) . "'";
            if (is_null($value)) $values[$key] = 'NULL';
        }
        if (is_array($values) && !empty($values_limit)) {
            foreach ($values as $key => $val) {
                if (isset($values_limit[$key])) {
                    $query = preg_replace(['/:' . $key . '/'], [$val], $query, $values_limit[$key], $count);
                } else {
                    $query = preg_replace(['/:' . $key . '/'], [$val], $query, 1, $count);
                }
            }
            unset($key, $val);
        } else {
            $query = preg_replace($keys, $values, $query, 1, $count);
        }
        unset($keys, $values, $values_limit, $words_repeated);
        return $query;

    }

    /**
     * 获取临牌对应账号ID
     * @return array
     */
    private function getAuthId($data)
    {
        $token_parser = app()->make('tymon.jwt.parser');
        $token_str = $token_parser->parseToken();
        if (empty($token_str)) return $data;
        try {
            $JWTParser = new Parser(new JoseEncoder());
            $token = $JWTParser->parse($token_str);
            $claims = $token->claims();
            $data['account_type'] = $claims->get('account_type', '');
            $data['account_id'] = $claims->get('sub', 0);
        } catch (Exception $e) {
            Log::error('获取令牌信息失败');
            Log::error($token_str);
            Log::error($e);
        }
        return $data;
    }

}
