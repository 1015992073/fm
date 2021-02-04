
/**
 * 插件名: 通过ajax获取数据
 * 作者：@jeff liu
 * 说明:
 * 使用说明
 * 基于jquery 的ajax，重构异步加载数据
 * 使用方法
 * 一个： $.GetDataByAjax(url, data)).done(function (data) {
*  console.log(data).fail(function(){console.log("错误")});;
*  })
* 多个同时: $.when($.GetDataByAjax(url, data),$.GetDataByAjax(url2, data2)).done(function (data,data2) {
 console.log(data,data2);
 })
* 
* 
 */
; var num = 0;//记录当前页面异步数据个数，如果不为0，那么背景不消失
jQuery.GetDataByAjax = function (url, data, showbg) {
    var showzz = false;//不显示遮罩
    if (showbg) {
        showzz = true
    }

    var deferred = $.Deferred();
    $.ajax({
        type: "POST",
        async: true,
        timeout: 8000,
        url: url,
        data: data,
        dataType: "json",
        beforeSend: function () {
            num += 1
            $.showLoading("請稍後...", showzz)
        },
        success: function (data, status) {

            deferred.resolve(data);
        },
        complete: function () {
            num -= 1
            $.hideLoading(showzz)
        },
        error: function (err, status) { returndata = { "data": "", "status": 404 } }
    });
    // return returndata
    return deferred.promise();
};


//显示加载提示
jQuery.showLoading = function (text, showzz) {

    if (showzz) {

        $.showzhezhao()
    }

    if (text && text.length > 0) {

        var lodingbox = $("<div class='lodingbox'>" + text + "</div>");
    } else {

        var lodingbox = $("<div class='lodingbox'>加载中...</div>");
    }
    if ($(".lodingbox").length > 0) {

    } else {
        $("body").append(lodingbox)
        lodingbox.css({ "position": "fixed", "background": "#ff8100", "color": "#FFFFFF", "border-radius": "8px", "top": "50%", "left": "50%", "margin-left": "-50px", "margin-top": "-50px", "height": "100px", "line-height": "100px", "text-align": "center", "width": "100px", "z-index": "99990" })
    }


};
//移除加载提示
jQuery.hideLoading = function () {
    if (num) {
        //还有加载项
    } else {
        $(".lodingbox").remove();
        $.hidezhezhao();
    }


}
//显示背景遮罩
jQuery.showzhezhao = function () {
    if ($(".zhezhaobox").length > 0) {

    } else {
        var zhezhaobox = $("<div class='zhezhaobox'></div>")
        $("body").append(zhezhaobox)
        zhezhaobox.css({ "position": "fixed", "top": "0px", "left": "0px", "height": "100%", "width": "100%", "z-index": "99980", "background": "rgba(0,0,0,0.1)" })

    }
};
//移除背景遮罩
jQuery.hidezhezhao = function () {
    $(".zhezhaobox").remove()
};

/*-----------------------------------模态框--------------------------------------------*/
//模态 bgcolse为true 点击背景关闭模态框,建议使用modalDiyTs
jQuery.modalTs = function (title, content, colsebg) {

    var box = $("<div class='modalTsbg'><div class='modalTs'><div class='title'>" + title + "</div><div class='content'>" + content + "</div><div class='close'>X</div></div></div>");

    $("body").append(box)
    $(".modalTsbg").css({ "position": "fixed", "top": "0px", "left": "0px", "height": "100%", "width": "100%", "z-index": "99980", "background": "rgba(0,0,0,0.1)" })
    //var mt="-"+$(".modalTs").height()/2+"px"
    //$(".modalTs").css({"margin-top":"100px"})
    //設置模態框樣式
    $(".modalTsbg .modalTs").css({ "position": "fixed", "top": "0px", "left": "50%", "margin-top": "150px", "width": "300px", "position": "fixed", "margin-left": "-150px", "z-index": 99982, "background": "#fff", "border-radius": "5px", "padding": "0px 15px", "box-shadow": "2px 2px 3px #888" })
    $(".modalTsbg .modalTs .title").css({ "height": " 50px", "line-height": "50px", "overflow": "hidden", "text-overflow": "ellipsis", "white-space": "nowrap", "border-bottom": "1px solid #ddd", "font-size": "18px" })
    $(".modalTsbg .modalTs .content").css({ "padding": "15px 0px", "min-height": "160px" })
    $(".modalTsbg .modalTs .close").css({ "position": "absolute", "right": "15px", "top": "8px", "height": "30px", "width": "30px", "line-height": "30px", "border": "1px solid #555", "text-align": "center", "color": "#888", "cursor": "pointer" })
    $(".modalTsbg .modalTs .rest-butt").css({ "width": "60%", "height": " 40px", "line-height": "40px", "text-align": "center", "margin-left": "auto", "margin-right": "auto", "border-radius": "5px", "margin-top": "20px", "background": "#FCDD00" })


    if (colsebg) {
        $(".modalTsbg").addClass("close-true")
    } else {
        $(".modalTsbg").removeClass("close-true")
    }

    $("body").on("click", ".close-true", function () { $(".modalTsbg").remove() })
    //关闭按钮
    $("body").on("click", ".modalTs .close", function () { $(".modalTsbg").remove() })
}

