(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        module.exports = factory(require('jquery'));
    } else {
        root.Modal = factory(root.jQuery);
    }
}(this, function ($) {
    "use strict";

    $.fn.modal = function (action) {
        var target = this;
        var panel = $(window);
        if (action == "show" || action == undefined) {
            if (typeof $.modal.beforeOpen == "function") {
                $.modal.beforeOpen(target);
            }

            let _tWidth = $(target).width();
            if($(panel).width() < 768){
                _tWidth = $(panel).width() * 0.9;
            }
            let _m_left_offset = panel.width() / 2 - _tWidth / 2;
            let _m_top_offset = panel.height() / 2 - $(target).height() / 2;
            $(target).css("top", _m_top_offset + "px");
            if (_m_left_offset > 0) {
                $(target).css("left", _m_left_offset + "px");
            }

            $(target).css("height", $(target).height() + "px");
            $(target).show();
            $("body").append("<div id='_simple_modal_cover_div' style='min-width: 100%;min-height: 100%;z-index: 99999;opacity:0.5;background-color: #000;position: fixed;top: 0;left: 0;'></div>");
            $(target).css("z-index", "999999");
            $(target).css("position", "fixed")
            $(target).attr("_s_modal", "show");
            if ($.modal.clickClose == true) {
                $("#_simple_modal_cover_div").click(function () {
                    hideModal();
                })
            }

            if (typeof $.modal.open == "function") {
                $.modal.open(target);
            }

            let wY = window.scrollY;
            window.onscroll = function (){
                window.scrollTo(0, wY);
            }
        }

        if (action == "hide") {
            hideModal();
        }
    }

    $.modal = {
        panel: window,
        clickClose: true,
        beforeOpen: "",
        open: "",
        beforeClose: "",
        afterClose: "",
        settings: function (settings) {
            $.extend($.modal, settings)
        },
        hide: function () {
            hideModal();
        }
    }

    function hideModal() {
        var modal = $("[_s_modal='show']")[0];

        window.onscroll = function (){
        }

        if (typeof $.modal.beforeClose == "function") {
            $.modal.beforeClose(modal);
        }

        $("[_s_modal='show']").offset({"top": 0, "left": 0})
        $("[_s_modal='show']").hide();
        $("#_simple_modal_cover_div").remove();
        $("[_s_modal='show']").removeAttr("_s_modal");

        if (typeof $.modal.afterClose == "function") {
            $.modal.afterClose(modal);
        }
    }

    window.onload = function () {
        $("a[ref='modal']").click(function () {
            var id = $(this).attr("href");
            var target = $(id);
            if (target.length == 1) {
                target.modal("show");
            }
        })

        $(".modalClose").click(function (){
            var id = $(this).attr("for");
            if(id != undefined){
                $("#" + id).modal("hide");
            }
        })
    }

    $.fn.loading = function (action){
        if(action == "show"){
            let vH = $(window).height() / 2 - 15;
            $("body").append("<div id='_simple_loading_cover_div' style='min-width: 100%;min-height: 100%;z-index: 999991;opacity:0.5;background-color: #ccc;position: fixed;top: 0;left: 0;'><div style=\"position: fixed;top: "+vH+"px;width: 100%; text-align: center;z-index: 999992\"><img src=\"/wp-content/plugins/rk-wvc-product-form/assets/images/loading.gif\" width=\"100px\"></div></div>");
        }

        if(action == "hide"){
            $("#_simple_loading_cover_div").remove();
        }
    }

    $.fn.verify = function (action){
        if(action == "check"){
            let pass = true;
            $(this).find(".warning").remove();

            $(this).find(".required").each(function (){
                let n = $(this).parent().attr("for");
                let reqV = true;
                if($(this).attr("req-up-for") != undefined){
                    let upValue = $(this).attr("req-up-value")==undefined?"":$(this).attr("req-up-value");
                    if($("#" + $(this).attr("req-up-for")).val().toString().toLowerCase() != upValue.toString().toLowerCase()){
                        reqV = false;
                    }
                }
                if(reqV == true && $("#"+n).val() == ""){
                    pass = false;
                    $(this).parent().append('<span class="warning"><span class="btIco"><span data-ico-fa="" class="btIcoHolder"></span></span>' + $(this).attr("req-tip") + '</span>');
                }
            });

            return pass;
        }
    }
}));