<?php
/**
 * Plugin Name: WVC Product form
 * Description: apply form for WVC products
 * Version: 1.0
 * Author: Jay Deng
 * Requires at least: 6.3
 * Requires PHP: 7.4
 */
require_once __DIR__ . "/includes/class-rk-wvc.php";
require_once __DIR__ . "/includes/rk-wvc-widget-functions.php";
require_once __DIR__ . "/api.php";

function _RK_WVC()
{
    return RK_WVC::instance();
}

register_activation_hook( __FILE__, 'wvc_table_install');
function wvc_table_install() {
    global $wpdb;
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."wvc_apply` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`uInfo` JSON NULL DEFAULT NULL,
	`pdInfo` JSON NULL DEFAULT NULL,
	`addtime` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`) USING BTREE
)
COMMENT='产品申请表'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB";
    $wpdb->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."wvc_sample_apply` (
	`uid` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
	`content` JSON NULL DEFAULT NULL,
	`addtime` INT(11) NOT NULL DEFAULT '0'
)
COMMENT='样品申请临时记录'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB";
    $wpdb->query($sql);
}

// 添加管理菜单
add_action("admin_menu", function (){
    add_menu_page("WVC product management", "WVC product",'manage_options','rk_wvc_product_management','rk_wvc_product_management','dashicons-archive');
    add_submenu_page("rk_wvc_product_management", "Apply Lists", "Aapply Lists", "manage_options", "rk_wvc_product_apply_lists", "rk_wvc_product_apply_lists");
});


add_action("wp_enqueue_scripts", function (){
    wp_enqueue_script("rk_mvc_main_js", _RK_WVC()->plugin_url() . "/assets/js/rk-wvc-main.js", array('jquery'));
    wp_enqueue_style("rk_mvc_main_style", _RK_WVC()->plugin_url() . "/assets/css/rk-wvc-style.css");
});

add_action("wp_enqueue_style", function (){

});

add_action("admin_enqueue_scripts", function (){
    wp_enqueue_script("rk_mvc_main", _RK_WVC()->plugin_url() . "/assets/js/rk-wvc-admin.js", array('jquery'));
});

function rk_wvc_product_management()
{
    $rk_wvc_brand = get_site_option("rk_wvc_brand");
    $rk_wvc_weight = get_site_option("rk_wvc_weight");

    $_html = <<<html
<div class="rwmb-field rwmb-text-wrapper">
    <div class="rwmb-label" id="boldthemes_theme_menu_name-label">
    <label for="boldthemes_theme_menu_name">Product Brand</label>
    </div>
    <div class="rwmb-input">
    <input type="text" id="rk_wvc_brand"  style="width: 90%" name="rk_wvc_brand" value="{wvc_brand}">
    </div>
 </div>
 <div style="margin-top: 20px;">
    <div class="rwmb-label" id="boldthemes_theme_menu_name-label">
    <label for="boldthemes_theme_menu_name">Product Weight</label>
    </div>
    <div class="rwmb-input">
    <input type="text" id="rk_wvc_weight" style="width: 90%"  name="rk_wvc_weight" value="{wvc_weight}">
    </div>
 </div>
<div style="margin-top: 20px">
    <input type="button" id="wvc-product-meta-save" value="Save" class="button button-primary button-small">
</div>
html;
    $_html = str_replace('{wvc_brand}', $rk_wvc_brand, $_html);
    $_html = str_replace('{wvc_weight}', $rk_wvc_weight, $_html);
    echo  $_html;
}

// 增加管理页面属性
add_action("add_meta_boxes", function (){
    $p_obj = get_post()->to_array();
    if($p_obj['post_type'] != "page")
        return;

    add_meta_box(
        'wvc_product_spec',
        "Product Extension",
        'rk_wvc_product_extension_box',
        null, 'normal'
    );
});

