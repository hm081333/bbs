<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

class Pay
{
    /**
     * 小程序支付
     * @param string $out_trade_no 商户系统内部订单号，只能是数字、大小写字母_-*且在同一个商户号下唯一
     * @param string $description 商品描述
     * @param string $attach 附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用，实际情况下只有支付完成状态才会返回该字段。（传递模型类名别称@方法，用于执行支付成功回调）
     * @param float $total_amount 订单总金额，传入单位为元，传给微信单位为分
     * @param string $openid 用户在直连商户appid下的唯一标识
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public static function wechatMiniApp(string $out_trade_no, string $description, string $attach, float $total_amount, string $openid)
    {
        Log::driver('wechat')->debug('wechatMiniApp: ' . Tools::jsonEncode([
                'out_trade_no' => $out_trade_no,
                'description' => $description,
                'attach' => $attach,
                'total_amount' => $total_amount,
                'openid' => $openid,
            ]));
        $pay = \EasyWeChat::pay();
        $payConfig = $pay->getConfig();
        // 支付请求参数
        $payParams = [
            'mchid' => $payConfig->get('mch_id'),
            'out_trade_no' => $out_trade_no,
            'appid' => $payConfig->get('app_id'),
            'description' => $description,
            'attach' => $attach,
            'notify_url' => \App\Utils\Tools::url('common/notify/wechatPay'),
            'amount' => [
                'total' => (int)($total_amount * 100),
                'currency' => 'CNY',
            ],
            'payer' => [
                'openid' => $openid,
            ],
        ];

        Log::driver('wechat')->debug('v3/pay/transactions/jsapi: ' . Tools::jsonEncode($payParams));
        // JSAPI下单
        $response = $pay->getClient()->postJson('v3/pay/transactions/jsapi', $payParams);
        Log::driver('wechat')->debug('JSAPI: ' . $response->toJson());
        // 预支付交易会话标识。用于后续接口调用中使用，该值有效期为2小时
        $prepayId = $response['prepay_id'];

        $appId = $payConfig->get('app_id');
        $signType = 'RSA'; // 默认RSA，v2要传MD5
        $config = $pay->getUtils()->buildMiniAppConfig($prepayId, $appId, $signType); // 返回wx.requestPayment参数数组
        Log::driver('wechat')->debug('JSAPI-OBJECT: ' . Tools::jsonEncode($config));
        return $config;
    }
}
