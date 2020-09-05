<?php


namespace Library\JDDailyBonus;


use Library\Exception\BadRequestException;
use function PhalApi\T;

class initial
{

    public $KEY;
    public $LogDetails = false; //是否开启响应日志, true则开启
    public $stop = 0;
    // public $out = 0; //接口超时退出, 用于可能发生的网络不稳定, 0则关闭. 如QX日志出现大量"JS Context timeout"后脚本中断时, 建议填写6000
    public $custom;
    public $acData = [
        // 京东商城-夺宝
        'JDTreasure' => '29xMZdGiiGYmMZ5CQcGmb7iPhN7n',
        // 京东商城-母婴
        'JDBaby' => '3BbAVGQPDd6vTyHYjmAutXrKAos6',
        // 京东商城-数码
        'JD3C' => '4SWjnZSCTHPYjE5T7j35rxxuMTb6',
        // 京东晚市-补贴
        'JDSubsidy' => 'xK148m4kWj5hBcTPuJUNNXH3AkJ',
        // 京东商城-钟表
        'JDClocks' => '2BcJPCVVzMEtMUynXkPscCSsx68W',
        // 京东商城-医药
        'JDDrug' => '3tqTG5sF1xCUyC6vgEF5CLCxGn7w',
        // 京东商城-超市
        'JDGStore' => 'aNCM6yrzD6qp1Vvh5YTzeJtk7cM',
        // 京东商城-宠物
        'JDPet' => '37ta5sh5ocrMZF3Fz5UMJbTsL42',
        // 京东商城-图书
        'JDBook' => '3SC6rw5iBg66qrXPGmZMqFDwcyXi',
        // 京东拍拍-二手
        'JDShand' => '3S28janPLYmtFxypu37AYAGgivfp',
        // 京东商城-美妆
        'JDMakeup' => '2smCxzLNuam5L14zNJHYu43ovbAP',
        // 京东商城-清洁
        'JDClean' => '2Tjm6ay1ZbZ3v7UbriTj6kHy9dn6',
        // 京东商城-女装
        'JDWomen' => 'DpSh7ma8JV7QAxSE2gJNro8Q2h9',
        // 京东商城-个护
        'JDCare' => 'NJ1kd1PJWhwvhtim73VPsD1HwY3',
        // 京东商城-美食
        'JDFood' => '4PzvVmLSBq5K63oq4oxKcDtFtzJo',
        // 京东商城-珠宝
        'JDJewels' => 'zHUHpTHNTaztSRfNBFNVZscyFZU',
        // 京东商城-菜场
        'JDVege' => 'Wcu2LVCFMkBP3HraRvb7pgSpt64',
    ];
    public $merge = [
        'SpeedUp' => [],
        'JDBean' => [],
        'JDTurn' => [],
        'JRDoll' => [],
        'JRDSign' => [],
        'Overseas' => [],
        'JDFSale' => [],
        'JDPet' => [],
        'JD3C' => [],
        'JDTreasure' => [],
        'JDBaby' => [],
        'JDSubsidy' => [],
        'JDDrug' => [],
        'JDClocks' => [],
        'JDBook' => [],
        'JDGStore' => [],
        'JDShand' => [],
        'JDMakeup' => [],
        'JDWomen' => [],
        'JDCare' => [],
        'JDFood' => [],
        'JDClean' => [],
        'JDVege' => [],
        'JDJewels' => [],
        'JDCube' => [],
        'JDPrize' => [],
        'JRSteel' => [],
        'JRBean' => [],
        'subsidy' => [],
        'JDCash' => [],
        'JDGetCash' => [],
        'JDShake' => [],
    ];

    public function __construct($key = false)
    {
        $this->initialMerge();
        $this->custom = new custom($this);
        if ($key) {
            $this->KEY = $key;
        } else {
            throw new BadRequestException(T('请输入key'));
        }
        if ($this->KEY) {
            $this->all();
        } else {
            $this->custom->notify("京东签到", "", "脚本终止, 未获取Cookie ‼️");
            $this->custom->done();
        }
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function initialMerge()
    {
        foreach ($this->merge as &$merge) {
            $merge = [
                'success' => 0,
                'fail' => 0,
                'error' => 0,
                'bean' => 0,
                'steel' => 0,
                'notify' => '',
                'key' => 0,
                'TSteel' => 0,
                'Cash' => 0,
                'TCash' => 0,
                'Qbear' => 0,
                'nickname' => '',
            ];
        }
        unset($merge);
    }

    private function all()
    {
        call_user_func([new JingDongBean($this), 'main'], $this->stop); //京东京豆
        call_user_func([new JingRongBean($this), 'main'], $this->stop); //金融京豆
        call_user_func([new JingRongDoll($this), 'main'], $this->stop); //金融抓娃娃
        call_user_func([new JingRongSteel($this), 'main'], $this->stop); //金融钢镚
        call_user_func([new JingDongTurn($this), 'main'], $this->stop); //京东转盘
        call_user_func([new JDFlashSale($this), 'main'], $this->stop); //京东闪购
        call_user_func([new JDOverseas($this), 'main'], $this->stop); //京东国际
        call_user_func([new JingDongCash($this), 'main'], $this->stop); //京东现金红包
        call_user_func([new JDMagicCube($this), 'main'], $this->stop); //京东小魔方
        call_user_func([new JingDongGetCash($this), 'main'], $this->stop); //京东领现金
        call_user_func([new JingDongPrize($this), 'main'], $this->stop); //京东抽大奖
        call_user_func([new JingDongSubsidy($this), 'main'], $this->stop); //京东金贴
        call_user_func([new JingDongShake($this), 'main'], $this->stop); //京东摇一摇

        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDTreasure', '京东商城-夺宝');//京东夺宝岛
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDBaby', '京东商城-母婴'); //京东母婴馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JD3C', '京东商城-数码'); //京东数码电器馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDSubsidy', '京东晚市-补贴'); //京东晚市补贴金
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDClocks', '京东商城-钟表'); //京东钟表馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDDrug', '京东商城-医药'); //京东医药馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDGStore', '京东商城-超市'); //京东超市
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDBook', '京东商城-图书'); //京东图书

        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDPet', '京东商城-宠物'); //京东宠物馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDShand', '京东拍拍-二手'); //京东拍拍二手
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDMakeup', '京东商城-美妆'); //京东美妆馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDWomen', '京东商城-女装'); //京东女装馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDVege', '京东商城-菜场'); //京东菜场
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDFood', '京东商城-美食'); //京东美食馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDClean', '京东商城-清洁'); //京东清洁馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDCare', '京东商城-个护'); //京东个人护理馆
        call_user_func([new JDUserSignPre($this), 'main1'], $this->stop, 'JDJewels', '京东商城-珠宝'); //京东珠宝馆

        call_user_func([new JingDongSpeedUp($this), 'main'], $this->stop); //京东天天加速
        call_user_func([new JRDoubleSign($this), 'main'], $this->stop); //金融双签

        call_user_func([new TotalSteel($this), 'main']); //总钢镚查询
        call_user_func([new TotalCash($this), 'main']); //总红包查询
        call_user_func([new TotalBean($this), 'main']); //总京豆查询

        call_user_func([new notify($this), 'main']); //通知模块
    }

}
