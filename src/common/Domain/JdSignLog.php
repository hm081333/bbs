<?php


namespace Common\Domain;


use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;
use Library\Traits\Domain;
use Library\Traits\Model;
use PhalApi\Model\NotORMModel;
use function PhalApi\T;

/**
 * 京东签到记录 领域层
 * Class JdSignLog
 * @package Common\Domain
 */
class JdSignLog
{
    use Domain;

    protected $sign_key;
    protected $reward_type;
    protected $jd_sign_id;

    /**
     * 设置签到项的key
     * @param $sign_key
     * @return $this
     */
    public function setSignKey($sign_key)
    {
        $this->sign_key = $sign_key;
        return $this;
    }

    /**
     * 设置签到ID
     * @param $jd_sign_id
     * @return $this
     */
    public function setJdSignId($jd_sign_id)
    {
        $this->jd_sign_id = $jd_sign_id;
        return $this;
    }

    /**
     * 获得京豆记录
     * @param        $num
     * @param array  $memo
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function bean($num, $memo = [])
    {
        $this->setRewardType('bean')->log($num, $memo);
    }

    /**
     * 写入签到记录
     * @param float $num
     * @param array $memo
     * @return bool
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function log($num, $memo = [])
    {
        if (time() <= 1583510400) {
            return false;
        }
        if (empty($num)) {
            return false;
        }
        if (empty($this->sign_key) || empty($this->reward_type) || empty($this->jd_sign_id)) {
            throw new InternalServerErrorException(T('系统异常'));
        }
        if (is_array($memo)) {
            $memo = serialize($memo);
        }
        $insert_res = $this->Model_JdSignLog()->insert([
            'jd_sign_id' => $this->jd_sign_id,
            'sign_key' => $this->sign_key,
            'add_time' => time(),
            'reward_type' => $this->reward_type,
            'num' => $num,
            'memo' => $memo,
        ]);
        if (!$insert_res) {
            throw new InternalServerErrorException(T('写入签到记录失败'));
        }
        return true;
    }

    /**
     * 京东签到记录 数据层
     * @return \Common\Model\JdSignLog|Model|NotORMModel
     * @throws BadRequestException
     */
    protected function Model_JdSignLog()
    {
        return self::getModel('JdSignLog');
    }

    /**
     * 设置收益类型
     * @param $reward_type
     * @return $this
     */
    public function setRewardType($reward_type)
    {
        $this->reward_type = $reward_type;
        return $this;
    }

    /**
     * 获得营养液记录
     * @param        $num
     * @param array  $memo
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function nutrients($num, $memo = [])
    {
        $this->setRewardType('nutrients')->log($num, $memo);
    }

    /**
     * 获得白条提额记录
     * @param        $num
     * @param array  $memo
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function baitiao($num, $memo = [])
    {
        $this->setRewardType('baitiao')->log($num, $memo);
    }

    /**
     * 获得钢镚记录
     * @param        $num
     * @param array  $memo
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function coin($num, $memo = [])
    {
        $this->setRewardType('coin')->log($num, $memo);
    }
}
