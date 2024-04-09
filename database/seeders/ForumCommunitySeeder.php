<?php

namespace Database\Seeders;

use App\Models\Forum\ForumCommunity;
use App\Models\Forum\ForumTopicType;
use Illuminate\Database\Seeder;

class ForumCommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        ForumCommunity::truncate();
        ForumTopicType::truncate();
        $forum_community_list = collect()->push([
            'level' => 0,
            'name' => '软件安全',
            'children' => [
                [
                    'name' => '原创发布区',
                    'desc' => '吾爱破解坛友原创作品展示，包含Windows原创工具，也有Android、iOS和Mac OS程序相应的原创程序！',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => '原创工具'],
                        ['name' => '原创汉化'],
                        ['name' => 'Android工具'],
                        ['name' => 'Android汉化'],
                        ['name' => 'iOS'],
                        ['name' => 'Mac'],
                    ],
                ],
                [
                    'name' => '脱壳破解区',
                    'desc' => '讨论Windows软件脱壳分析、软件逆向分析、代码逆向改造、虚拟机加密分析，也包括Web、Mac OS等其他平台程序逆向分析一切尽在此！',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => '转贴'],
                        ['name' => '原创'],
                        ['name' => '.NET逆向'],
                        ['name' => 'CTF'],
                        ['name' => 'Web逆向'],
                        ['name' => 'MacOS逆向'],
                        ['name' => '分享'],
                    ],
                ],
                [
                    'name' => '移动安全区',
                    'desc' => '讨论Android软件脱壳分析、Android软件逆向分析、Android系统安全分析、Android软件加密分析，当然iOS等移动程序逆向分析一切尽在此！',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => 'Android 原创'],
                        ['name' => 'Android 脱壳'],
                        ['name' => 'Android CTF'],
                        ['name' => 'Android 分享'],
                        ['name' => 'Android 转帖'],
                        ['name' => 'iOS 原创'],
                        ['name' => 'iOS 分享'],
                        ['name' => 'iOS 转帖'],
                    ],
                ],
                [
                    'name' => '软件调试区',
                    'desc' => '讨论操作系统底层或系统软件的漏洞分析、也包含游戏安全等相关话题！',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => '系统底层'],
                        ['name' => '漏洞分析'],
                        ['name' => '调试逆向'],
                        ['name' => '游戏安全'],
                    ],
                ],
                [
                    'name' => '编程语言区',
                    'desc' => '学习编程探讨编程技巧，共同分享程序源代码',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => '其他转载'],
                        ['name' => '易语言 原创'],
                        ['name' => 'Python 原创'],
                        ['name' => 'C&C++ 原创'],
                        ['name' => 'Java 原创'],
                        ['name' => '其他原创'],
                        ['name' => '易语言 转载'],
                        ['name' => 'Python 转载'],
                        ['name' => 'C&C++ 转载'],
                        ['name' => 'Java 转载'],
                    ],
                ],
                [
                    'name' => '动画发布区',
                    'desc' => '软件脱壳、软件汉化、软件逆向相关动画教程，学习心得分享。在这里我们可以携手并进，是我们汲取知识的伟大航路!',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => 'Windows'],
                        ['name' => 'Android'],
                        ['name' => '编程'],
                        ['name' => '其他'],
                    ],
                ],
                [
                    'name' => '逆向资源区',
                    'desc' => '[加密解密]相关软件发布区，包含最新最全的软件加密解密相关工具，工欲善其事必先利其器！',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => 'Android Tools'],
                        ['name' => 'Debuggers'],
                        ['name' => 'Disassemblers'],
                        ['name' => 'PEtools'],
                        ['name' => 'Packers'],
                        ['name' => 'Patchers'],
                        ['name' => 'Editors'],
                        ['name' => 'Cryptography'],
                        ['name' => 'Unpackers'],
                        ['name' => 'Dongle'],
                        ['name' => '.NET'],
                        ['name' => 'Scripts'],
                        ['name' => 'OllyDbg 1.x Plugin'],
                        ['name' => 'OllyDbg 2.x Plugin'],
                        ['name' => 'x64dbg Plugin'],
                        ['name' => 'IDA Plugin'],
                        ['name' => 'Mac OSX'],
                        ['name' => 'Network Analyzer'],
                        ['name' => 'Other'],
                    ],
                ],
                [
                    'name' => '精品软件区',
                    'desc' => '精品软件推荐，软件交流天地，汇集众多精彩评论，热心会员每日更新！',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => 'Windows'],
                        ['name' => 'Android'],
                        ['name' => 'Mac'],
                        ['name' => '其他'],
                    ],
                ],
                [
                    'name' => '悬赏问答区',
                    'desc' => '富于智慧和温馨的互助乐园，帮助需要帮助的人!',
                    'level' => 1,
                    'topicTypes' => [
                        ['name' => '资源求助'],
                        ['name' => '下载转存'],
                        ['name' => '经验求助'],
                        ['name' => '其他求助'],
                        ['name' => '公告'],
                    ],
                ],
            ],
        ])->push([
            'level' => 0,
            'name' => '病毒分析',
            'children' => [
                [
                    'name' => '病毒分析区',
                    'desc' => '分析研究病毒木马技术，预防病毒木马，查杀病毒木马',
                    'level' => 1,
                ],
                [
                    'name' => '病毒样本区',
                    'desc' => '计算机病毒样本的测试、上报与交流，请勿实机运行所下载的样本',
                    'level' => 1,
                ],
                [
                    'name' => '病毒救援区',
                    'desc' => '你的计算机又受到病毒的侵扰使你不知所措了？不要着急，让我们一起帮助你解决问题！',
                    'level' => 1,
                ],
                [
                    'name' => '安全工具区',
                    'desc' => '工欲善其事 必先利其器，从这里能找到你想要的利器',
                    'level' => 1,
                ],
            ],
        ])->push([
            'level' => 0,
            'name' => '娱乐',
            'children' => [
                [
                    'name' => '水漫金山',
                    'desc' => '',
                    'level' => 1,
                ],
                [
                    'name' => '福利经验',
                    'desc' => '',
                    'level' => 1,
                ],
            ],
        ])->push([
            'level' => 0,
            'name' => '活动策划专区',
            'children' => [
                [
                    'name' => '周边活动作品区',
                    'desc' => '',
                    'level' => 1,
                ],
                [
                    'name' => '电子书策划制作区',
                    'desc' => '',
                    'level' => 1,
                ],
                [
                    'name' => '2014CrackMe大赛',
                    'desc' => '',
                    'level' => 1,
                ],
                [
                    'name' => '吾爱破解2016安全挑战赛',
                    'desc' => '',
                    'level' => 1,
                ],
                [
                    'name' => '腾讯游戏安全技术竞赛',
                    'desc' => '',
                    'level' => 1,
                ],
            ],
        ])->push([
            'level' => 0,
            'name' => '管理',
            'children' => [
                [
                    'name' => '站点公告',
                    'desc' => '开放注册信息，版块调整公告，站点信息发布，会员违规处理等 ',
                    'level' => 1,
                ],
                [
                    'name' => '招聘求职',
                    'desc' => '此版块只允许发布招聘和求职相关信息！',
                    'level' => 1,
                ],
                [
                    'name' => '申请专区',
                    'desc' => '会员、版主、勋章、友情链接申请，发帖前请认真阅读本版规则！',
                    'level' => 1,
                ],
                [
                    'name' => '站务处理',
                    'desc' => '如果你对我们有什么意见或者建议，请在此提出 ',
                    'level' => 1,
                ],
            ],
        ]);
        ForumCommunity::make()->saveData($forum_community_list);
    }
}
