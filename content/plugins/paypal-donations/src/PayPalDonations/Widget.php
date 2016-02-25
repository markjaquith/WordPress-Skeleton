<?php
/**
 * The Class for the Widget.
 *
 * @package  PayPal Donations
 * @author   Johan Steen <artstorm at gmail dot com>
 */
class PayPalDonations_Widget extends WP_Widget
{
    /**
     * Register the Widget.
     */
    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'widget_paypal_donations',
            'description' => __(
                'PayPal Donation Button',
                PayPalDonations::TEXT_DOMAIN
            )
        );
        parent::__construct('paypal_donations', 'PayPal Donations', $widget_ops);
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        extract($args);
        // global $paypal_donations;
        $paypal_donations = PayPalDonations::getInstance();

        // Get the settings
        $title = apply_filters('widget_title', $instance['title']);
        $text = $instance['text'];
        $purpose = $instance['purpose'];
        $reference = $instance['reference'];

        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        if ($text) {
            echo wpautop($text);
        }
        echo $paypal_donations->generateHtml($purpose, $reference);
        echo $after_widget;
    }
    
    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['text'] = $new_instance['text'];
        $instance['purpose'] = strip_tags(stripslashes($new_instance['purpose']));
        $instance['reference'] = strip_tags(stripslashes($new_instance['reference']));

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        // Default Widget Settings
        $defaults = array(
            'title'     => __('Donate', PayPalDonations::TEXT_DOMAIN),
            'text'      => '',
            'purpose'   => '',
            'reference' => ''
        );
        $instance = wp_parse_args((array) $instance, $defaults);

        $data = array(
            'instance' => $instance,
            'title_id' => $this->get_field_id('title'),
            'title_name' => $this->get_field_name('title'),
            'text_id' => $this->get_field_id('text'),
            'text_name' => $this->get_field_name('text'),
            'purpose_id' => $this->get_field_id('purpose'),
            'purpose_name' => $this->get_field_name('purpose'),
            'reference_id' => $this->get_field_id('reference'),
            'reference_name' => $this->get_field_name('reference'),
        );
        echo PayPalDonations_View::render('widget-form', $data);
    }
}
