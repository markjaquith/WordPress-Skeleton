    <?php if ( isset( $message ) ): ?>
        <div class="epl_box epl_error">
            <div class="epl_box_content">
            <?php echo $message; ?>
        </div>
    </div>
    <?php endif; ?>
<div class="">



                <div id="admin_cart_section" class="epl_cart_section">
        <?php

                if ( isset( $cart_data ) )
                    echo $cart_data;
        ?>
            </div>
            <div id="admin_totals_section">
        <?php

                //if ( isset( $cart_totals ) )
                  //  echo $cart_totals;
        ?>

            </div>
            <div id="admin_regis_section">
        <?php

                if ( isset( $attendee_info ) )
                    echo $attendee_info;
        ?>
    </div>


</div>