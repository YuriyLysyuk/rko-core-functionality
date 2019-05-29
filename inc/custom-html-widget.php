<?php
/**
 *  Custom HTML widget with class
 *
 * @package      CoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.0
 * @license      GPL-2.0+
**/

class LY_Custom_HTML extends WP_Widget {
    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @since 1.0.0
     * @var array
     */
    protected $defaults;
    /**
     * Constructor
     *
     * @since 1.0.0
     */
    function __construct() {
        // widget defaults
        $this->defaults = array(
            'title'          => '',
            'widget_content' => '',
            'custom_class' => '',
        );
        // Widget Slug
        $widget_slug = 'ly-custom-html';
        // widget basics
        $widget_ops = array(
            'classname'   => '',
            'description' => 'LY Custom HTML without wrap and with custom class'
        );
        // widget controls
        $control_ops = array(
            'id_base' => $widget_slug,
            //'width'   => '400',
        );
        // load widget
        parent::__construct( $widget_slug, 'LY Custom HTML', $widget_ops, $control_ops );
    }
    /**
     * Outputs the HTML for this widget.
     *
     * @since 1.0.0
     * @param array $args An array of standard parameters for widgets in this theme
     * @param array $instance An array of settings for this widget instance
     */
    function widget( $args, $instance ) {
        extract( $args );
        // Merge with defaults
        $instance = wp_parse_args( (array) $instance, $this->defaults );
        //print_r($instance);
        // Добавляем в обертку виджета заданный класс
        if ( !empty( $instance['custom_class'] ) ) {
            $before_widget = preg_replace( '/(?<=\sclass=["\'])/', $instance['custom_class'].' ', $before_widget );
        }
        echo $before_widget;
        // Title
        if ( !empty( $instance['title'] ) ) {
            echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
        }
        // Textaria
        if ( !empty( $instance['widget_content'] ) ) {

            echo apply_filters( 'widget_content', $instance['widget_content'], $instance, $this );
        }
        echo $after_widget;
    }
    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @since 1.0.0
     * @param array $new_instance An array of new settings as submitted by the admin
     * @param array $old_instance An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     */
    function update( $new_instance, $old_instance ) {
        $new_instance['title']           = strip_tags( $new_instance['title'] );
        $new_instance['widget_content']  = $new_instance['widget_content'];
        $new_instance['custom_class']    = strip_tags( $new_instance['custom_class'] );
        return $new_instance;
    }
    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @since 1.0.0
     * @param array $instance An array of the current settings for this widget
     */
    function form( $instance ) {
        // Merge with defaults
        $instance = wp_parse_args( (array) $instance, $this->defaults );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Заголовок:</label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'custom_class' ); ?>">CSS класс:</label>
            <input type="text" id="<?php echo $this->get_field_id( 'custom_class' ); ?>" name="<?php echo $this->get_field_name( 'custom_class' ); ?>" value="<?php echo esc_attr( $instance['custom_class'] ); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'widget_content' ); ?>">Текст:</label>
            <textarea type="text" id="<?php echo $this->get_field_id( 'widget_content' ); ?>" name="<?php echo $this->get_field_name( 'widget_content' ); ?>" rows="16" cols="20" class="widefat"><?php echo esc_attr( $instance['widget_content'] ); ?></textarea>
        </p>
        <?php
    }
}
add_action( 'widgets_init', function(){ register_widget( 'LY_Custom_HTML' ); } );

// Use shortcodes in widget.
add_filter('widget_content', 'do_shortcode');