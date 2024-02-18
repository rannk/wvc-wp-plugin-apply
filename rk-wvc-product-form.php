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

function _RK_WVC()
{
    return RK_WVC::instance();
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
// 注册api
add_action( 'rest_api_init', 'rk_wvc_apply_api' );

function rk_wvc_apply_api() {
    register_rest_route( 'wvc', 'apply', [
        'methods'  => 'GET',
        'callback' => 'rk_wvc_resp_apply'
    ] );
}

function rk_wvc_resp_apply($request) {
    $params = $request->get_params();
    return json_encode($params);
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
    $rk_wvc_spec = get_post_meta( $post->ID, 'rk_wvc_spec', true );
    $rk_wvc_cover_value = get_post_meta( $post->ID, 'rk_wvc_cover_value', true );
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
<div class="rwmb-field rwmb-text-wrapper">
    <div class="rwmb-label" id="boldthemes_theme_menu_name-label">
    <label for="boldthemes_theme_menu_name">Product Spec</label>
    </div>
    <div class="rwmb-input">
    <input type="text" id="rk_wvc_spec" class="rwmb-text" name="rk_wvc_spec" value="{wvc_spec}">
    </div>
 </div>
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
    $_html = str_replace('{wvc_spec}', $rk_wvc_spec, $_html);
    $_html = str_replace('{wvc_cover_value}', $rk_wvc_cover_value, $_html);
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

        if($_POST['rk_wvc_is_product']) {
            update_post_meta($post_id, 'rk_wvc_is_product', $_POST['rk_wvc_is_product']);
            update_post_meta($post_id, 'rk_wvc_spec', $_POST['rk_wvc_spec']);
            update_post_meta($post_id, 'rk_wvc_cover_value', $_POST['rk_wvc_cover_value']);
        }
    }
}

// 产品页中设置产品属性
add_shortcode( 'rk-wvc-apply', 'rk_wvc_apply_content' );
add_shortcode('rk-wvc-apply-form', "rk_wvc_apply_form");

function rk_wvc_apply_content()
{

    $post = get_post();
    if(!empty($post)){
        $rk_wvc_spec = get_post_meta( $post->ID, 'rk_wvc_spec', true );
        $rk_wvc_cover_value = get_post_meta( $post->ID, 'rk_wvc_cover_value', true );
        echo '<input type="hidden" id="rk_wvc_spec_value" value="'.$rk_wvc_spec.'">';
        echo '<input type="hidden" id="rk_wvc_cover_value" value="'.$rk_wvc_cover_value.'">';
        echo '<input type="hidden" id="rk_wvc_title_value" value="'.$post->post_title.'">';
    }
}

function rk_wvc_apply_form()
{
    ob_start();
    load_template(__DIR__ . "/templates/applyForm.php");
    return ob_get_clean();
}
