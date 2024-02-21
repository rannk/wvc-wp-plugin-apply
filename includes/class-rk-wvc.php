<?php
class RK_WVC
{
    protected static $_instance = null;


    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function plugin_url() {
        return '/wp-content/plugins/rk-wvc-product-form';
    }

    public function getProducts()
    {
        global $wpdb;
        $sql = "select post_title,ID from ". $wpdb->base_prefix . "posts p inner join ". $wpdb->base_prefix . "postmeta pm on p.ID=pm.post_id where pm.meta_key='rk_wvc_is_product' and pm.meta_value=1";
        return $wpdb->get_results($sql, ARRAY_A);
    }

    public function anayExtension($value)
    {
        $separator = "|";
        if(stripos($value, ",")){
            $separator = ",";
        }

        return explode($separator, $value);
    }

    public function getProductExtension()
    {
        $wvc_brand = $this->anayExtension(get_option("rk_wvc_brand"));
        $wvc_weight = $this->anayExtension(get_option("rk_wvc_weight"));
        return [
            "rk_wvc_brand" => $wvc_brand,
            "rk_wvc_weight" => $wvc_weight
        ];
    }

    public function getProductOptions($id="")
    {
        $pd_options = "<option value=''>--select product--</option>";
        $pdLists = $this->getProducts();
        if(!empty($pdLists)){
            foreach($pdLists as $v){
                $pd_options .= '<option value="' . $v['ID'] . '" ';
                if($v['ID'] == $id){
                    $pd_options .= 'selected';
                }
                $pd_options .= '>' . $v['post_title'] . '</option>';
            }
        }

        return $pd_options;
    }

    public function getBrandOptions($id="")
    {
        $ret = $this->getProductExtension();
        $option_html = "<option value=''>--select brand--</option>";
        if(!empty($ret['rk_wvc_brand'])){
            foreach($ret['rk_wvc_brand'] as $v){
                $option_html .= '<option value="'.$v.'" ';
                if($v == $id){
                    $option_html .= "selected";
                }
                $option_html .= ">" . $v . "</option>";
            }
        }

        return $option_html;
    }

    public function getWeightOptions($id="")
    {
        $ret = $this->getProductExtension();
        $option_html = "<option value=''>--select weight--</option>";
        if(!empty($ret['rk_wvc_weight'])){
            foreach($ret['rk_wvc_weight'] as $v){
                $option_html .= '<option value="'.$v.'" ';
                if($v == $id){
                    $option_html .= "selected";
                }
                $option_html .= ">" . $v . "</option>";
            }
        }

        return $option_html;
    }

    /**
     * 临时的样品申请操作
     * @param $uid
     * @param $content
     * @return void
     */
    public function sampleApplyTmp($uid, $content)
    {
        global $wpdb;
        $sql = "select uid,content from ". $wpdb->base_prefix . "wvc_sample_apply where uid='".addslashes($uid)."'";
        $row = $wpdb->get_row($sql, ARRAY_A);
        $cont_arr = json_decode($content);
        if(empty($row)){
            $sql = "insert into ". $wpdb->base_prefix . "wvc_sample_apply set uid='".addslashes($uid)."', content='".addslashes(json_encode([0=>$cont_arr]))."',addtime=".time();
        }else{
            $curr_cont = json_decode($row['content'], true);
            $curr_cont[count($curr_cont)] = $cont_arr;
            $sql = "update ". $wpdb->base_prefix . "wvc_sample_apply set  content='".addslashes(json_encode($curr_cont))."' where uid='".addslashes($uid)."'";
        }

        $wpdb->query($sql);
    }

    public function removeSample($uid, $index)
    {
        global $wpdb;
        $sql = "select uid,content from ". $wpdb->base_prefix . "wvc_sample_apply where uid='".addslashes($uid)."'";
        $row = $wpdb->get_row($sql, ARRAY_A);
        if(!empty($row)){
            $cont_arr = json_decode($row['content'], true);
            $cont_new_arr = [];
            for($i=0;$i<count($cont_arr);$i++){
                if($i == $index)
                    continue;

                $cont_new_arr[] = $cont_arr[$i];
            }
            $sql = "update ". $wpdb->base_prefix . "wvc_sample_apply set  content='".addslashes(json_encode($cont_new_arr))."' where uid='".addslashes($uid)."'";
            $wpdb->query($sql);
        }
    }

