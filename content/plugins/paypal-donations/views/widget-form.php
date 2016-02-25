<p>
    <label for="<?php echo $title_id; ?>"><?php _e('Title:', PayPalDonations::TEXT_DOMAIN); ?> 
    <input class="widefat" id="<?php echo $title_id; ?>" name="<?php echo $title_name; ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
    </label>
</p>

<p>
    <label for="<?php echo $text_id; ?>"><?php _e('Text:', PayPalDonations::TEXT_DOMAIN); ?> 
    <textarea class="widefat" id="<?php echo $text_id; ?>" name="<?php echo $text_name; ?>"><?php echo esc_attr($instance['text']); ?></textarea>
    </label>
</p>

<p>
    <label for="<?php echo $purpose_id; ?>"><?php _e('Purpose:', PayPalDonations::TEXT_DOMAIN); ?> 
    <input class="widefat" id="<?php echo $purpose_id; ?>" name="<?php echo $purpose_name; ?>" type="text" value="<?php echo esc_attr($instance['purpose']); ?>" />
    </label>
</p>

<p>
    <label for="<?php echo $reference_id; ?>"><?php _e('Reference:', PayPalDonations::TEXT_DOMAIN); ?> 
    <input class="widefat" id="<?php echo $reference_id; ?>" name="<?php echo $reference_name; ?>" type="text" value="<?php echo esc_attr($instance['reference']); ?>" />
    </label>
</p>