// 帶按鈕的 模态 bgcolse为true 点击背景关闭模态框,建议使用modalDiyTs
/*

			                $.modalConfirm("提示", "發帖成功", "確定", function () {
       
			                },"取消", function () {  
			                }
                            )
*/
jQuery.modalConfirm = function (title, content, Submittext, SubmitFun, canceltext, cancelFun, popclass = "") {
    let Submitbutt = "";
    let canceltbutt = "";
    if (SubmitFun === undefined) {

        Submitbutt = ""
    } else {
        Submitbutt = '<span class="submitbutt butt">' + Submittext + '</span>';
        $("body").on("click", ".modalTs .submitbutt", function () { SubmitFun() })
    }
    if (cancelFun === undefined) {
        canceltbutt = "";
    } else {
        canceltbutt = '<span class="canceltbutt butt">' + canceltext + '</span>';
        $("body").on("click", ".modalTs .canceltbutt", function () { cancelFun() })
    }

    var box = $("<div class='modalTsbg " + popclass + " '><div class='modalTs'><div class='title'>" + title + "</div><div class='content'>" + content + "</div><div class='buttons'>" + Submitbutt + canceltbutt + "</div></div></div>");

    $("body").append(box)
    $(".modalTsbg").css({ "position": "fixed", "top": "0px", "left": "0px", "height": "100%", "width": "100%", "z-index": "99980", "background": "rgba(0,0,0,0.1)" })
    //var mt="-"+$(".modalTs").height()/2+"px"
    //$(".modalTs").css({"margin-top":"100px"})
    //設置模態框樣式
    $(".modalTsbg .modalTs").css({ "position": "fixed", "top": "0px", "left": "50%", "margin-top": "150px", "width": "300px", "position": "fixed", "margin-left": "-150px", "z-index": 99982, "background": "#fff", "border-radius": "5px", "padding": "0px 15px", "box-shadow": "2px 2px 3px #888" })
    $(".modalTsbg .modalTs .title").css({ "height": " 50px", "line-height": "50px", "overflow": "hidden", "text-overflow": "ellipsis", "white-space": "nowrap", "border-bottom": "1px solid #ddd", "font-size": "18px" })
    $(".modalTsbg .modalTs .content").css({ "padding": "15px 0px", "min-height": "160px" })
    $(".modalTsbg .modalTs .close").css({ "position": "absolute", "right": "15px", "top": "8px", "height": "30px", "width": "30px", "line-height": "30px", "border": "1px solid #555", "text-align": "center", "color": "#888", "cursor": "pointer" })
    $(".modalTsbg .modalTs .rest-butt").css({ "width": "60%", "height": " 40px", "line-height": "40px", "text-align": "center", "margin-left": "auto", "margin-right": "auto", "border-radius": "5px", "margin-top": "20px", "background": "#FCDD00" })
    $(".modalTsbg .modalTs .buttons").css({ "text-align": "center", "margin-bottom": "10px" })
    $(".modalTsbg .modalTs .butt").css({ "width": "80px", "height": " 30px", "line-height": "30px", "margin-right": "5px", "text-align": "center", "display": "inline-block", "cursor": "pointer", "border-radius": "5px", "margin-top": "20px", "background": "#FCDD00" })


    //$("body").on("click", ".close-true", function () { $(".modalTsbg").remove() })
    //关闭按钮
    //$("body").on("click", ".modalTs .close", function () { $(".modalTsbg").remove() })
}

//自定义宽高的模态 bgcolse为true 点击背景关闭模态框
/**
 * .modaldiypop {position: fixed; top: 0; left: 0; height: 100%; width: 100%; z-index: 99980; background-color: rgba(0,0,0,0.2);}
 * .modaldiypop .modalTs{ position: fixed;top: 50%;left: 50%; padding: 10px; z-index: 99982}
 * .modaldiypop .modalTs .title{ height: 50px; line-height: 50px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; border-bottom: 1px solid #ddd; font-size: 18px}
 * .modaldiypop .modalTs .content{ padding: 15px 0px; min-height: 160px }
 * .modaldiypop .modalTs .close{  position: absolute; right: 10px; top: 10px; height: 30px; width: 30px; line-height: 28px; border: 1px solid #555; text-align: center; color: #888; cursor: pointer}
 * .modaldiypop .modalTs .butts{ text-align: center;}
 * .modaldiypop .modalTs .butts .active{ background-color:#efefef;}
 * .modaldiypop .modalTs .butt{ display: inline-block; padding:3px 12px;}

 */
