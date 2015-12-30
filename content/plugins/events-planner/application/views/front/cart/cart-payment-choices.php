<div class="individual_pay_choice clearfix">
    <span class="pay_choice_field">
        <?php

        echo $payment_choice['field'];
        ?>
    </span>
    <span class="pay_choice_field" >
        <?php

        echo htmlspecialchars_decode( $payment_choice['label'] );
        ?>
    </span>
</div>