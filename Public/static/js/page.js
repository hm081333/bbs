var href_id = "index";
var href_data = {};
var href_step = [];
var obj = {};
/*追加页面对象的写法*/
obj['index'] = function () {
    // var data = getData({service: 'Default.Main'});
    pageInit({service: 'Default.Main'});
};
obj['topic_list'] = function () {
    pageInit({service: 'Topic.Topic_List'});
};
obj['topic_info'] = function () {
    pageInit({service: 'Topic.Topic'});
};
obj['user_info'] = function () {
    pageInit({service: 'User.User_Info'});
};
obj['edit_member'] = function () {
    pageInit({service: 'User.Edit_Member'});
};
obj['create_topic'] = function () {
    pageInit({service: 'Topic.Create_Topic'});
};
obj['delivery_list'] = function () {
    pageInit({service: 'Default.DeliveryList'});

    bindClick("#delivery_list a.delivery_id", function () {
        sendButtomAjax($(this), function (d) {
            if (d.ret == 200) {
                $('#delivery').html(d.data);
                $('.collapsible').collapsible();
                $('.collapsible').collapsible('open', 0);
                $('#delivery').modal('open');
            } else {
                Materialize.toast(d.msg, 2000, 'rounded');
            }
        });
    });
};
obj['login'] = function () {
    pageInit({service: 'User.Login'});

    sendFormAjax("#login form")
};


$(document).ready(function () {
    // href_id = location.hash.split('#')[1];
    page_href_id(); //第一次访问时加载首页

    $("body").on("click", "a", function (event) {//监听body下所有a标签的点击事件
        $click = $(event.currentTarget);//当前点击的对象
        if ($click.is("a.btn-link")) {
            event.preventDefault(); //阻止默认操作 - 跳转href地址
            var click_id = $click.attr('href').split('#')[1];
            if (click_id === href_id) {
                return false;
            }
            href_id = click_id;
            href_data = $click.data() || {};
            // var data = $click.data() || {};
            // setData(data);
            page_href_id();
            return false;
        } else if ($click.is("a.btn-back")) {
            event.preventDefault(); //阻止默认操作 - 跳转href地址
            backLastStep();
            return false;
        }
        // console.log($click);
        // return false;
    });

});


/*调起页面对象函数*/
function page_href_id() {
    var obj_func_name = 'obj.' + href_id;
    if (typeof(eval(obj_func_name)) === "function") {
        eval(obj_func_name + '()');
    } else {
        // 函数不存在
    }
}

function pageInit(data, func) {
    $.extend(data, href_data);
    Ajax(data, function (d) {
        if ($('#' + href_id).length > 0) {
            $('#' + href_id).empty();
            $('#' + href_id).html(d.data);
        } else {
            var html = "";
            html += '<div class="page page-inited" id="' + href_id + '">';
            html += d.data;
            html += '</div>';
            $('#Content').append(html);
        }
        afterPageLoad();//初始化所有必要的框架初始化
        backBtn();
        goNextStep();
        $('.page-current').removeClass('page-current');
        $('#' + href_id).addClass('page-current');
        // console.log(href_id);
        // console.log(href_step);
    });
}

function pageReInit(func) {
    afterPageLoad();//初始化所有必要的框架初始化
    backBtn();
    $('.page-current').removeClass('page-current');
    $('#' + href_id).addClass('page-current');
}

function backBtn() {
    if (href_id === "index") {
        $('header nav a.btn-back').hide();
    } else {
        $('header nav a.btn-back').show();
    }
}

/**
 * 传入传值数组，存入localStorage本地存储
 * @param data
 */
function setData(data) {
    localStorage.setItem("href_data", JSON.stringify(data));//转化为JSON字符串 存储 localStorage - H5本地存储
}

/**
 * 传入api地址，读取localStorage本地存储数组，合并成一个完成的请求数组
 * @param data
 * @returns array
 */
function getData(data) {
    var href_data = JSON.parse(localStorage.getItem("href_data")); //读取本地存储
    return $.extend(data, href_data);
}

/**
 * 设置页面步进
 * @param data
 */
function setStep(data) {
    localStorage.setItem("href_step", JSON.stringify(data));//转化为JSON字符串 存储 localStorage - H5本地存储
}

/**
 * 获取页面步进
 * @returns array
 */
function getStep() {
    return JSON.parse(localStorage.getItem("href_step")); //读取本地存储
}

/**
 * 跳转新页面时候步进添加新页面id
 */
function goNextStep() {
    // var href_step = [];
    if (href_id === "index") {
        href_step = [href_id];
    } else {
        // href_step = getStep(); //读取本地存储
        href_step = href_step.concat([href_id]);
    }
    // setStep(href_step);
}

function backLastStep() {
    // var href_step = getStep(); //读取本地存储
    var now_index = href_step.length - 1;//现在所在页面的id的index
    var now_page = href_step[now_index];//现在所在页面的id
    if (now_page === "index") {//在首页就不返回了
        return false;
    }
    var last_index = href_step.length - 2;//上一个页面id的index
    href_id = href_step[last_index];//上一个页面的id
    href_step.splice(now_index, 1);//删除一个id
    pageReInit();//重载页面
    // setStep(href_step);//重设页面步进
}

