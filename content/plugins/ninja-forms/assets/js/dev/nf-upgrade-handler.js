jQuery(document).ready(function($) {

    $( '.nf-upgrade-complete' ).nfAdminModal( { title: nfUpgradeHandler.nf_upgrade_complete_title, buttons: '.nf-upgrade-complete-buttons' } );

    function UpgradeHandler( upgrade ) {

        this.upgrade = upgrade;

        this.process = function( step, total_steps, args ) {

            step = typeof step !== 'undefined' ? step : 0;
            total_step = typeof total_step !== 'undefined' ? total_step : 0;
            args = typeof args !== 'undefined' ? args : [];

            console.log( 'Upgrade: ' + this.upgrade );

            $.post(
                ajaxurl,
                {
                    upgrade: this.upgrade,
                    step: parseInt( step ),
                    total_steps: parseInt( total_steps ),
                    args: args,
                    action: 'nf_upgrade_handler'
                },
                function (response) {

                    var elem = $( '#nf_upgrade_' + upgradeHandler.upgrade );

                    try {
                        response = $.parseJSON(response);
                    } catch( e ) {

                        // TODO: move error display to Upgrade object

                        elem.find( '.spinner' ).css( 'display', 'none' ).css( 'visibility', 'hidden' );

                        elem.find( '.dashicons-no' ).css( 'display', 'block' );

                        elem.find( '.nf-upgrade-handler__errors__text').html('Bad Response :\'(<br/>' + e + "<br />" + response );
                        elem.find( '.nf-upgrade-handler__errors').slideDown();

                        return;
                    }

                    console.log( 'DEBUG: NF_UpgradeHandler step response: ');
                    console.log( response );

                    if( undefined == response ) {

                        // TODO: move error display to Upgrade object

                        elem.find( '.spinner' ).css( 'display', 'none' ).css( 'visibility', 'hidden' );

                        elem.find( '.dashicons-no' ).css( 'display', 'block' );

                        elem.find( '.nf-upgrade-handler__errors__text').html('Empty Response :\'(');
                        elem.find( '.nf-upgrade-handler__errors').slideDown();

                        return;
                    }

                    if( response.errors ) {
                        // TODO: move error display to Upgrade object
                        elem.find( '.spinner' ).css( 'display', 'none' ).css( 'visibility', 'hidden' );

                        elem.find( '.dashicons-no' ).css( 'display', 'block' );

                        var error_text = '';

                        $.each( response.errors, function( index, error ) {
                            error_text = error_text + '[' + index + '] ' + error + '<br />';
                        });

                        elem.find( '.nf-upgrade-handler__errors__text').html('Processing Error :\'(<br />' + error_text );
                        elem.find( '.nf-upgrade-handler__errors').slideDown();

                        $( '#progressbar_' + response.upgrade).slideUp();

                        return;
                    }

                    var progressbar = $( '#progressbar_' + response.upgrade ).progressbar({
                        value: 100 * ( response.step / response.total_steps )
                    });

                    //TODO: move animations to Upgrade object
                    elem.find( '.spinner' ).css( 'display', 'block' ).css( 'visibility', 'visible' );

                    elem.find( '.dashicons-no' ).css( 'display', 'none' );

                    elem.find( '.inside') .slideDown();

                    if ( undefined != response.complete ) {

                        //TODO: move animations to Upgrade object
                        elem.find( '.inside' ).slideUp();

                        elem.find( '.spinner' ).css( 'display', 'none' ).css( 'visibility', 'hidden' );

                        elem.find( '.dashicons-yes').css( 'display', 'block' );

                        if ( undefined != response.nextUpgrade ) {
                            upgradeHandler.upgrade = response.nextUpgrade;

                            $( '#nf_upgrade_' + upgradeHandler.upgrade ).find( '.spinner' ).css( 'display', 'block' ).css( 'visibility', 'visible' );

                            $( '#nf_upgrade_' + upgradeHandler.upgrade ).find( '.inside') .slideDown();

                            upgradeHandler.process();
                            return;
                        }

                        console.log( 'DEBUG: NF_UpgradeHandler says "It is finished!"' );

                        $( '.nf-upgrade-complete' ).nfAdminModal( 'open' );

                        return;
                    }

                    upgradeHandler.process( response.step, response.total_steps, response.args  );
                }
            ).fail(function() {
                alert( "error" );
            });

        };

    }

    function Upgrade( name ) {

        this.name = name;

        this.elem = '#nf_upgrade_' + name;

        this.open = function() {

            jQuery( this.elem).slideDown();

        };

        this.close = function() {

            jQuery( this.elem).slideUp();

        };

    }

    if( "undefined" != typeof nfUpgradeHandler  ) {

        console.log('DEBUG: NF_UpgradeHandler first upgrades is ' + nfUpgradeHandler.upgrade);

        var upgradeHandler = new UpgradeHandler(nfUpgradeHandler.upgrade);

        $('.progressbar').progressbar({value: 0});

        var first_upgrade = $('#nf_upgrade_' + upgradeHandler.upgrade);

        //TODO: move animations to Upgrade object
        first_upgrade.find('.spinner').css('display', 'block').css('visibility', 'visible');

        first_upgrade.find('.dashicons-no').css('display', 'none');

        first_upgrade.find('.inside').slideDown();

        upgradeHandler.process();

    } else {

        // No Upgrades to run, return to All Forms Page
        document.location.href = "admin.php?page=ninja-forms";

    }

});