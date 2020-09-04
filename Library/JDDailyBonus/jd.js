var Key = 'shshshfpb=tVBqHpN7OyXYgPxIVBqY9vg%3D%3D; shshshfpa=b57728e0-9c76-3ba0-cad4-b6a185c849a4-1567159746; __jdu=15826026155661206111329; pinId=HxzY8uAQww21ffsfn98I-w; TrackID=1CZJLZnkFgP74KaePuYH_h6uoDyfF5fB_UfP7LDNQVY2xG8_1l3kq4i6Rg38IPCLd4GJb5vdpF933XyprToIpTJR9zNKwAUhKlZrMuVoRCZI; cn=0; autoOpenApp_downCloseDate_auto=1597230765177_21600000; warehistory="100013505108,"; __jdv=122270672%7Candroidapp%7Ct_335139774%7Cappshare%7CCopyURL%7C1597895033476; areaId=19; PCSYCityID=CN_440000_441900_0; 3AB9D23F7A4B3C9B=ZCI77DLATPU56KOWQY7SIADNBLZ4YGQBXXZJLUQJXU4O2STCU5TYMMXQZQW6AOO64BVOROS26TU2WPW5S6Z7QXQXFQ; ipLoc-djd=19-1655-4886-0; __jda=122270672.15826026155661206111329.1582602615.1598672620.1598923871.84; __jdc=122270672; mba_muid=15826026155661206111329; shshshfp=bb121ebd1fe42c9aaa8cdb7b3e415964; jcap_dvzw_fp=2b8f1e15e06d403b441948b1a6b3cba4$841880209415; TrackerID=MaQQwFVqUM7XACCPzdAJCSOJU-5d1RiBqwaS2Qp9SmrjRwEZsDYuaB0hDh71bf7oybXH6xxz7K1IIzKeo6YTcPcpt2GFZO6zg9deymeJwUt5d-CMySNOzs-CN_K04LmH; pt_key=AAJfTaT7ADBoLFy101N1EOD5RTmSDHS56toP3TFjhpRUYjQgiUvcpf1yH0zgug5LBZVLA_9T_uI; pt_pin=hm081333; pt_token=2o7pzw4c; pwdt_id=hm081333; mobilev=html5; __jdb=122270672.8.15826026155661206111329|84.1598923871; mba_sid=15989238716474259343362560277.8; __jd_ref_cls=Mnpm_ComponentApplied;';

var DualKey = ''; //如需双账号签到,此处单引号内填写抓取的"账号2"Cookie, 否则请勿填写

var LogDetails = false; //是否开启响应日志, true则开启

var stop = 0;

var DeleteCookie = false; //是否清除Cookie, true则开启. (该选项仅适用于QX,Surge,Loon,JsBox)

var boxdis = true; //是否开启自动禁用, false则关闭. 脚本运行崩溃时(如VPN断连), 下次运行时将自动禁用相关崩溃接口(仅部分接口启用), 崩溃时可能会误禁用正常接口. (该选项仅适用于QX,Surge,Loon)

var ReDis = false; //是否移除所有禁用列表, true则开启. 适用于触发自动禁用后, 需要再次启用接口的情况. (该选项仅适用于QX,Surge,Loon)

var out = 0; //接口超时退出, 用于可能发生的网络不稳定, 0则关闭. 如QX日志出现大量"JS Context timeout"后脚本中断时, 建议填写6000

var $nobyda = nobyda();

