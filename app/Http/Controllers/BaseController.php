<?php

namespace App\Http\Controllers;

use App\Exceptions\Request\BadRequestException;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/**
 * 模型映射类
 *
 * @property-read \App\Models\Adv modelAdv App\Models\Adv
 * @property-read \App\Models\AdvCategory modelAdvCategory App\Models\AdvCategory
 * @property-read \App\Models\Article\Article modelArticleArticle App\Models\Article\Article
 * @property-read \App\Models\Article\ArticleCategory modelArticleArticleCategory App\Models\Article\ArticleCategory
 * @property-read \App\Models\Forum\ForumCommunity modelForumForumCommunity App\Models\Forum\ForumCommunity
 * @property-read \App\Models\Forum\ForumReply modelForumForumReply App\Models\Forum\ForumReply
 * @property-read \App\Models\Forum\ForumTopic modelForumForumTopic App\Models\Forum\ForumTopic
 * @property-read \App\Models\Forum\ForumTopicType modelForumForumTopicType App\Models\Forum\ForumTopicType
 * @property-read \App\Models\Fund\FundNetValue modelFundFundNetValue App\Models\Fund\FundNetValue
 * @property-read \App\Models\Fund\FundProduct modelFundFundProduct App\Models\Fund\FundProduct
 * @property-read \App\Models\Fund\FundValuation modelFundFundValuation App\Models\Fund\FundValuation
 * @property-read \App\Models\Intel\IntelProduct modelIntelIntelProduct App\Models\Intel\IntelProduct
 * @property-read \App\Models\Intel\IntelProductCategory modelIntelIntelProductCategory App\Models\Intel\IntelProductCategory
 * @property-read \App\Models\Intel\IntelProductSeries modelIntelIntelProductSeries App\Models\Intel\IntelProductSeries
 * @property-read \App\Models\Intel\IntelProductSpec modelIntelIntelProductSpec App\Models\Intel\IntelProductSpec
 * @property-read \App\Models\Mongodb\AccessLog modelMongodbAccessLog App\Models\Mongodb\AccessLog
 * @property-read \App\Models\Mongodb\SqlLog modelMongodbSqlLog App\Models\Mongodb\SqlLog
 * @property-read \App\Models\System\AdministrativeDivision modelSystemAdministrativeDivision App\Models\System\AdministrativeDivision
 * @property-read \App\Models\System\SystemConfig modelSystemSystemConfig App\Models\System\SystemConfig
 * @property-read \App\Models\System\SystemFile modelSystemSystemFile App\Models\System\SystemFile
 * @property-read \App\Models\System\SystemLanguage modelSystemSystemLanguage App\Models\System\SystemLanguage
 * @property-read \App\Models\System\SystemOption modelSystemSystemOption App\Models\System\SystemOption
 * @property-read \App\Models\System\SystemOptionItem modelSystemSystemOptionItem App\Models\System\SystemOptionItem
 * @property-read \App\Models\Tieba\BaiduId modelTiebaBaiduId App\Models\Tieba\BaiduId
 * @property-read \App\Models\Tieba\BaiduTieba modelTiebaBaiduTieba App\Models\Tieba\BaiduTieba
 * @property-read \App\Models\User\User modelUserUser App\Models\User\User
 * @property-read \App\Models\User\UserFeedback modelUserUserFeedback App\Models\User\UserFeedback
 * @property-read \App\Models\User\UserFeedbackLog modelUserUserFeedbackLog App\Models\User\UserFeedbackLog
 * @property-read \App\Models\User\UserFund modelUserUserFund App\Models\User\UserFund
 * @property-read \App\Models\User\UserLoginLog modelUserUserLoginLog App\Models\User\UserLoginLog
 * @property-read \App\Models\User\UserNotifyBarkSetting modelUserUserNotifyBarkSetting App\Models\User\UserNotifyBarkSetting
 * @property-read \App\Models\User\UserNotifyDingDingBotSetting modelUserUserNotifyDingDingBotSetting App\Models\User\UserNotifyDingDingBotSetting
 * @property-read \App\Models\User\UserNotifyPushPlusSetting modelUserUserNotifyPushPlusSetting App\Models\User\UserNotifyPushPlusSetting
 * @property-read \App\Models\User\UserOptionalFund modelUserUserOptionalFund App\Models\User\UserOptionalFund
 * @property-read \App\Models\WeChat\WechatOfficialAccountUser modelWeChatWechatOfficialAccountUser App\Models\WeChat\WechatOfficialAccountUser
 * @package App\Http\Controllers
 * @class BaseController
 */
class BaseController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
    }

    /**
     * 接口参数规则定义
     *
     * @return array
     */
    protected function getRules()
    {
        return [];
    }

    /**
     * 响应一个成功消息
     *
     * @param string     $message
     * @param mixed|null $data
     *
     * @return JsonResponse
     */
    public function success(string $message = '', mixed $data = null): JsonResponse
    {
        return Response::api($message, $data);
    }

    /**
     * 获取参数函数
     *
     * @param array|null $rule 默认null，使用getRules中的规则定义（没有定义即为空规则）；传入规则数组时使用自定义规则。空规则数组返回所有请求参数。
     *
     * @return array
     * @throws BadRequestException
     */
    protected function getParams(?array $rule = null)
    {
        if (!is_array($rule)) {
            [$controller, $action] = explode('@', Route::currentRouteAction());
            if ($controller == static::class) {
                $rules = $this->getRules() ?? [];
                $rule = $rules[$action] ?? [];
            }
        }
        return ValidateRule::instance($rule)->validateRequest();
    }

    public function __get($name)
    {
        if (str_starts_with($name, 'model')) {
            $name = str_replace('model', '', $name);
            return Tools::model()->$name;
        }
        throw new Exception('非法调用不存在函数');
    }

}
