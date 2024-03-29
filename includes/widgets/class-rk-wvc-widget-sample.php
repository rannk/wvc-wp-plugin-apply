<?php
defined( 'ABSPATH' ) || exit;

/**
 * Widget cart class.
 */
class RK_WVC_Widget_Sample extends RK_WVC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rk_wvc_button';
		$this->widget_description = 'WVC Get Sample Button';
		$this->widget_id          = 'rk_wvc_widget_sample';
		$this->widget_name        = 'Sample Button';
		$this->settings           = array(
			'title'         => array(
				'type'  => 'text',
				'std'   => 'Get Sample',
				'label' => 'Title',
			),
            'class' => array(
                'type'  => 'text',
                'label' => 'class',
            ),
		);

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
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = 'Get Sample';
		}

		$this->widget_start( $args, $instance );

        $html = <<<html
<a href="#;" class="wvc_apply_btn {class}">
<span class="btIconWidgetContent">
<span class="btIconWidgetTitle">{title}</span></span></a>
html;

        $html = str_replace("{title}", $instance['title'], $html);
        $html = str_replace("{class}", $instance['class'], $html);

        echo $html;

		$this->widget_end( $args );
	}
}