function rk_wvc_product_extension_box($post)
{
    $rk_wvc_is_product = get_post_meta( $post->ID, 'rk_wvc_is_product', true );
    $rk_wvc_brand = get_post_meta( $post->ID, 'rk_wvc_brand', true );
    $rk_wvc_cover_value = get_post_meta( $post->ID, 'rk_wvc_cover_value', true );
    $rk_wvc_weight = get_post_meta($post->ID, 'rk_wvc_weight', true);

    $checked = "";
    if(!empty($rk_wvc_is_product)) {
        $checked = "checked";
    }

    $_html = <<<html
<div class="rwmb-field rwmb-text-wrapper">
    <div class="rwmb-label" id="boldthemes_theme_menu_name-label">
    <label for="boldthemes_theme_menu_name">This is a WVC product</label>
    </div>
    <div class="rwmb-input">
    <input type="checkbox" id="rk_wvc_is_product" name="rk_wvc_is_product" value="1" {checked}>
    </div>
 </div>
 <!--
<div class="rwmb-field rwmb-text-wrapper">
    <div class="rwmb-label" id="boldthemes_theme_menu_name-label">
    <label for="boldthemes_theme_menu_name">Product Brand</label>
    </div>
    <div class="rwmb-input">
    <input type="text" id="rk_wvc_brand" class="rwmb-text" name="rk_wvc_brand" value="{wvc_brand}">
    </div>
 </div>
 <div class="rwmb-field rwmb-text-wrapper">
    <div class="rwmb-label" id="boldthemes_theme_menu_name-label">
    <label for="boldthemes_theme_menu_name">Product Weight</label>
    </div>
    <div class="rwmb-input">
    <input type="text" id="rk_wvc_weight" class="rwmb-text" name="rk_wvc_weight" value="{wvc_weight}">
    </div>
 </div>
 -->
<div class="rwmb-field rwmb-text-wrapper">
    <div class="rwmb-label" id="boldthemes_theme_menu_name-label">
    <label for="boldthemes_theme_menu_name">Product Cover Image</label>
    </div>
    <div class="rwmb-input">
    <input type="button" id="wvc-cover-upload-button" value="Select" class="button button-primary button-small">
    <input type="button" id="wvc-cover-remove-button" value="Remove Cover" class="button button-warning button-small">
    <input name="rk_wvc_cover_value" id="rk_wvc_cover_value" type="hidden" value="{wvc_cover_value}">
    <p><img src="{wvc_cover_value}" width="100px" id="wvc_cover_preview"></p>
    </div>
 </div>
html;

    $_html = str_replace('{checked}', $checked, $_html);
    $_html = str_replace('{wvc_brand}', $rk_wvc_brand, $_html);
    $_html = str_replace('{wvc_cover_value}', $rk_wvc_cover_value, $_html);
    $_html = str_replace('{wvc_weight}', $rk_wvc_weight, $_html);
    echo $_html;
}

// 页面保存时的操作
add_action("save_post", "rk_wvc_page_save");
function rk_wvc_page_save($post_id)
{
    if(!$_POST['post_type'])
        return;

    if ( 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'publish_posts', $post_id ) ){
            return;
        }

        update_post_meta($post_id, 'rk_wvc_is_product', isset($_POST['rk_wvc_is_product'])?1:0);
        //update_post_meta($post_id, 'rk_wvc_brand', $_POST['rk_wvc_brand']);
        //update_post_meta($post_id, 'rk_wvc_weight', $_POST['rk_wvc_weight']);
        update_post_meta($post_id, 'rk_wvc_cover_value', $_POST['rk_wvc_cover_value']);
    }
}

// 产品页中设置产品属性
add_shortcode( 'rk-wvc-apply', 'rk_wvc_apply_content' );
add_shortcode('rk-wvc-apply-form', "rk_wvc_apply_form");
add_shortcode("rk-wvc-sample-list", "rk_wvc_sample_list");