async function all() {
    if (stop == 0) {
        await Promise.all([
            JingDongBean(stop), //京东京豆
            JingRongBean(stop), //金融京豆
            JingRongDoll(stop), //金融抓娃娃
            JingRongSteel(stop), //金融钢镚
            JingDongTurn(stop), //京东转盘
            JDFlashSale(stop), //京东闪购
            JDOverseas(stop), //京东国际
            JingDongCash(stop), //京东现金红包
            JDMagicCube(stop), //京东小魔方
            JingDongPrize(stop), //京东抽大奖
            JingDongSubsidy(stop), //京东金贴
            JingDongGetCash(stop), //京东领现金
            JingDongShake(stop) //京东摇一摇
        ]);
        await Promise.all([
            JDUserSignPre(stop, 'JDTreasure', '京东商城-夺宝'), //京东夺宝岛
            JDUserSignPre(stop, 'JDBaby', '京东商城-母婴'), //京东母婴馆
            JDUserSignPre(stop, 'JD3C', '京东商城-数码'), //京东数码电器馆
            JDUserSignPre(stop, 'JDSubsidy', '京东晚市-补贴'), //京东晚市补贴金
            JDUserSignPre(stop, 'JDDrug', '京东商城-医药'), //京东医药馆
            JDUserSignPre(stop, 'JDWomen', '京东商城-女装'), //京东女装馆
            JDUserSignPre(stop, 'JDGStore', '京东商城-超市'), //京东超市
            JDUserSignPre(stop, 'JDBook', '京东商城-图书') //京东图书
        ]);
        await Promise.all([
            JDUserSignPre(stop, 'JDPet', '京东商城-宠物'), //京东宠物馆
            JDUserSignPre(stop, 'JDShand', '京东拍拍-二手'), //京东拍拍二手
            JDUserSignPre(stop, 'JDClean', '京东商城-清洁'), //京东清洁馆
            JDUserSignPre(stop, 'JDCare', '京东商城-个护'), //京东个人护理馆
            JDUserSignPre(stop, 'JDJewels', '京东商城-珠宝'), //京东珠宝馆
            JDUserSignPre(stop, 'JDClocks', '京东商城-钟表'), //京东钟表馆
            JDUserSignPre(stop, 'JDMakeup', '京东商城-美妆'), //京东美妆馆
            JDUserSignPre(stop, 'JDVege', '京东商城-菜场'), //京东菜场
            JDUserSignPre(stop, 'JDFood', '京东商城-美食') //京东美食馆
        ]);
    } else {
        // await JingDongBean(stop); //京东京豆
        // await JingRongBean(stop); //金融京豆
        // await JingRongDoll(stop); //金融抓娃娃
        // await JingRongSteel(stop); //金融钢镚
        // await JingDongTurn(stop); //京东转盘
        // await JDFlashSale(stop); //京东闪购
        // await JDOverseas(stop); //京东国际
        // await JingDongCash(stop); //京东现金红包
        // await JDMagicCube(stop); //京东小魔方
        // await JingDongGetCash(stop); //京东领现金
        // await JingDongPrize(stop); //京东抽大奖
        // await JingDongSubsidy(stop); //京东金贴
        // await JingDongShake(stop) //京东摇一摇
        await JDUserSignPre(stop, 'JDTreasure', '京东商城-夺宝'); //京东夺宝岛
        await JDUserSignPre(stop, 'JDBaby', '京东商城-母婴'); //京东母婴馆
        await JDUserSignPre(stop, 'JD3C', '京东商城-数码'); //京东数码电器馆
        await JDUserSignPre(stop, 'JDSubsidy', '京东晚市-补贴'); //京东晚市补贴金
        await JDUserSignPre(stop, 'JDClocks', '京东商城-钟表'); //京东钟表馆
        await JDUserSignPre(stop, 'JDDrug', '京东商城-医药'); //京东医药馆
        await JDUserSignPre(stop, 'JDGStore', '京东商城-超市'); //京东超市
        await JDUserSignPre(stop, 'JDPet', '京东商城-宠物'); //京东宠物馆
        await JDUserSignPre(stop, 'JDBook', '京东商城-图书'); //京东图书
        await JDUserSignPre(stop, 'JDShand', '京东拍拍-二手'); //京东拍拍二手
        await JDUserSignPre(stop, 'JDMakeup', '京东商城-美妆'); //京东美妆馆
        await JDUserSignPre(stop, 'JDWomen', '京东商城-女装'); //京东女装馆
        await JDUserSignPre(stop, 'JDVege', '京东商城-菜场'); //京东菜场
        await JDUserSignPre(stop, 'JDFood', '京东商城-美食'); //京东美食馆
        await JDUserSignPre(stop, 'JDClean', '京东商城-清洁'); //京东清洁馆
        await JDUserSignPre(stop, 'JDCare', '京东商城-个护'); //京东个人护理馆
        await JDUserSignPre(stop, 'JDJewels', '京东商城-珠宝'); //京东珠宝馆
    }
    await Promise.all([
        JingDongSpeedUp(stop), //京东天天加速
        JRDoubleSign(stop) //金融双签
    ])
    await Promise.all([
        TotalSteel(), //总钢镚查询
        TotalCash(), //总红包查询
        TotalBean() //总京豆查询
    ])
    await notify(); //通知模块
}

