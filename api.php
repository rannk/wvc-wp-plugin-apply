<?php
// 注册api
add_action( 'rest_api_init', 'rk_wvc_apply_api' );
function rk_wvc_apply_api() {
    register_rest_route( 'wvc', 'apply', [
        'methods'  => 'GET',
        'callback' => 'rk_wvc_resp_apply'
    ] );

    // 产品meta信息保存 后台
    register_rest_route( 'wvc', 'pdMetaSave', [
        'methods'  => 'post',
        'callback' => 'rk_wvc_product_meta_save'
    ] );

    // 临时保存选择的样品
    register_rest_route( 'wvc', 'sampleSelect', [
        'methods'  => 'post',
        'callback' => 'rk_wvc_sample_select'
    ] );

    // 选择样品直接保存，包含姓名和邮箱
    register_rest_route( 'wvc', 'selectFormSave', [
        'methods'  => 'post',
        'callback' => 'rk_wvc_select_form_save'
    ] );

    // 移除临时保存的样品
    register_rest_route( 'wvc', 'sampleRemove', [
        'methods'  => 'post',
        'callback' => 'rk_wvc_sample_remove'
    ] );

    // 表单个人资料存储
    register_rest_route( 'wvc', 'formInfo', [
        'methods'  => 'post',
        'callback' => 'rk_wvc_form_info'
    ] );

    // 提交表单
    register_rest_route( 'wvc', 'formSubmit', [
        'methods'  => 'post',
        'callback' => 'rk_wvc_form_submit'
    ] );
}

function rk_wvc_resp_apply($request) {
    $params = $request->get_params();
    return json_encode($params);
}

function rk_wvc_product_meta_save($request)
{
    $params = $request->get_params();
    update_option("rk_wvc_brand", $params['rk_wvc_brand']);
    update_option("rk_wvc_weight", $params['rk_wvc_weight']);
    return json_encode(["code"=>200, "status"=>"success"]);
}

function rk_wvc_sample_select($request)
{
    $uid = $_COOKIE['_wvc_guest_uid'];
    if(empty($uid)){
        $uid = time() . rand(1,9) . rand(1,9) . rand(1,9) . rand(1,9);
        setcookie("_wvc_guest_uid", $uid, 0, "/");
    }
    $params = $request->get_params();
    _RK_WVC()->sampleApplyTmp($uid, json_encode($params));
    return json_encode(["code"=>200, "status"=>"success"]);
}

function rk_wvc_select_form_save($request)
{
    $params = $request->get_params();
    _RK_WVC()->selectFormSave($params);
    return json_encode(["code"=>200, "status"=>"success"]);
}

function rk_wvc_sample_remove($request)
{
    $uid = $_COOKIE['_wvc_guest_uid'];
    $params = $request->get_params();
    if(!empty($uid)){
        _RK_WVC()->removeSample($uid, $params['index']);
    }

    return json_encode(["code"=>200, "status"=>"success"]);
}

function rk_wvc_form_info($request)
{
    $uInfo = $_COOKIE['_wvc_uinfo'];
    if(!empty($uInfo)){
        $uInfo = json_decode(str_replace('\"', '"', $uInfo), true);
    }

    if(empty($uInfo)){
        $uInfo = [];
    }

    $params = $request->get_params();
    if(!empty($params['key'])){
        $uInfo[$params['key']] = $params['value'];
        setcookie("_wvc_uinfo", json_encode($uInfo), 0, "/");
    }

    return $_COOKIE['_wvc_uinfo'];
}

function rk_wvc_form_submit($request)
{
    $params = $request->get_params();
    $uid = $_COOKIE['_wvc_guest_uid'];
    $code = 200;
    $msg = "success";
    try{
        _RK_WVC()->applyFormSave($uid, json_encode($params));
    }catch (Exception $e){
        $code = 500;
        $msg = $e->getMessage();
    }
    return json_encode(["code"=>$code, "status"=>$msg]);
}