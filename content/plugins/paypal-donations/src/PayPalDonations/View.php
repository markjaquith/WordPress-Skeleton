<?php
/**
 * Class for MVC like View Handling in WordPress.
 *
 * @package  PayPal Donations
 * @author   Johan Steen <artstorm at gmail dot com>
 */
class PayPalDonations_View
{
    /**
     * Render a View.
     *
     * @param  string  $view      View to render.
     * @param  array   $data      Data to be used within the view.
     * @return string             The processed view.
     */
    public static function render($view, $data = null)
    {
        // Handle data
        ($data) ? extract($data) : null;

        ob_start();
        include(plugin_dir_path(__FILE__).'../../views/'.$view.'.php');
        $view = ob_get_contents();
        ob_end_clean();

        // Optimize the view by removing linefeeds and multiple spaces
        if (isset($pd_options['remove_lf'])) {
            $view = trim(preg_replace('/\s+/', ' ', $view));
        }

        return $view;
    }
}