function notify() {
    return new Promise(resolve => {
        try {
            var bean = 0;
            var steel = 0;
            var success = 0;
            var fail = 0;
            var err = 0;
            var notify = '';
            for (var i in merge) {
                bean += Number(merge[i].bean)
                steel += Number(merge[i].steel)
                success += Number(merge[i].success)
                fail += Number(merge[i].fail)
                err += Number(merge[i].error)
                notify += merge[i].notify ? "\n" + merge[i].notify : ""
            }
            var Cash = merge.JDCash.TCash ? merge.JDCash.TCash + "红包" : ""
            var Steel = merge.JRSteel.TSteel ? merge.JRSteel.TSteel + "钢镚" + (Cash ? ", " : "") : ""
            var beans = merge.JDShake.Qbear ? merge.JDShake.Qbear + "京豆" + (Steel || Cash ? ", " : "") : ""
            var bsc = beans ? "\n" : Steel ? "\n" : Cash ? "\n" : "获取失败\n"
            var Tbean = bean ? bean + "京豆" + (steel || merge.JDCash.Cash ? ", " : "") : ""
            var TSteel = steel ? steel + "钢镚" + (merge.JDCash.Cash ? ", " : "") : ""
            var TCash = merge.JDCash.Cash ? merge.JDCash.Cash + "红包" : ""
            var Tbsc = Tbean ? "\n" : TSteel ? "\n" : TCash ? "\n" : "获取失败\n"
            var Ts = success ? "成功" + success + "个" + (fail || err ? ", " : "") : ""
            var Tf = fail ? "失败" + fail + "个" + (err ? ", " : "") : ""
            var Te = err ? "错误" + err + "个\n" : success ? "\n" : fail ? "\n" : "获取失败\n"
            var one = "【签到概览】:  " + Ts + Tf + Te
            var two = "【签到总计】:  " + Tbean + TSteel + TCash + Tbsc
            var three = "【账号总计】:  " + beans + Steel + Cash + bsc
            var four = "【左滑 '查看' 以显示签到详情】\n"
            var disa = $nobyda.disable ? "\n检测到上次执行意外崩溃, 已为您自动禁用相关接口. 如需开启请前往BoxJs ‼️‼️\n" : ""
            var DName = merge.JDShake.nickname ? merge.JDShake.nickname : "获取失败"
            var Name = add ? DualAccount ? "【签到号一】:  " + DName + "\n" : "【签到号二】:  " + DName + "\n" : ""
            console.log("\n" + Name + one + two + three + four + disa + notify)
            if ($nobyda.isJSBox) {
                if (add && DualAccount) {
                    Shortcut = Name + one + two + three + "\n"
                } else if (!add && DualAccount) {
                    $intents.finish(Name + one + two + three + four + notify)
                } else if (typeof Shortcut != "undefined") {
                    $intents.finish(Shortcut + Name + one + two + three)
                }
            }
            if (!$nobyda.isNode) $nobyda.notify("", "", Name + one + two + three + four + disa + notify);
            if (DualAccount) {
                double();
            } else {
                $nobyda.time();
                $nobyda.done();
            }
        } catch (eor) {
            $nobyda.notify("通知模块 " + eor.name + "‼️", JSON.stringify(eor), eor.message)
        } finally {
            resolve()
        }
    });
}

function double() {
    initial()
    add = true
    DualAccount = false
    if ($nobyda.isJSBox) {
        if (DualKey || $file.exists("shared://JD_Cookie2.txt")) {
            KEY = DualKey ? DualKey : $file.read("shared://JD_Cookie2.txt").string
            all()
        } else {
            $nobyda.time();
        }
    } else if (DualKey || $nobyda.read("CookieJD2")) {
        KEY = DualKey ? DualKey : $nobyda.read("CookieJD2")
        all()
    } else {
        $nobyda.time();
        $nobyda.done();
    }
}

