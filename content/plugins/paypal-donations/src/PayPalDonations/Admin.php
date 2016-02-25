<?php
/**
 * PayPal Donations Settings.
 *
 * Class that renders out the HTML for the settings screen and contains helpful
 * methods to simply the maintainance of the admin screen.
 *
 * @package PayPal Donations
 * @author  Johan Steen <artstorm at gmail dot com>
 * @since   Post Snippets 1.5
 */
class PayPalDonations_Admin
{
    private $plugin_options;
    private $currency_codes;
    private $donate_buttons;
    private $localized_buttons;
    private $checkout_languages;

    const PAGE_SLUG = 'paypal-donations-options';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'scripts'));
    }

    /**
     * To be deprecated soon!
     */
    public function setOptions(
        $options,
        $code,
        $buttons,
        $loc_buttons,
        $checkout_lng
    ) {
        $this->plugin_options = $options;
        $this->currency_codes = $code;
        $this->donate_buttons = $buttons;
        $this->localized_buttons = $loc_buttons;
        $this->checkout_languages = $checkout_lng;
    }


    /**
     * Register the Menu.
     */
    public function menu()
    {
        add_options_page(
            'PayPal Donations Options',
            'PayPal Donations',
            'administrator',
            self::PAGE_SLUG,
            array($this, 'renderpage')
        );
    }

    public function renderpage()
    {
        $data = array(
            'pageSlug'    => PayPalDonations_Admin::PAGE_SLUG,
            'optionDBKey' => PayPalDonations::OPTION_DB_KEY,
        );
        echo PayPalDonations_View::render('admin', $data);
    }

    /**
     * Load CSS and JS on the settings page.
     */
    public function scripts($hook)
    {
        if ($hook != 'settings_page_paypal-donations-options') {
            return;
        }
        $plugin = get_plugin_data(PayPalDonations::FILE, false, false);
        $version = $plugin['Version'];

        wp_register_style(
            'paypal-donations',
            plugins_url('assets/tabs.css', PayPalDonations::FILE),
            array(),
            $version
        );
        wp_enqueue_style('paypal-donations');

        wp_enqueue_script(
            'paypal-donations',
            plugins_url('assets/tabs.js', PayPalDonations::FILE),
            array('jquery'),
            $version,
            false
        );
    }

    /**
     * Register the settings.
     */
    public function init()
    {
        add_settings_section(
            'account_setup_section',
            __('Account Setup', PayPalDonations::TEXT_DOMAIN),
            array($this, 'accountSetupCallback'),
            self::PAGE_SLUG
        );
        add_settings_field(
            'paypal_account',
            __('PayPal Account', PayPalDonations::TEXT_DOMAIN),
            array($this, 'paypalAccountCallback'),
            self::PAGE_SLUG,
            'account_setup_section',
            array(
                'label_for' => 'paypal_account',
                'description' => __(
                    'Your PayPal Email or Secure Merchant Account ID.',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );
        add_settings_field(
            'currency_code',
            __('Currency', PayPalDonations::TEXT_DOMAIN),
            array($this, 'currencyCallback'),
            self::PAGE_SLUG,
            'account_setup_section',
            array(
                'label_for' => 'currency_code',
                'description' => __(
                    'The currency to use for the donations.',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );

        add_settings_section(
            'optional_section',
            __('Optional Settings', PayPalDonations::TEXT_DOMAIN),
            '',
            self::PAGE_SLUG
        );
        add_settings_field(
            'page_style',
            __('Page Style', PayPalDonations::TEXT_DOMAIN),
            array($this, 'pageStyleCallback'),
            self::PAGE_SLUG,
            'optional_section',
            array(
                'label_for' => 'page_style',
                'description' => __(
                    'The name of a custom payment page style that exist in your
                     PayPal account profile.',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );
        add_settings_field(
            'return_page',
            __('Return Page', PayPalDonations::TEXT_DOMAIN),
            array($this, 'returnPageCallback'),
            self::PAGE_SLUG,
            'optional_section',
            array(
                'label_for' => 'return_page',
                'description' => __(
                    'URL to which the donator comes to after completing the
                     donation; for example, a URL on your site that displays a
                     "Thank you for your donation".',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );

        add_settings_section(
            'default_section',
            __('Defaults', PayPalDonations::TEXT_DOMAIN),
            '',
            self::PAGE_SLUG
        );
        add_settings_field(
            'amount',
            __('Amount', PayPalDonations::TEXT_DOMAIN),
            array($this, 'amountCallback'),
            self::PAGE_SLUG,
            'default_section',
            array(
                'label_for' => 'amount',
                'description' => __(
                    'The default amount for a donation (Optional).',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );
        add_settings_field(
            'purpose',
            __('Purpose', PayPalDonations::TEXT_DOMAIN),
            array($this, 'purposeCallback'),
            self::PAGE_SLUG,
            'default_section',
            array(
                'label_for' => 'purpose',
                'description' => __(
                    'The default purpose of a donation (Optional).',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );
        add_settings_field(
            'reference',
            __('Reference', PayPalDonations::TEXT_DOMAIN),
            array($this, 'referenceCallback'),
            self::PAGE_SLUG,
            'default_section',
            array(
                'label_for' => 'reference',
                'description' => __(
                    'Default reference for the donation (Optional).',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );

        add_settings_section(
            'donate_button_section',
            __('Donation Button', PayPalDonations::TEXT_DOMAIN),
            '',
            self::PAGE_SLUG
        );
        add_settings_field(
            'button',
            __('Select Button', PayPalDonations::TEXT_DOMAIN),
            array($this, 'buttonCallback'),
            self::PAGE_SLUG,
            'donate_button_section',
            array(
                'label_for' => 'button',
                'description' => ''
            )
        );
        add_settings_field(
            'button_url',
            __('Custom Button', PayPalDonations::TEXT_DOMAIN),
            array($this, 'buttonUrlCallback'),
            self::PAGE_SLUG,
            'donate_button_section',
            array(
                'label_for' => 'button_url',
                'description' => __(
                    'Enter a URL to a custom donation button.',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );
        add_settings_field(
            'button_localized',
            __('Country and Language', PayPalDonations::TEXT_DOMAIN),
            array($this, 'localizeButtonCallback'),
            self::PAGE_SLUG,
            'donate_button_section',
            array(
                'label_for' => 'button_localized',
                'description' => __(
                    'Localize the language and the country for the button.',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );

        add_settings_section(
            'tab_splitter',
            '',
            array($this, 'tabsCallback'),
            self::PAGE_SLUG
        );

        add_settings_section(
            'extras_section',
            __('Extras', PayPalDonations::TEXT_DOMAIN),
            array($this, 'extrasCallback'),
            self::PAGE_SLUG
        );
        add_settings_field(
            'disable_stats',
            __('Disable PayPal Statistics', PayPalDonations::TEXT_DOMAIN),
            array($this, 'disableStatsCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'disable_stats',
                'description' => ''
            )
        );
        add_settings_field(
            'center_button',
            __(
                'Theme CSS Override: Center Button',
                PayPalDonations::TEXT_DOMAIN
            ),
            array($this, 'centerButtonCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'center_button',
                'description' => ''
            )
        );
        add_settings_field(
            'new_tab',
            __(
                'Open PayPal in New Tab',
                PayPalDonations::TEXT_DOMAIN
            ),
            array($this, 'newTabCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'new_tab',
                'description' => ''
            )
        );
        add_settings_field(
            'remove_lf',
            __('Remove Line Feeds', PayPalDonations::TEXT_DOMAIN),
            array($this, 'removeLfCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'remove_lf',
                'description' => __(
                    'Enable this if your theme or a plugin adds autostyling to shortcodes/widgets.',
                    PayPalDonations::TEXT_DOMAIN
                ),
            )
        );
        add_settings_field(
            'sandbox',
            __('Enable PayPal Sandbox', PayPalDonations::TEXT_DOMAIN),
            array($this, 'setPayPalSandboxCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'sandbox',
                'description' => sprintf(
                    __('Enable PayPal sandbox for testing. Visit %s for more information and to register a merchant and customer testing accounts.', PayPalDonations::TEXT_DOMAIN),
                    '<a href="http://developer.paypal.com/">http://developer.paypal.com/</a>'
                ),
            )
        );
        add_settings_field(
            'set_checkout_language',
            __('Enable Checkout Language', PayPalDonations::TEXT_DOMAIN),
            array($this, 'setCheckoutLangugageCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'set_checkout_language',
                'description' => '',
            )
        );
        add_settings_field(
            'checkout_language',
            __('Checkout Language', PayPalDonations::TEXT_DOMAIN),
            array($this, 'checkoutLangugageCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'checkout_language',
                'description' => '',
            )
        );
        add_settings_field(
            'return_method',
            __('Return Method', PayPalDonations::TEXT_DOMAIN),
            array($this, 'returnMethodCallback'),
            self::PAGE_SLUG,
            'extras_section',
            array(
                'label_for' => 'return_method',
                'description' => __(
                    'Takes effect only if the return page is set.',
                    'post-snippets'
                ),
            )
        );

        register_setting(
            PayPalDonations::OPTION_DB_KEY,
            PayPalDonations::OPTION_DB_KEY
        );
    }

    // -------------------------------------------------------------------------
    // Section Callbacks
    // -------------------------------------------------------------------------

    public function accountSetupCallback()
    {
        printf(
            '<p>%s</p>',
            __('Required fields.', PayPalDonations::TEXT_DOMAIN)
        );
    }

    public function tabsCallback()
    {
        echo "</div><div id='paypal-donations-tab-content-2'>";
    }

    public function extrasCallback()
    {
        printf(
            '<p>%s</p>',
            __(
                'Optional extra settings to fine tune the setup in certain scenarios.',
                PayPalDonations::TEXT_DOMAIN
            )
        );
    }

    // -------------------------------------------------------------------------
    // Fields Callbacks
    // -------------------------------------------------------------------------

    public function paypalAccountCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<input class='regular-text' type='text' id='paypal_account' ";
        echo "name='{$optionKey}[paypal_account]'' ";
        echo "value='{$options['paypal_account']}' />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function currencyCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<select id='currency_code' name='{$optionKey}[currency_code]'>";
        if (isset($options['currency_code'])) {
            $current_currency = $options['currency_code'];
        } else {
            $current_currency = 'USD';
        }
        foreach ($this->currency_codes as $key => $code) {
            echo '<option value="'.$key.'"';
            if ($current_currency == $key) {
                echo ' selected="selected"';
            }
            echo '>'.$code.'</option>';
        }
        echo "</select>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function pageStyleCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<input class='regular-text' type='text' id='page_style' ";
        echo "name='{$optionKey}[page_style]'' ";
        echo "value='{$options['page_style']}' />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function returnPageCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<input class='regular-text' type='text' id='return_page' ";
        echo "name='{$optionKey}[return_page]'' ";
        echo "value='{$options['return_page']}' />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function amountCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<input class='regular-text' type='text' id='amount' ";
        echo "name='{$optionKey}[amount]'' ";
        echo "value='{$options['amount']}' />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function purposeCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<input class='regular-text' type='text' id='purpose' ";
        echo "name='{$optionKey}[purpose]'' ";
        echo "value='{$options['purpose']}' />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function referenceCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<input class='regular-text' type='text' id='reference' ";
        echo "name='{$optionKey}[reference]'' ";
        echo "value='{$options['reference']}' />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function buttonCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);

        $custom = true;
        if (isset($options['button_localized'])) {
            $button_localized = $options['button_localized'];
        } else {
            $button_localized = 'en_US';
        }
        if (isset($options['button'])) {
            $current_button = $options['button'];
        } else {
            $current_button = 'large';
        }

        foreach ($this->donate_buttons as $key => $button) {
            echo "\t<label title='" . esc_attr($key) . "'><input style='padding: 10px 0 10px 0;' type='radio' name='{$optionKey}[button]' value='" . esc_attr($key) . "'";
            if ($current_button === $key) { // checked() uses "==" rather than "==="
                echo " checked='checked'";
                $custom = false;
            }
            echo " /> <img src='" . str_replace('en_US', $button_localized, $button) . "' alt='" . $key  . "' style='vertical-align: middle;' /></label><br /><br />\n";
        }
        echo '  <label><input type="radio" name="'.$optionKey.'[button]" value="custom"';
        checked($custom, true);
        echo '/> '.__('Custom Button', PayPalDonations::TEXT_DOMAIN);

    }

    public function buttonUrlCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<input class='regular-text' type='text' id='button_url' ";
        echo "name='{$optionKey}[button_url]'' ";
        echo "value='{$options['button_url']}' />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function localizeButtonCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        echo "<select id='button_localized' name='{$optionKey}[button_localized]'>";
        if (isset($options['button_localized'])) {
            $button_localized = $options['button_localized'];
        } else {
            $button_localized = 'en_US';
        }
        foreach ($this->localized_buttons as $key => $code) {
            echo '<option value="'.$key.'"';
            if ($button_localized == $key) {
                echo ' selected="selected"';
            }
            echo '>'.$code.'</option>';
        }
        echo "</select>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function disableStatsCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        $checked = isset($options['disable_stats']) ?
            $options['disable_stats'] :
            false;
        echo "<input type='checkbox' id='disable_stats' ";
        echo "name='{$optionKey}[disable_stats]' value='1' ";
        if ($checked) {
            echo 'checked ';
        }
        echo " />";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function centerButtonCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        $checked = isset($options['center_button']) ?
            $options['center_button'] :
            false;
        echo "<input type='checkbox' id='center_button' ";
        echo "name='{$optionKey}[center_button]' value='1' ";
        if ($checked) {
            echo 'checked ';
        }
        echo "/>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function newTabCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        $checked = isset($options['new_tab']) ?
            $options['new_tab'] :
            false;
        echo "<input type='checkbox' id='new_tab' ";
        echo "name='{$optionKey}[new_tab]' value='1' ";
        if ($checked) {
            echo 'checked ';
        }
        echo "/>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function removeLfCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        $checked = isset($options['remove_lf']) ?
            $options['remove_lf'] :
            false;
        echo "<input type='checkbox' id='remove_lf' ";
        echo "name='{$optionKey}[remove_lf]' value='1' ";
        if ($checked) {
            echo 'checked ';
        }
        echo "/>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function setPayPalSandboxCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        $checked = isset($options['sandbox']) ?
            $options['sandbox'] :
            false;
        echo "<input type='checkbox' id='sandbox' ";
        echo "name='{$optionKey}[sandbox]' value='1' ";
        if ($checked) {
            echo 'checked ';
        }
        echo "/>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function setCheckoutLangugageCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        $checked = isset($options['set_checkout_language']) ?
            $options['set_checkout_language'] :
            false;

        echo "<input type='checkbox' id='set_checkout_language' ";
        echo "name='{$optionKey}[set_checkout_language]' value='1' ";
        if ($checked) {
            echo 'checked ';
        }
        echo " />";
        echo "<p class='description'>{$args['description']}</p>";
    }

    public function checkoutLangugageCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);

        echo "<select id='checkout_language' name='{$optionKey}[checkout_language]'>";
        echo "<option value=''>None</option>";
        if (isset($options['checkout_language'])) {
            $checkout_language = $options['checkout_language'];
        } else {
            $checkout_language = 'en_US';
        }
        foreach ($this->checkout_languages as $key => $code) {
            echo '<option value="'.$key.'"';
            if ($checkout_language == $key) {
                echo ' selected="selected"';
            }
            echo '>'.$code.'</option>';
        }
        echo "</select>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    public function returnMethodCallback($args)
    {
        $optionKey = PayPalDonations::OPTION_DB_KEY;
        $options = get_option($optionKey);
        $methods = array(
            __('GET method (default)', 'post-snippets'),
            __('GET method, no variables', 'post-snippets'),
            __('POST method', 'post-snippets')
        );

        echo "<select id='return_method' name='{$optionKey}[return_method]'>";
        if (isset($options['return_method'])) {
            $return_method = $options['return_method'];
        } else {
            $return_method = '0';
        }
        foreach ($methods as $key => $code) {
            echo '<option value="'.$key.'"';
            if ($return_method == $key) {
                echo ' selected="selected"';
            }
            echo '>'.$code.'</option>';
        }
        echo "</select>";

        echo "<p class='description'>{$args['description']}</p>";
    }

    // -------------------------------------------------------------------------
    // HTML and Form element methods
    // -------------------------------------------------------------------------

    /**
     * Checkbox.
     * Renders the HTML for an input checkbox.
     *
     * @param   string  $label      The label rendered to screen
     * @param   string  $name       The unique name to identify the input
     * @param   boolean $checked    If the input is checked or not
     */
    public static function checkbox($label, $name, $checked)
    {
        printf('<input type="checkbox" name="%s" value="true"', $name);
        if ($checked) {
            echo ' checked';
        }
        echo ' />';
        echo ' '.$label;
    }
}
