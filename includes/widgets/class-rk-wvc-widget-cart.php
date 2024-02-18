<?php
defined( 'ABSPATH' ) || exit;

/**
 * Widget cart class.
 */
class RK_WVC_Widget_Cart extends RK_WVC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rk_wvc wvc_apply_cart';
		$this->widget_description = 'WVC apply cart';
		$this->widget_id          = 'rk_wvc_widget_cart';
		$this->widget_name        = 'Apply Cart';
		$this->settings           = array(
			'title'         => array(
				'type'  => 'text',
				'std'   => 'Apply Cart',
				'label' => 'Title',
			),
			'hide_if_empty' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => 'Hide if cart is empty'
			),
		);

		if ( is_customize_preview() ) {
			wp_enqueue_script( 'rk-wvc-cart-fragments' );
		}

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( apply_filters( 'woocommerce_widget_cart_is_hidden', is_cart() || is_checkout() ) ) {
			return;
		}

        wp_enqueue_script( 'rk-wvc-cart-fragments' );

		$hide_if_empty = empty( $instance['hide_if_empty'] ) ? 0 : 1;

		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = 'Apply Cart';
		}

		$this->widget_start( $args, $instance );

		if ( $hide_if_empty ) {
			echo '<div class="hide_cart_widget_if_empty">';
		}

        load_template(__DIR__ . "/../../templates/cart.php");

		if ( $hide_if_empty ) {
			echo '</div>';
		}

		$this->widget_end( $args );
	}
}
