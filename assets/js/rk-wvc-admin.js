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
            }
        });

        // 打开媒体库对话框
        mediaUploader.open();
    });
});