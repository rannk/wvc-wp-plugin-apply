jQuery(document).ready(function ($) {
    // 申请按钮
    $(".wvc_apply_btn").click(function (e) {
        e.preventDefault();
        $("#wvcModal .success-step").hide();
        $("#wvcModal").modal("show");
    });

    $("#wvc_pd_weight_select").change(function (v) {
        if ($("#wvc_pd_weight_select").val().toString().toLowerCase() == "other") {
            $("#wvc_pd_weight_other").show();
        } else {
            $("#wvc_pd_weight_other").hide();
        }
    })

    $("#wvc_pd_brand_select").change(function (v) {
        if ($("#wvc_pd_brand_select").val().toString().toLowerCase() == "other") {
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

    $(".wvc_pd_select_btn").click(function () {
        if(!$("#wvcPdSelectionModal").verify("check")){
            return;
        }

        let _t = this;

        let _text = $(_t).html();

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
                    if($(_t).attr("data-href") != undefined){
                        location.href = $(_t).attr("data-href");
                    }else if(location.href.indexOf("apply-form")>0){
                        location.reload();
                    }else{
                        $("#wvcModal .success-step").show();
                    }

                }
                $(_t).html(_text);
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
            "billing_first_name":$("#billing_first_name").val(),
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
                    location.href='/thanks_for_your_messages/';
                }else{
                    console.log(data.status);
                }
                $(_t).loading("hide");
            }
        })
    });

    if($(window).width()<431){
        if($(".wvcProductTable").length > 0){
            $(".wvcProductTable").each(function (ii, _t){
                let _attrNames = [];
                let cardHtml = "<ul class='wvcProductTableOnMobile'>";
                $(_t).find("tr:nth-child(1) td").each(function (index,item){
                    _attrNames[index] = $(item).html().toString().trim();
                })

                if($(_t).find("tr:nth-child(2) td:nth-child(1)").html().toString().trim() == ""){
                    $(".wvcProductTable tr:nth-child(2) td").each(function (index, item){
                        if($(item).html().toString().trim() != ""){
                            _attrNames[index] = $(item).html().toString().trim();
                        }
                    });
                }

                $(_t).find("tr").each(function (i, item){
                    if(i > 1){
                        cardHtml += '<li>';
                        let trIdx = i+1;
                        for(let j=0;j<_attrNames.length;j++){
                            let tdIdx = j+1;
                            if(j == 0){
                                cardHtml += '<h5 class="wvcCardTitle">' + $(_t).find("tr:nth-child("+trIdx+") td:nth-child("+tdIdx+")").html().toString().trim() + "<span class='rk-svg-icon svg-down'></span></h5><div class='wvcCardContent'>";
                            }else{
                                let tdV = '';
                                if($(_t).find("tr:nth-child("+trIdx+") td:nth-child("+tdIdx+")").length > 0){
                                    tdV = $(_t).find("tr:nth-child("+trIdx+") td:nth-child("+tdIdx+")").html().toString().trim()
                                }else{
                                    while(trIdx>2){
                                        --trIdx;
                                        if($(_t).find("tr:nth-child("+trIdx+") td:nth-child("+tdIdx+")").length > 0){
                                            tdV = $(_t).find("tr:nth-child("+trIdx+") td:nth-child("+tdIdx+")").html().toString().trim()
                                            break;
                                        }
                                    }
                                }
                                cardHtml += '<div><label>'+_attrNames[j]+'</label><label>'+tdV+'</label></div>'
                            }
                        }
                        cardHtml += "</div></li>";
                    }
                });

                cardHtml += "</ul>";

                $(_t).parent().append(cardHtml);
                $(_t).remove();
            });

            $(".wvcCardTitle").click(function (){
                if($(this).parent().find(".wvcCardContent").hasClass("on")){
                    $(this).parent().find(".wvcCardContent").removeClass("on");
                    $(this).find(".rk-svg-icon").removeClass("svg-up");
                    $(this).find(".rk-svg-icon").addClass("svg-down");
                }else{
                    $(this).parent().find(".wvcCardContent").addClass("on");
                    $(this).find(".rk-svg-icon").removeClass("svg-down");
                    $(this).find(".rk-svg-icon").addClass("svg-up");
                }
            });

            $(".wvcProductTableOnMobile li:nth-child(1) h5").click();
        }
    }
});