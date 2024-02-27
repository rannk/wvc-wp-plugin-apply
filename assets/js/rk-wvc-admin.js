jQuery(document).ready(function($) {
    // 打开媒体库
    $('#wvc-cover-upload-button').on('click', function(e) {
        e.preventDefault();
        // 打开媒体库
        var mediaUploader = wp.media({
            frame: 'select',
            title: '选择媒体文件',
            multiple: false, // 是否允许多选
            library: { type: 'image' } // 允许的媒体类型
        });

        // 当媒体库中选择了媒体文件后执行的回调函数
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            if(attachment.url != undefined){
                $("#wvc_cover_preview").attr("src", attachment.url);
                $("#rk_wvc_cover_value").val(attachment.url);
            }
        });

        // 打开媒体库对话框
        mediaUploader.open();
    });

    $('#wvc-product-meta-save').on('click', function (e){
        e.preventDefault();
        let data = {
            "rk_wvc_brand": $("#rk_wvc_brand").val(),
            "rk_wvc_weight": $("#rk_wvc_weight").val(),
        }
        $.ajax({
            url:"/wp-json/wvc/pdMetaSave",
            type:"post",
            data:data,
            success:function(data,dataTextStatus,jqxhr){

            }
        })
    });

    $(".applyDetailView").click(function (){
        let id = $(this).attr("appid");
        let uInfo = $.parseJSON($("#uInfo-"+id).val());

        let pdInfo = $.parseJSON($("#pdInfo-"+id).val());
        $("#wvcApplyDetailModal .fillInfo").each(function (){
           let prop = $(this).attr("prop");
           $(this).html(uInfo[prop]);
        });

        let _h = '';
        for(let i=0;i<pdInfo.length;i++){
            _h += '<li><div style="font-weight: bold">'+pdInfo[i].wvc_pd_name+'</div>';
            _h += '<div class="sj1"> Spec: ' + pdInfo[i].wvc_pd_spec + '</div>';
            _h += '<div class="sj1"> Brand: ' + pdInfo[i].wvc_pd_brand_select + ' '+pdInfo[i].wvc_pd_brand_other+'</div>';
            _h += '<div class="sj1"> Weight: ' + pdInfo[i].wvc_pd_weight_select + ' '+pdInfo[i].wvc_pd_weight_other+'</div></li>';
        }

        $("#wvcApplyDetailModal #sampleLists").html(_h);

        $("#wvcApplyDetailModal #countryInfo").html(countriesArr[uInfo.billing_country]);

        $("#wvcApplyDetailModal").modal("show");
    });

    $("#wvc-apply-search-submit").click(function (){
        location.href='/wp-admin/admin.php?page=rk_wvc_product_apply_lists&s=' + $("#post-search-input").val();
    });
});