function rk_wvc_apply_content()
{
    ob_start();
    $post = get_post();
    if(!empty($post)){
        echo '<a href="#" class="btBtn btnOutlineStyle btnNormalColor btnSmall btnNormalWidth btnRightPosition btnNoIcon wvc_apply_btn">Get Sample</a>';
        echo '<a href="/apply-form" class="btBtn btnOutlineStyle btnAlternateColor btnSmall btnNormalWidth btnRightPosition btnNoIcon" style="margin-left: 10px;">Apply Form</a>';
    }
    echo rk_wvc_apply_modal();
    return ob_get_clean();
}

function rk_wvc_apply_form()
{
    ob_start();
    load_template(__DIR__ . "/templates/applyForm.php");
    return ob_get_clean();
}

function rk_wvc_apply_modal()
{
    $_html = <<<_html
<div id="wvcModal" class="modal wvcModal">
  <header>
  <h4>Product Selection</h4>
</header>
<div class="content" id="wvcPdSelectionModal">
<div class="success-step" style="display: none">
Congratulations! you have selected this product in the cart, you can continue to choose other products or click <a href="/apply-form" style="font-size: 18px">here</a> to fill the apply form
</div>
    <div class="row">
    <label for="wvc_pd_name">choice the product
        <abbr class="required" title="required" req-tip="please choice the product">*</abbr>
    </label>
    <select id="wvc_pd_name">
    {pd_options}
    </select>
    </div>
    <div class="row">
    <label for="wvc_pd_brand_other">choice the Brand<span class="required" req-tip="please fill the brand information" req-up-for="wvc_pd_brand_select" req-up-value="other"></span></label>
    <select id="wvc_pd_brand_select" name="wvc_pd_brand_select">
    {pd_brand_options}
    </select>
            <input type="text" placeholder="Fill in the brand you want" id="wvc_pd_brand_other" name="wvc_pd_brand_other" style="display: none;margin-top: 5px">
    </div>
    <div class="row">
    <label>fill the spec</label>
    <input type="text" placeholder="Fill in the specifications you want" id="wvc_pd_spec">
    </div>
    <div class="row">
    <label for="wvc_pd_weight_other">choice the weight<span class="required" req-tip="please fill in the weight" req-up-for="wvc_pd_weight_select" req-up-value="other"></span></label>
    <select id="wvc_pd_weight_select" name="wvc_pd_weight_select">
        {pd_weight_options}
    </select>
        <input type="text" placeholder="Fill in the weight you want" id="wvc_pd_weight_other" name="wvc_pd_weight_other" style="display: none;margin-top: 5px">
    </div>
    
    <div class="footer">
<a class="btBtn btnOutlineStyle btnAccentColor btnSmall btnNoIcon" id="wvc_pd_select_btn">Select</a>
<a class="btBtn btnOutlineStyle btnAlternateColor btnSmall btnNoIcon" id="wvc_pd_select_btn" href="/apply-form">Apply Form</a>
<a class="btBtn btnOutlineStyle btnNormalColor btnSmall btnNoIcon" id="wvc_pd_modal_close_btn">Close</a>
</div>
</div>
</div>
_html;
    $pd_options = _RK_WVC()->getProductOptions(get_post()->ID);
    $_html = str_replace('{pd_options}', $pd_options, $_html);
    $_html = str_replace('{pd_brand_options}', _RK_WVC()->getBrandOptions(), $_html);
    $_html = str_replace('{pd_weight_options}',  _RK_WVC()->getWeightOptions(), $_html);
    return $_html;
}

function rk_wvc_sample_list()
{
    ob_start();
    $uid = $_COOKIE['_wvc_guest_uid'];
    $sampleList = [];
    if(!empty($uid)){
        $sampleList = _RK_WVC()->getSampleList($uid);
    }
    set_query_var("sampleList", $sampleList);
    load_template(__DIR__ . "/templates/sampleList.php");
    echo rk_wvc_apply_modal();
    return ob_get_clean();
}