function JRDoubleSign(s) {
    return new Promise(resolve => {
        if (disable("JRDSign")) return resolve()
        setTimeout(() => {
            const JRDSUrl = {
                url: 'https://nu.jr.jd.com/gw/generic/jrm/h5/m/process?',
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    Cookie: KEY,
                },
                body: "reqData=%7B%22actCode%22%3A%22FBBFEC496C%22%2C%22type%22%3A3%2C%22riskDeviceParam%22%3A%22%22%7D"
            };
            $nobyda.post(JRDSUrl, function (error, response, data) {
                try {
                    if (error) {
                        throw new Error(error)
                    } else {
                        const Details = LogDetails ? "response:\n" + data : '';
                        if (data.match(/\"resultCode\":0/)) {
                            if (data.match(/\"count\":\d+/)) {
                                console.log("\n" + "京东金融-双签签到成功 " + Details)
                                merge.JRDSign.bean = data.match(/\"count\":(\d+)/)[1]
                                merge.JRDSign.notify = "京东金融-双签: 成功, 明细: " + merge.JRDSign.bean + "京豆 🐶"
                                merge.JRDSign.success = 1
                            } else {
                                console.log("\n" + "京东金融-双签签到失败 " + Details)
                                merge.JRDSign.fail = 1
                                if (data.match(/已领取/)) {
                                    merge.JRDSign.notify = "京东金融-双签: 失败, 原因: 已签过 ⚠️"
                                } else if (data.match(/未在/)) {
                                    merge.JRDSign.notify = "京东金融-双签: 失败, 原因: 未在京东签到 ⚠️"
                                } else {
                                    merge.JRDSign.notify = "京东金融-双签: 失败, 原因: 无奖励 🐶"
                                }
                            }
                        } else {
                            console.log("\n" + "京东金融-双签签到失败 " + Details)
                            merge.JRDSign.fail = 1
                            if (data.match(/(\"resultCode\":3|请先登录)/)) {
                                merge.JRDSign.notify = "京东金融-双签: 失败, 原因: Cookie失效‼️"
                            } else {
                                merge.JRDSign.notify = "京东金融-双签: 失败, 原因: 未知 ⚠️"
                            }
                        }
                    }
                } catch (eor) {
                    $nobyda.AnError("京东金融-双签", "JRDSign", eor)
                } finally {
                    resolve()
                }
            })
        }, s)
        if (out) setTimeout(resolve, out + s)
    });
}

function JDUserSignPre(s, key, title) {
    if ($nobyda.isNode) {
        return JDUserSignPre1(s, key, title);
    } else if (key == 'JDWomen' || key == 'JDJewels' || $nobyda.isJSBox) {
        return JDUserSignPre2(s, key, title);
    } else {
        return JDUserSignPre1(s, key, title);
    }
}

function JDUserSignPre1(s, key, title, ask) {
    return new Promise((resolve, reject) => {
        if (disable(key, title, 1)) return reject()
        const JDUrl = {
            url: 'https://api.m.jd.com/?client=wh5&functionId=qryH5BabelFloors',
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                Cookie: KEY,
            },
            body: `body=${encodeURIComponent(`{"activityId":"${acData[key]}"${ask ? `,"paginationParam":"2",${ask}` : ``}}`)}`
        };
        $nobyda.post(JDUrl, async function (error, response, data) {
            try {
                if (error) {
                    throw new Error(error)
                } else {
                    const turnTableId = data.match(/\"turnTableId\":\"(\d+)\"/)
                    const page = data.match(/\"paginationFlrs\":\"\[\[.+?\]\]\"/)
                    if (data.match(/enActK/)) { // 含有签到活动数据
                        const od = JSON.parse(data);
                        let params = (od.floatLayerList || []).filter(o => o.params && o.params.match(/enActK/)).map(o => o.params).pop();
                        if (!params) { // 第一处找到签到所需数据
                            // floatLayerList未找到签到所需数据，从floorList中查找
                            let signInfo = (od.floorList || []).filter(o => o.template == 'signIn' && o.signInfos && o.signInfos.params && o.signInfos.params.match(/enActK/))
                                .map(o => o.signInfos).pop();
                            if (signInfo) {
                                if (signInfo.signStat == '1') {
                                    console.log(`\n${title}重复签到`)
                                    merge[key].notify = `${title}: 失败, 原因: 已签过 ⚠️`
                                    merge[key].fail = 1
                                } else {
                                    params = signInfo.params;
                                }
                            } else {
                                merge[key].notify = `${title}: 失败, 活动查找异常 ⚠️`
                                merge[key].fail = 1
                            }
                        }
                        if (params) {
                            return resolve({
                                params: params
                            }); // 执行签到处理
                        }
                    } else if (turnTableId) { // 无签到数据, 但含有关注店铺签到
                        const boxds = $nobyda.read("JD_Follow_disable") === "false" ? false : true
                        if (boxds) {
                            console.log(`\n${title}关注店铺`)
                            return resolve(parseInt(turnTableId[1]))
                        } else {
                            merge[key].notify = `${title}: 失败, 需要关注店铺 ⚠️`
                            merge[key].fail = 1
                        }
                    } else if (page && !ask) { // 无签到数据, 尝试带参查询
                        const boxds = $nobyda.read("JD_Retry_disable") === "false" ? false : true
                        if (boxds) {
                            console.log(`\n${title}二次查询`)
                            return resolve(page[0])
                        } else {
                            merge[key].notify = `${title}: 失败, 请尝试开启增强 ⚠️`
                            merge[key].fail = 1
                        }
                    } else {
                        merge[key].notify = `${title}: 失败, ${!data ? `需要手动执行` : `不含活动数据`} ⚠️`
                        merge[key].fail = 1
                    }
                }
                disable(key, title, 2)
                reject()
            } catch (eor) {
                $nobyda.AnError(title, key, eor)
                disable(key, title, 2)
                reject()
            }
        })
        if (out) setTimeout(reject, out + s)
    }).then(data => {
        disable(key, title, 2)
        if (typeof (data) == "object") return JDUserSign1(s, key, title, encodeURIComponent(JSON.stringify(data)));
        if (typeof (data) == "number") return JDUserSign2(s, key, title, data);
        if (typeof (data) == "string") return JDUserSignPre1(s, key, title, data);
    }, () => {
    })
}

function JDUserSignPre2(s, key, title) {
    return new Promise((resolve, reject) => {
        if (disable(key, title, 1)) return reject()
        const JDUrl = {
            url: `https://pro.m.jd.com/mall/active/${acData[key]}/index.html`,
            headers: {
                Cookie: KEY,
            }
        };
        $nobyda.get(JDUrl, async function (error, response, data) {
            try {
                if (error) {
                    throw new Error(error)
                } else {
                    const act = data.match(/\"params\":\"\{\\\"enActK.+?\\\"\}\"/)
                    const turnTable = data.match(/\"turnTableId\":\"(\d+)\"/)
                    const page = data.match(/\"paginationFlrs\":\"\[\[.+?\]\]\"/)
                    if (act) { // 含有签到活动数据
                        return resolve(act)
                    } else if (turnTable) { // 无签到数据, 但含有关注店铺签到
                        const boxds = $nobyda.read("JD_Follow_disable") === "false" ? false : true
                        if (boxds) {
                            console.log(`\n${title}关注店铺`)
                            return resolve(parseInt(turnTable[1]))
                        } else {
                            merge[key].notify = `${title}: 失败, 需要关注店铺 ⚠️`
                            merge[key].fail = 1
                        }
                    } else if (page) { // 无签到数据, 尝试带参查询
                        const boxds = $nobyda.read("JD_Retry_disable") === "false" ? false : true
                        if (boxds) {
                            console.log(`\n${title}二次查询`)
                            return resolve(page[0])
                        } else {
                            merge[key].notify = `${title}: 失败, 请尝试开启增强 ⚠️`
                            merge[key].fail = 1
                        }
                    } else {
                        merge[key].notify = `${title}: 失败, ${!data ? `需要手动执行` : `不含活动数据`} ⚠️`
                        merge[key].fail = 1
                    }
                }
                disable(key, title, 2)
                reject()
            } catch (eor) {
                $nobyda.AnError(title, key, eor)
                disable(key, title, 2)
                reject()
            }
        })
        if (out) setTimeout(reject, out + s)
    }).then(data => {
        disable(key, title, 2)
        if (typeof (data) == "object") return JDUserSign1(s, key, title, encodeURIComponent(`{${data}}`));
        if (typeof (data) == "number") return JDUserSign2(s, key, title, data)
        if (typeof (data) == "string") return JDUserSignPre1(s, key, title, data)
    }, () => {
    })
}

function JDUserSign1(s, key, title, body) {
    return new Promise(resolve => {
        setTimeout(() => {
            const JDUrl = {
                url: 'https://api.m.jd.com/client.action?functionId=userSign',
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    Cookie: KEY,
                },
                body: `body=${body}&client=wh5`
            };
            $nobyda.post(JDUrl, function (error, response, data) {
                try {
                    if (error) {
                        throw new Error(error)
                    } else {
                        const Details = LogDetails ? `response:\n${data}` : '';
                        const cc = JSON.parse(data)
                        if (data.match(/签到成功/)) {
                            console.log(`\n${title}签到成功(1)${Details}`)
                            if (data.match(/(\"text\":\"\d+京豆\")/)) {
                                let beanQuantity = cc.awardList[0].text.match(/\d+/)
                                merge[key].notify = `${title}: 成功, 明细: ${beanQuantity}京豆 🐶`
                                merge[key].bean = beanQuantity
                                merge[key].success = 1
                            } else {
                                merge[key].notify = `${title}: 成功, 明细: 无京豆 🐶`
                                merge[key].success = 1
                            }
                        } else {
                            console.log(`\n${title}签到失败(1)${Details}`)
                            if (data.match(/(已签到|已领取)/)) {
                                merge[key].notify = `${title}: 失败, 原因: 已签过 ⚠️`
                            } else if (data.match(/(不存在|已结束|未开始)/)) {
                                merge[key].notify = `${title}: 失败, 原因: 活动已结束 ⚠️`
                            } else if (cc.code == 3) {
                                merge[key].notify = `${title}: 失败, 原因: Cookie失效‼️`
                            } else {
                                merge[key].notify = `${title}: 失败, 原因: 未知 ⚠️`
                            }
                            merge[key].fail = 1
                        }
                    }
                } catch (eor) {
                    $nobyda.AnError(title, key, eor)
                } finally {
                    resolve()
                }
            })
        }, s)
        if (out) setTimeout(resolve, out + s)
    });
}

async function JDUserSign2(s, key, title, tid) {
    await new Promise(resolve => {
        $nobyda.get({
            url: `https://jdjoy.jd.com/api/turncard/channel/detail?turnTableId=${tid}`,
            headers: {
                Cookie: KEY
            }
        }, function (error, response, data) {
            resolve()
        })
        if (out) setTimeout(resolve, out + s)
    });
    return new Promise(resolve => {
        setTimeout(() => {
            const JDUrl = {
                url: 'https://jdjoy.jd.com/api/turncard/channel/sign',
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    Cookie: KEY,
                },
                body: `turnTableId=${tid}`
            };
            $nobyda.post(JDUrl, function (error, response, data) {
                try {
                    if (error) {
                        throw new Error(error)
                    } else {
                        const Details = LogDetails ? `response:\n${data}` : '';
                        const cc = JSON.parse(data)
                        if (cc.success == true) {
                            console.log(`\n${title}签到成功(2)${Details}`)
                            if (data.match(/\"jdBeanQuantity\":\d+/)) {
                                merge[key].notify = `${title}: 成功, 明细: ${cc.data.jdBeanQuantity}京豆 🐶`
                                merge[key].bean = cc.data.jdBeanQuantity
                            } else {
                                merge[key].notify = `${title}: 成功, 明细: 无京豆 🐶`
                            }
                            merge[key].success = 1
                        } else {
                            console.log(`\n${title}签到失败(2)${Details}`)
                            if (data.match(/(已经签到|已经领取)/)) {
                                merge[key].notify = `${title}: 失败, 原因: 已签过 ⚠️`
                            } else if (data.match(/(不存在|已结束|未开始)/)) {
                                merge[key].notify = `${title}: 失败, 原因: 活动已结束 ⚠️`
                            } else if (data.match(/(没有登录|B0001)/)) {
                                merge[key].notify = `${title}: 失败, 原因: Cookie失效‼️`
                            } else {
                                merge[key].notify = `${title}: 失败, 原因: 未知 ⚠️`
                            }
                            merge[key].fail = 1
                        }
                    }
                } catch (eor) {
                    $nobyda.AnError(title, key, eor)
                } finally {
                    resolve()
                }
            })
        }, s)
        if (out) setTimeout(resolve, out + s)
    });
}

function JDUseProps(s, PropID) {
    return new Promise(resolve => {
        if (PropID) {
            setTimeout(() => {
                var PropCount = 0
                var PropNumTask = 0
                for (var i = 0; i < PropID.length; i++) {
                    const PropUrl = {
                        url: '' + PropID[i] + '%22%7D',
                        headers: {
                            Referer: 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
                            Cookie: KEY
                        }
                    };
                    $nobyda.get(PropUrl, function (error, response, data) {
                        try {
                            PropCount++
                            if (error) {
                                throw new Error(error)
                            } else {
                                const cc = JSON.parse(data)
                                const Details = LogDetails ? "response:\n" + data : '';
                                console.log("\n天天加速-尝试使用第" + PropCount + "个道具" + Details)
                                if (cc.message == 'success' && cc.success == true) {
                                    PropNumTask += 1
                                }
                            }

                        } catch (eor) {
                            $nobyda.AnError("使用道具-加速", "SpeedUp", eor)
                        } finally {
                            if (PropID.length == PropCount) {
                                console.log("\n天天加速-已成功使用" + PropNumTask + "个道具")
                                resolve()
                            }
                        }
                    })
                }
            }, s)
            if (out) setTimeout(resolve, out + s)
        } else {
            resolve()
        }
    });
}

function TotalSteel() {
    return new Promise(resolve => {
        if (disable("TSteel")) return resolve()
        const SteelUrl = {
            url: 'https://coin.jd.com/m/gb/getBaseInfo.html',
            headers: {
                Cookie: KEY,
            }
        };
        $nobyda.get(SteelUrl, function (error, response, data) {
            try {
                if (!error) {
                    const Details = LogDetails ? "response:\n" + data : '';
                    if (data.match(/(\"gbBalance\":\d+)/)) {
                        console.log("\n" + "京东-总钢镚查询成功 " + Details)
                        const cc = JSON.parse(data)
                        merge.JRSteel.TSteel = cc.gbBalance
                    } else {
                        console.log("\n" + "京东-总钢镚查询失败 " + Details)
                    }
                } else {
                    throw new Error(error)
                }
            } catch (eor) {
                $nobyda.AnError("账户钢镚-查询", "JRSteel", eor)
            } finally {
                resolve()
            }
        })
        if (out) setTimeout(resolve, out)
    });
}

function TotalBean() {
    return new Promise(resolve => {
        if (disable("Qbear")) return resolve()
        const BeanUrl = {
            url: 'https://wq.jd.com/user/info/QueryJDUserInfo?sceneval=2',
            headers: {
                Cookie: KEY,
                Referer: "https://wqs.jd.com/my/jingdou/my.shtml?sceneval=2"
            }
        };
        $nobyda.post(BeanUrl, function (error, response, data) {
            try {
                if (!error) {
                    const Details = LogDetails ? "response:\n" + data : '';
                    const cc = JSON.parse(data)
                    if (cc.base.jdNum != 0) {
                        console.log("\n" + "京东-总京豆查询成功 " + Details)
                        merge.JDShake.Qbear = cc.base.jdNum
                    } else {
                        console.log("\n" + "京东-总京豆查询失败 " + Details)
                    }
                    if (data.match(/\"nickname\" ?: ?\"(.+?)\",/)) {
                        merge.JDShake.nickname = cc.base.nickname
                    } else if (data.match(/\"no ?login\.?\"/)) {
                        merge.JDShake.nickname = "Cookie失效 ‼️"
                    } else {
                        merge.JDShake.nickname = '';
                    }
                } else {
                    throw new Error(error)
                }
            } catch (eor) {
                $nobyda.AnError("账户京豆-查询", "JDShake", eor)
            } finally {
                resolve()
            }
        })
        if (out) setTimeout(resolve, out)
    });
}

function TotalCash() {
    return new Promise(resolve => {
        if (disable("TCash")) return resolve()
        const CashUrl = {
            url: 'https://api.m.jd.com/client.action?functionId=myhongbao_balance',
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                Cookie: KEY,
            },
            body: "body=%7B%22fp%22%3A%22-1%22%2C%22appToken%22%3A%22apphongbao_token%22%2C%22childActivityUrl%22%3A%22-1%22%2C%22country%22%3A%22cn%22%2C%22openId%22%3A%22-1%22%2C%22childActivityId%22%3A%22-1%22%2C%22applicantErp%22%3A%22-1%22%2C%22platformId%22%3A%22appHongBao%22%2C%22isRvc%22%3A%22-1%22%2C%22orgType%22%3A%222%22%2C%22activityType%22%3A%221%22%2C%22shshshfpb%22%3A%22-1%22%2C%22platformToken%22%3A%22apphongbao_token%22%2C%22organization%22%3A%22JD%22%2C%22pageClickKey%22%3A%22-1%22%2C%22platform%22%3A%221%22%2C%22eid%22%3A%22-1%22%2C%22appId%22%3A%22appHongBao%22%2C%22childActiveName%22%3A%22-1%22%2C%22shshshfp%22%3A%22-1%22%2C%22jda%22%3A%22-1%22%2C%22extend%22%3A%22-1%22%2C%22shshshfpa%22%3A%22-1%22%2C%22activityArea%22%3A%22-1%22%2C%22childActivityTime%22%3A%22-1%22%7D&client=apple&clientVersion=8.5.0&d_brand=apple&networklibtype=JDNetworkBaseAF&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&sign=fdc04c3ab0ee9148f947d24fb087b55d&st=1581245397648&sv=120"
        };
        $nobyda.post(CashUrl, function (error, response, data) {
            try {
                if (!error) {
                    const Details = LogDetails ? "response:\n" + data : '';
                    if (data.match(/(\"totalBalance\":\d+)/)) {
                        console.log("\n" + "京东-总红包查询成功 " + Details)
                        const cc = JSON.parse(data)
                        merge.JDCash.TCash = cc.totalBalance
                    } else {
                        console.log("\n" + "京东-总红包查询失败 " + Details)
                    }
                } else {
                    throw new Error(error)
                }
            } catch (eor) {
                $nobyda.AnError("账户红包-查询", "JDCash", eor)
            } finally {
                resolve()
            }
        })
        if (out) setTimeout(resolve, out)
    });
}

function disable(Val, name, way) {
    const read = $nobyda.read("JD_DailyBonusDisables")
    const annal = $nobyda.read("JD_Crash_" + Val)
    if (annal && way == 1 && boxdis) {
        var Crash = $nobyda.write("", "JD_Crash_" + Val)
        if (read) {
            if (read.indexOf(Val) == -1) {
                var Crash = $nobyda.write(`${read},${Val}`, "JD_DailyBonusDisables")
                console.log(`\n${name}-触发自动禁用 ‼️`)
                merge[Val].notify = `${name}: 崩溃, 触发自动禁用 ‼️`
                merge[Val].error = 1
                $nobyda.disable = 1
            }
        } else {
            var Crash = $nobyda.write(Val, "JD_DailyBonusDisables")
            console.log(`\n${name}-触发自动禁用 ‼️`)
            merge[Val].notify = `${name}: 崩溃, 触发自动禁用 ‼️`
            merge[Val].error = 1
            $nobyda.disable = 1
        }
        return true
    } else if (way == 1 && boxdis) {
        var Crash = $nobyda.write(name, "JD_Crash_" + Val)
    } else if (way == 2 && annal) {
        var Crash = $nobyda.write("", "JD_Crash_" + Val)
    }
    if (read && read.indexOf(Val) != -1) {
        return true
    } else {
        return false
    }
}

function GetCookie() {
    try {
        if ($request.headers && $request.url.match(/api\.m\.jd\.com.*=signBean/)) {
            var CV = $request.headers['Cookie']
            if (CV.match(/(pt_key=.+?pt_pin=|pt_pin=.+?pt_key=)/)) {
                var CookieValue = CV.match(/pt_key=.+?;/) + CV.match(/pt_pin=.+?;/)
                var CK1 = $nobyda.read("CookieJD")
                var CK2 = $nobyda.read("CookieJD2")
                var AccountOne = CK1 ? CK1.match(/pt_pin=.+?;/) ? CK1.match(/pt_pin=(.+?);/)[1] : null : null
                var AccountTwo = CK2 ? CK2.match(/pt_pin=.+?;/) ? CK2.match(/pt_pin=(.+?);/)[1] : null : null
                var UserName = CookieValue.match(/pt_pin=(.+?);/)[1]
                var DecodeName = decodeURIComponent(UserName)
                if (!AccountOne || UserName == AccountOne) {
                    var CookieName = " [账号一] ";
                    var CookieKey = "CookieJD";
                } else if (!AccountTwo || UserName == AccountTwo) {
                    var CookieName = " [账号二] ";
                    var CookieKey = "CookieJD2";
                } else {
                    $nobyda.notify("更新京东Cookie失败", "非历史写入账号 ‼️", '请开启脚本内"DeleteCookie"以清空Cookie ‼️')
                    $nobyda.done()
                    return
                }
            } else {
                $nobyda.notify("写入京东Cookie失败", "", "请查看脚本内说明, 登录网页获取 ‼️")
                $nobyda.done()
                return
            }
            if ($nobyda.read(CookieKey)) {
                if ($nobyda.read(CookieKey) != CookieValue) {
                    var cookie = $nobyda.write(CookieValue, CookieKey);
                    if (!cookie) {
                        $nobyda.notify("用户名: " + DecodeName, "", "更新京东" + CookieName + "Cookie失败 ‼️");
                    } else {
                        $nobyda.notify("用户名: " + DecodeName, "", "更新京东" + CookieName + "Cookie成功 🎉");
                    }
                } else {
                    console.log("京东: \n与历史Cookie相同, 跳过写入")
                }
            } else {
                var cookie = $nobyda.write(CookieValue, CookieKey);
                if (!cookie) {
                    $nobyda.notify("用户名: " + DecodeName, "", "首次写入京东" + CookieName + "Cookie失败 ‼️");
                } else {
                    $nobyda.notify("用户名: " + DecodeName, "", "首次写入京东" + CookieName + "Cookie成功 🎉");
                }
            }
        } else {
            $nobyda.notify("写入京东Cookie失败", "", "请检查匹配URL或配置内脚本类型 ‼️");
        }
    } catch (eor) {
        $nobyda.write("", "CookieJD")
        $nobyda.write("", "CookieJD2")
        $nobyda.notify("写入京东Cookie失败", "", '已尝试清空历史Cookie, 请重试 ⚠️')
        console.log(`\n写入京东Cookie出现错误 ‼️\n${JSON.stringify(eor)}\n\n${eor}\n\n${JSON.stringify($request.headers)}\n`)
    }
    $nobyda.done()
}

ReadCookie();
