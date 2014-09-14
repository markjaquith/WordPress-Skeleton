/**
 * Admin Script
 */

function GFStripeAdminClass() {

    this.validateKey = function(keyName, key){

        if(key.length == 0){
            this.setKeyStatus(keyName, "");
            return;
        }

        jQuery('#' + keyName).val(key.trim());

        this.setKeyStatusIcon(keyName, "<img src='" + gforms_stripe_admin_strings.spinner + "'/>");

        if( keyName == "live_publishable_key" || keyName == "test_publishable_key" )
            this.validatePublishableKey(keyName, key);
        else
            this.validateSecretKey(keyName, key);

    };

    this.validateSecretKey = function(keyName, key){
        jQuery.post(ajaxurl, {
                action : "gf_validate_secret_key",
                keyName: keyName,
                key : key
            },
            function(response) {

                if(response == "valid"){
                    GFStripeAdmin.setKeyStatus(keyName, "1");
                }
                else if(response == "invalid"){
                    GFStripeAdmin.setKeyStatus(keyName, "0");
                }
                else{
                    GFStripeAdmin.setKeyStatusIcon(keyName, gforms_stripe_admin_strings.validation_error);
                }
            }
        );
    }

    this.validatePublishableKey = function(keyName, key){

        this.setKeyStatusIcon(keyName, "<img src='" + gforms_stripe_admin_strings.spinner + "'/>");

        cc = {
            number:     "4916433572511762",
            exp_month:  "01",
            exp_year:   (new Date()).getFullYear() + 1,
            cvc:        "111",
            name:       "Test Card"
        };

        Stripe.setPublishableKey( key );
        Stripe.card.createToken( cc, function( status, response ) {

            if(status == 200){
                GFStripeAdmin.setKeyStatus(keyName, "1");
            }
            else if( ( status == 400 || status == 402 ) && keyName == "live_publishable_key" ){
                //Live publishable key will return a 400 or 402 status when the key is valid, but the account isn't setup to run live transactions
                GFStripeAdmin.setKeyStatus(keyName, "1");
            }
            else{
                GFStripeAdmin.setKeyStatus(keyName, "0");
            }
        });
    }

    this.initKeyStatus = function(keyName){
        var is_valid = jQuery('#' + keyName + '_is_valid').val();
        var key = jQuery('#' + keyName ).val();

        if(is_valid.length > 0){
            this.setKeyStatus(keyName, is_valid);
        }
        else if( key.length > 0 ){
            this.validateKey(keyName, key);
        }


    }

    this.setKeyStatus = function(keyName, is_valid){

        jQuery('#' + keyName + '_is_valid').val(is_valid);

        var iconMarkup = "";
        if(is_valid == "1")
            iconMarkup = "<i class=\"fa icon-check fa-check gf_valid\"></i>";
        else if(is_valid == "0")
            iconMarkup = "<i class=\"fa icon-remove fa-times gf_invalid\"></i>";

        this.setKeyStatusIcon(keyName, iconMarkup);
    }

    this.setKeyStatusIcon = function(keyName, iconMarkup){
        var icon = jQuery('#' + keyName + "_status_icon");
        if(icon.length > 0)
            icon.remove();

        jQuery('#' + keyName).after("<span id='" + keyName + "_status_icon'>&nbsp;&nbsp;" + iconMarkup + "</span>");
    }
};

jQuery(document).ready(function(){
    GFStripeAdmin = new GFStripeAdminClass();

    GFStripeAdmin.initKeyStatus('live_publishable_key');
    GFStripeAdmin.initKeyStatus('live_secret_key');
    GFStripeAdmin.initKeyStatus('test_publishable_key');
    GFStripeAdmin.initKeyStatus('test_secret_key');
});