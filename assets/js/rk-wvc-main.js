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


            let _m_left_offset = panel.width() / 2 - $(target).width() / 2;
            $(target).css("top", (panel.height() / 2 - $(target).height() / 2) + "px");
            if (_m_left_offset > 0) {
                $(target).css("left", _m_left_offset + "px");
            }
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
                    if($("#" + $(this).attr("req-up-for")).val() != upValue){
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

jQuery(document).ready(function ($) {
    // 申请按钮
    $(".wvc_apply_btn").click(function () {
        $("#wvcModal .success-step").hide();
        $("#wvcModal").modal("show");
    });

    $("#wvc_pd_weight_select").change(function (v) {
        if ($("#wvc_pd_weight_select").val() == "other") {
            $("#wvc_pd_weight_other").show();
        } else {
            $("#wvc_pd_weight_other").hide();
        }
    })

    $("#wvc_pd_brand_select").change(function (v) {
        if ($("#wvc_pd_brand_select").val() == "other") {
            $("#wvc_pd_brand_other").show();
        } else {
            $("#wvc_pd_brand_other").hide();
        }
    })

    $("#wvc_pd_modal_close_btn").click(function () {
        $("#wvcModal").modal("hide");
    });

    $(".wvcSampleList .remove").click(function () {
        let data = {
            "index": $(this).attr("index")
        }

        if (confirm("are you sure remove the sample from list?") == true) {
            $(this).parent().append('<div class="progress-4"></div>');
            $(this).remove();
            $.ajax({
                url: "/wp-json/wvc/sampleRemove",
                type: "post",
                data: data,
                dataType: "json",
                success: function (data) {
                    data = $.parseJSON(data);
                    if (data.status == 'success') {
                        location.reload();
                    }
                }
            })
        }
    });

    $("#wvc_pd_select_btn").click(function () {
        if($(this).html() != "Select"){
            return;
        }

        if(!$("#wvcPdSelectionModal").verify("check")){
            return;
        }

        let _t = this;

        let wvc_pd_name = "";
        if ($("#wvc_pd_name").val() != "") {
            $("#wvc_pd_name option").each(function (index, item) {
                if ($(item).attr("value") == $("#wvc_pd_name").val()) {
                    wvc_pd_name = $(item).html();
                }
            });
        }

        let data = {
            "wvc_pd_id": $("#wvc_pd_name").val(),
            "wvc_pd_name": wvc_pd_name,
            "wvc_pd_brand_select": $("#wvc_pd_brand_select").val(),
            "wvc_pd_brand_other": $("#wvc_pd_brand_other").val(),
            "wvc_pd_spec": $("#wvc_pd_spec").val(),
            "wvc_pd_weight_select": $("#wvc_pd_weight_select").val(),
            "wvc_pd_weight_other": $("#wvc_pd_weight_other").val(),
        }

        $(this).html("Submitting...");
        $.ajax({
            url: "/wp-json/wvc/sampleSelect",
            type: "post",
            data: data,
            dataType: "json",
            success: function (data, dataTextStatus, jqxhr) {
                data = $.parseJSON(data);
                if (data.status == 'success') {
                    if(location.href.indexOf("apply-form")>0){
                        location.reload();
                    }else{
                        $("#wvcModal .success-step").show();
                    }

                }
                $(_t).html("Select");
            }
        })
    });


    $(".wvcInputForm").blur(function (){
        let data={
            "key":$(this).attr("id"),
            "value":$(this).val()
        }
        $.ajax({
            url: "/wp-json/wvc/formInfo",
            type: "post",
            data: data,
            dataType: "json",
            success: function (data) {}
        })
    });

    $(".wvcSubmitFormBtn").click(function (){
        if(!$("#customer_details").verify("check")){
            return;
        }

        var _t = this;
        $(_t).loading("show");
        var data = {
            "bill_first_name":$("#bill_first_name").val(),
            "billing_last_name":$("#billing_last_name").val(),
            "billing_company":$("#billing_company").val(),
            "billing_country":$("#billing_country").val(),
            "billing_address_1":$("#billing_address_1").val(),
            "billing_address_2":$("#billing_address_2").val(),
            "billing_city":$("#billing_city").val(),
            "billing_postcode":$("#billing_postcode").val(),
            "billing_phone":$("#billing_phone").val(),
            "billing_email":$("#billing_email").val()
        }

        $.ajax({
            url: "/wp-json/wvc/formSubmit",
            type: "post",
            data: data,
            dataType: "json",
            success: function (data) {
                data = $.parseJSON(data);
                if (data.status == 'success') {
                    alert("submit success");
                    location.reload();
                }else{
                    console.log(data.status);
                }
                $(_t).loading("hide");
            }
        })
    });
});