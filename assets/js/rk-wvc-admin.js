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
                console.log(data);
            }
        })
    });
});