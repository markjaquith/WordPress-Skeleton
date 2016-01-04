<div class="wrap">

    <h2><?php _e( 'Ninja Forms Upgrade Processing', 'ninja-forms' ); ?></h2>

    <?php foreach ( NF_UpgradeHandler()->upgrades as $upgrade ): ?>
        <?php if( ! $upgrade->isComplete() ) : ?>
            <div id="nf_upgrade_<?php echo $upgrade->name ?>">
                <dl class="menu-item-bar nf_upgrade">
                    <dt class="menu-item-handle">
                        <span class="item-title ninja-forms-field-title nf_upgrade__name"><?php echo $upgrade->nice_name; ?></span>
                                <span class="item-controls">
                                    <span class="item-type">
                                        <span class="item-type-name nf_upgrade__status">
                                            <!-- TODO: Move inline styles to Stylesheet. -->
                                            <!-- Status: INCOMPLETE -->
                                            <span class="dashicons dashicons-no" style="color: red; display: none;"></span>
                                            <!-- Status: PROCESSING -->
                                            <span class="spinner" style="display: none;margin-top: -1.5px;margin-right: -2px;"></span>
                                            <!-- Status: COMPLETE -->
                                            <span class="dashicons dashicons-yes" style="color: green; display: none;"></span>
                                        </span>
                                    </span>
                                </span>
                    </dt>
                </dl>
                <div class="menu-item-settings menu-item-settings--nf-upgrade type-class inside" style="display: none;">
                    <div id="progressbar_<?php echo $upgrade->name; ?>" class="progressbar">
                        <div class="progress-label">
                            <?php _e( 'Processing', 'ninja-forms' ); ?>
                        </div>
                    </div>
                    <p><?php echo $upgrade->description; ?></p>
                    <div class="nf-upgrade-handler__errors" style="display: none; box-sizing: border-box; border: 1px solid #DEDEDE; padding-left: 5px; margin-right: 10px; border-radius: 3px; background-color: #EDEDED;">
                        <h3 class="nf-upgrade-handler__errors__title">
                            <?php _e( 'Error', 'ninja-forms' ); ?>
                        </h3>
                        <pre class="nf-upgrade-handler__errors__text" style="padding-left: 10px;">

                        </pre>
                        <p>
                            <?php echo sprintf( __('Please %scontact support%s with the error seen above.', 'ninja-forms' ) , '<a href="https://ninjaforms.com/contact/">', '</a>' ); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div> <!-- /.wrap -->

<div class="nf-upgrade-complete" style="display: none;">
    <?php _e( 'Ninja Forms has completed all available upgrades!', 'ninja-forms' ); ?>
</div><!-- /.nf-upgrade-complete -->
<div class="nf-upgrade-complete-buttons" style="display: none;">
    <div id="nf-admin-modal-update">
        <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=ninja-forms' );?>"><?php _e( 'Go to Forms', 'ninja-forms' ); ?></a>
    </div>
</div><!-- /.nf-upgrade-complete-buttons -->
