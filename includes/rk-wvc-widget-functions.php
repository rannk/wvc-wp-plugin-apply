<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
require_once dirname( __FILE__ ) . '/abstracts/abstract-rk-wvc-widget.php';
require_once dirname( __FILE__ ) . '/widgets/class-rk-wvc-widget-cart.php';

/**
 * Register Widgets.
 *
 */
function rk_wcv_register_widgets() {
	register_widget( 'RK_WVC_Widget_Cart' );
}
add_action( 'widgets_init', 'rk_wcv_register_widgets' );