jQuery.modalDiyTs = function (options) {


    let stop = $(document.body).scrollTop();
    let sleft = $(document.body).scrollLeft();
    let bodywidth = $("body").width();
    let bodystyle = $("body").attr("style") ? $("body").attr("style") : "";
    let scrollWidth = window.innerWidth - document.body.clientWidth;//滚动条宽度
    let elrandclass = "ui-" + new Date().getTime().toString(36) + Math.random().toString(36).slice(2);//随机class
    let popboxel = "." + elrandclass;//弹出popbox
    console.log("gd", scrollWidth, bodywidth,)

    var defaultOpts = {
        "title": "",
        "content": "",
        "width": "300px", //默认宽度
        "height": "300px", //默认高度
        "top": "100px",
        "colsebg": false,//点击遮罩背景关闭模态框
        "closebutt": true,//显示关闭按钮
        "elClass": "modalDiyTsbg",//模态框自定义class
        "backgroundcolor": "#fff",//彈出框背景顔色
        "boxshadow": "2px 2px 3px #888",//模態陰影
        "submittext": "",
        "canceltext": "",
        submitFun: function () { },/**确定回调 */
        cancelFun: function () { },/**取消 回调 */



    };
    var opt = $.extend({}, defaultOpts, options);

    var modbox = $("<div class='" + opt.elClass + " " + elrandclass + " modaldiypop'></div>");
    let title = opt.title != "" ? "<div class='title'>" + opt.title + "</div>" : "";
    let closebutt = opt.closebutt ? "<div class='close'>X</div>" : "";
    let submitbutt = opt.submittext ? "<div class='butt submitbutt'>" + opt.submittext + "</div>" : "";
    let cancelbutt = opt.canceltext ? "<div class='butt cancelbutt'>" + opt.canceltext + "</div>" : "";
    var mainbox = $("<div class='modalTs'>" + title + "<div class='content'>" + opt.content + "<div class='butts'>" + submitbutt + cancelbutt + "</div>" + "</div>" + closebutt + "</div>")

    modbox.append(mainbox)
    $("body").append(modbox)


    let marginleft = "";
    let margintop = "";

    if (opt.width.indexOf("%") > 0) {
        //单位是%
        marginleft = '-' + (parseInt(opt.width) / 2) + '%';
    } else {
        // px或者其他非10%
        marginleft = '-' + (parseInt(opt.width) / 2) + 'px';
    }

    if (opt.height.indexOf("%") > 0) {
        //单位是%
        margintop = '-' + (parseInt(opt.height) / 2) + '%';
    } else {
        // px或者其他非%
        margintop = '-' + (parseInt(opt.height) / 2) + 'px';
    }


    //設置模態框樣式
    modbox.find(".modalTs").css({ "width": opt.width, "height": opt.height, "margin-left": marginleft, "margin-top": margintop, "background-color": opt.backgroundcolor, "box-shadow": opt.boxshadow })

    $("body").css({ position: "fixed", top: -stop, width: bodywidth + scrollWidth });//body设置固定模式防止滚动
    if (scrollWidth > 0) {
        //有滚动条，加上滚动条宽度
        $("body").css({ "border-right": scrollWidth + "px solid transparent" });
    }

    if (opt.colsebg) {
        modbox.addClass("close-true")
    } else {
        modbox.removeClass("close-true")
    }


    //点击确定
    $("body").on("click", popboxel + " .submitbutt", function () {
        opt.submitFun.call(this, $(popboxel));
        close();
    })

    //取消
    $("body").on("click", popboxel + " .cancelbutt", function () {
        opt.cancelFun.call(this, $(popboxel));
        close();
    })

    //遮罩关闭模态
    $("body").on("click", " .close-true", function (e) {

        close();
        if (opt.canceltext != "") {
            opt.cancelFun.call(this, $(popboxel));
        }


    })

    $("body").on("click", popboxel + " .modalTs", function (e) {
        //防止点击弹出框关闭弹出窗
        e.stopPropagation();
    })
    //关闭按钮
    $("body").on("click", popboxel + ' .modalTs .close', function () {
        close();
        if (opt.canceltext != "") {
            opt.cancelFun.call(this, $(popboxel));
        }
    })

    function close() {
        $(popboxel).remove();
        $("body").attr("style", bodystyle);
        $(window).scrollTop(stop);
        $(window).scrollLeft(sleft);
    }

}

jQuery.queryStringToObj = function (query) {
    var search = window.location.search + '';

    if (search.charAt(0) != '?') {
        return [];
    }
    else {
        search = search.replace('?', '').split('&');
      
        if (Array.isArray(search) && search.length > 0) {
            let obj={};
            for (var i = 0; i < search.length; i++) {
                if(search[i].indexOf("=")>0){
                    let item=search[i].split('=');
                    obj[item[0]]=item[1]
                }
            }
            
            return obj;

        } else {
            return [];
        }



    }
}; 