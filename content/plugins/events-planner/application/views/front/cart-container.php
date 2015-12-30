<div id="epl_main_container" class="<?php echo $mode;?>">
    <div id="epl_ajax_content">
        <!-- -->
        <form autocomplete="off" action="<?php echo $form_action; ?>" method="post" id="events_planner_shopping_cart">
            
            <?php echo $content; ?>

            <p class="epl_button_wrapper">

            <?php if(isset($prev_step_url)): ?>
            <a href="<?php echo $prev_step_url; ?>" class="epl_button_small" ><?php epl_e('Back'); ?></a>
            <?php endif; ?>
            <?php if(isset($next_step_label)): ?>
            <input type="submit" name="next" class="epl_button" value="<?php echo (isset($next_step_label))?$next_step_label:epl_e('Next'); ?>" >
            <?php endif; ?>
            
            </p>
        </form>

    </div>
</div>
