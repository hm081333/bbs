$(document).ready(function () {
    // var id = location.hash.split('#')[1];
    // id = id || 'index';
    // page_href_id(id);
    page_href_id('index'); //第一次访问时加载首页

    $("body").on("click", function (event) {
        $click = $(event.target);
        if ($click.is("a.btn-link")) {
            event.preventDefault(); //阻止默认操作 - 跳转href地址
            var id = $click.attr('href').split('#')[1];
            var data = $click.data() || {};
            setData(data);
            page_href_id(id);
            return false;
        } else if ($click.parent('a.btn-back').length > 0) {
            event.preventDefault(); //阻止默认操作 - 跳转href地址
            backLastStep();
            return false;
        }
        console.log($click);
        // return false;
    });

});


var obj = {};
/*追加页面对象的写法*/
obj['index'] = function () {
    var data = getData({service: 'Default.Main'});
    pageInit(data, 'index');
};
obj['topic_list'] = function () {
    var data = getData({service: 'Topic.Topic_List'});
    // var data = {service: 'Topic.Topic_List', class_id: data.id};
    pageInit(data, 'topic_list');
};
obj['topic_info'] = function () {
    var data = getData({service: 'Topic.Topic'});
    pageInit(data, 'topic_info');
};
obj['user_info'] = function () {
    var data = getData({service: 'User.User_Info'});
    pageInit(data, 'user_info');
};

function page_href_id(id) {
    var obj_func_name = 'obj.' + id;
    if (typeof(eval(obj_func_name)) === "function") {
        eval(obj_func_name + '()');
    } else {
        // 函数不存在
    }
}

function pageInit(data, id, func) {
    Ajax(data, function (d) {
        if ($('#' + id).length > 0) {
            $('#' + id).empty();
            $('#' + id).html(d.data);
        } else {
            var html = "";
            html += '<div class="page page-inited" id="' + id + '">';
            html += d.data;
            html += '</div>';
            $('#Content').append(html);
        }
        afterPageLoad();//初始化所有必要的框架初始化
        $('body,html').animate({
            scrollTop: 0
        }, 0);
        if (id !== "index") {
            $('header nav a.btn-back').show();
        } else {
            $('header nav a.btn-back').hide();
        }
        $('.page-current').removeClass('page-current');
        $('#' + id).addClass('page-current');
        goNextStep([id]);
    });
}

function pageReInit(id, func) {
    afterPageLoad();//初始化所有必要的框架初始化
    $('body,html').animate({
        scrollTop: 0
    }, 0);
    if (id !== "index") {
        $('header nav a.btn-back').show();
    } else {
        $('header nav a.btn-back').hide();
    }
    $('.page-current').removeClass('page-current');
    $('#' + id).addClass('page-current');
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


function setStep(data) {
    localStorage.setItem("href_step", JSON.stringify(data));//转化为JSON字符串 存储 localStorage - H5本地存储
}

function getStep() {
    return JSON.parse(localStorage.getItem("href_step")); //读取本地存储
}

function goNextStep(data) {
    if (data[0] !== "index") {
        var href_step = getStep(); //读取本地存储
        data = href_step.concat(data);
    }
    setStep(data);
}

function backLastStep() {
    var href_step = getStep(); //读取本地存储
    var now_index = href_step.length - 1;
    var now_page = href_step[now_index];
    if (now_page === "index") {
        return false;
    }
    var last_index = href_step.length - 2;
    var last_page = href_step[last_index];
    href_step.splice(now_index, 1);
    pageReInit(last_page);
    setStep(href_step);
}

