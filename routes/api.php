<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('webhook', function (\Illuminate\Http\Request $request) {
    $token = 'b49913863c50dfcd20acae835ebf8948';
    // 检验token
    if ($request->header('x-gitee-token') != $token || $request->post('password') != $token) return 'Hello World';
    // 只针对主分支更新
    if ($request->post('ref') == 'refs/heads/master' && $request->post('total_commits_count', 0) > 0) {
        $post_data = $request->post();
        $path = base_path();
        $res = shell_exec("cd {$path} && bash update.sh 2>&1");//以www用户运行
        $res_log = '-------------------------' . PHP_EOL;
        $res_log .= $post_data['user_name'] . ' 在' . date('Y-m-d H:i:s') . '向' . $post_data['repository']['name'] . '项目的' . $post_data['ref'] . '分支push了' . $post_data['total_commits_count'] . '个commit：' . PHP_EOL;
        $res_log .= $res . PHP_EOL;
        file_put_contents(base_path('git-webhook-ceshi.log'), $res_log, FILE_APPEND);//追加写入
        return 'success';
    }
    return 'Hello World';
})->name('webhook');
