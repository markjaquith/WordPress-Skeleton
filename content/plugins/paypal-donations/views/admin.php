<!-- Create a header in the default WordPress 'wrap' container -->
<div class="wrap">
    <div id="icon-plugins" class="icon32"></div>
    <h2>PayPal Donations</h2>

    <div style="background:#FFF6D5;border: 1px solid #D1B655;color: #3F2502;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">	
        <p>
            The usage instruction and video tutorial is available on the PayPal Donations plugin <a href="https://www.tipsandtricks-hq.com/paypal-donations-widgets-plugin" target="_blank">documentation page</a>.
        </p>
        <p>
            If you need a feature rich and sleek plugin for accepting PayPal donation and payment then check out our <a target="_blank" href="https://www.tipsandtricks-hq.com/wordpress-estore-plugin-complete-solution-to-sell-digital-products-from-your-wordpress-blog-securely-1059">WP eStore Plugin</a> (it comes with premium support). You can accept recurring payments with it also.
        </p>
    </div>

    <h2 class="nav-tab-wrapper">
        <ul id="paypal-donations-tabs">
            <li id="paypal-donations-tab_1" class="nav-tab nav-tab-active"><?php _e('General', 'paypal-donations'); ?></li>
            <li id="paypal-donations-tab_2" class="nav-tab"><?php _e('Advanced', 'paypal-donations'); ?></li>
        </ul>
    </h2>

    <form method="post" action="options.php">
        <?php settings_fields($optionDBKey); ?>
        <div id="paypal-donations-tabs-content">
            <div id="paypal-donations-tab-content-1">
                <?php do_settings_sections($pageSlug); ?>
            </div>
        </div>
        <?php submit_button(); ?>
    </form>
