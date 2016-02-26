<div class="epl_cart_wrapper">

    <?php if ( isset( $message ) ): ?>
        <div class="epl_regis_message_warn">
        <?php echo $message; ?>
    </div>

    <?php endif; ?>

    <?php foreach ( ( array ) $cart_data['cart_items'] as $k => $event ): ?>

            <div class="event_name section"><?php echo $event['title']; ?></div>

    <?php if ( isset( $cart_data['available_spaces'][$k] ) ) : ?>



    <?php echo $cart_data['available_spaces'][$k]; ?>

    <?php endif; ?>



                <div class="section">

        <?php echo $event['event_dates']; ?>

            </div>

    <?php if ( $event['event_time_and_prices'] ): ?>
                    <div class="section">
        <?php echo $event['event_time_and_prices']; ?>
                </div>
    <?php endif; ?>




    <?php endforeach; ?>
    <?php if ( !isset( $cart_data['free_event'] ) ): ?>

                        <div id="epl_totals_wrapper" class="section">

        <?php if ( !$cart_data['view_mode'] != 'overview' ): ?>

        <?php endif; ?>

        <?php

                            echo $cart_data['cart_totals'];
        ?>
        <?php if ( $mode != 'overview' ): ?>
                                <a href="#" id="calculate_total_due" class="epl_button_small epl_fr">Refresh Total</a>
        <?php endif; ?>
                            </div>

    <?php if ( $mode != 'overview' ): ?>
                                    <div class="section">

        <?php

                                    echo $cart_data['pay_options'];
        ?>
                                </div>
        <?php endif; ?>
<?php endif; ?>

</div>