    public function getSampleList($uid)
    {
        global $wpdb;
        $sql = "select uid,content from ". $wpdb->base_prefix . "wvc_sample_apply where uid='".addslashes($uid)."'";
        $row = $wpdb->get_row($sql, ARRAY_A);
        $retList = [];
        if(!empty($row)){
            $cont_arr = json_decode($row['content'], true);
            if(!empty($cont_arr)){
                $ids = [];
                foreach($cont_arr as $v){
                    $ids[] = $v['wvc_pd_id'];
                    $retList[] = ['ID' => $v['wvc_pd_id'],
                        'title' => $v['wvc_pd_name'],
                        'spec' => $v['wvc_pd_spec'],
                        'brand' => $v['wvc_pd_brand_select'],
                        'weight' => $v['wvc_pd_weight_select'],
                        'cover' => '/wp-content/plugins/rk-wvc-product-form/assets/images/noimage.jpg?v=1'];
                    if($v['wvc_pd_brand_select'] == "other"){ // 如果品牌选择其他，显示自己填写的内容
                        $retList[count($retList) - 1]['brand'] = $v['wvc_pd_brand_other'];
                    }

                    if($v['wvc_pd_weight_select'] == "other"){ // 如果重量选择其他，显示自己填写的内容
                        $retList[count($retList) - 1]['weight'] = $v['wvc_pd_weight_other'];
                    }
                }

                // 获取产品封面
                $sql = "select * from ". $wpdb->base_prefix . "postmeta pm where post_id in (".addslashes(implode(",", $ids)).") and meta_key='rk_wvc_cover_value'";
                $rows = $wpdb->get_results($sql, ARRAY_A);
                if(!empty($rows)){
                    foreach($rows as $row){
                        for($i=0;$i<count($retList);$i++){
                            if($retList[$i]['ID'] == $row['post_id'] && !empty($row['meta_value'])){
                                $retList[$i]['cover'] = $row['meta_value'];
                            }
                        }
                    }
                }
            }
        }

        return $retList;
    }

    public function applyFormSave($uid, $uInfo)
    {
        global $wpdb;
        if(empty($uInfo)){
            return;
        }

        $sql = "select uid,content from ". $wpdb->base_prefix . "wvc_sample_apply where uid='".addslashes($uid)."'";
        $row = $wpdb->get_row($sql, ARRAY_A);
        $pdInfo = "{}";
        if(!empty($row) && !empty($row['content'])){
            $pdInfo = $row['content'];
        }

        $sql = "INSERT INTO ". $wpdb->base_prefix . "wvc_apply set uInfo='".addslashes($uInfo)."',pdInfo='".addslashes($pdInfo)."',addtime=" . time();
        $wpdb->query($sql);
        $sql = "delete from ". $wpdb->base_prefix . "wvc_sample_apply where uid='".addslashes($uid)."'";
        $wpdb->query($sql);
    }

    /**
     * 根据产品ID获取产品的扩展信息
     * 暂时不用
     * @param $id
     * @return array
     */
    public function getProductExtensionByID($id)
    {
        global $wpdb;
        $keyMetas = ["rk_wvc_brand", "rk_wvc_weight", "rk_wvc_cover_value"];
        $sql = "select * from ". $wpdb->base_prefix . "postmeta pm where meta_key='rk_wvc_is_product' and pm.post_id=".ceil($id); //判断是否为有效产品
        $row = $wpdb->get_row($sql, ARRAY_A);
        if(empty($row) || empty($row['meta_value'])){
            return [];
        }

        $sql = "select * from ". $wpdb->base_prefix . "postmeta pm where pm.post_id=".ceil($id); //获取扩展信息
        $rows = $wpdb->get_results($sql, ARRAY_A);
        $ret = [];
        if(!empty($rows)){
            foreach($rows as $row){
                if(in_array($row['meta_key'], $keyMetas)){
                    $ret[$row['meta_key']] = $row['meta_value'];
                }
            }
        }

        return $ret;
    }
}