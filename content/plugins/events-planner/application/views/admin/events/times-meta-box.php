<div id="epl_event_type_0">
    <?php

    $m = 'Available in the Pro Version:
<ul>
<li>- Give your user the option to select a different <span class="epl_font_red">time</span> for different days.</li>
<li>- Give your user the option to select a different <span class="epl_font_red">price</span> for different days.</li>
<li>- Ability to have <span class="epl_font_red">time specific pricing</span>.</li>
</ul>

';
    echo epl_show_ad( $m );
    ?>


    <div class="epl_box epl_highlight">
        <div class="epl_box_content">
            <?php

            echo epl_get_the_label( '_epl_free_event', $price_option_fields );
            echo epl_get_the_field( '_epl_free_event', $price_option_fields );
            ?>
        </div>

    </div>




    <div id="epl_time_price_section" class="">

        <?php echo $time_price_section; ?>


        </div>

</div>
