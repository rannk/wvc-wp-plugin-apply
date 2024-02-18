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
}