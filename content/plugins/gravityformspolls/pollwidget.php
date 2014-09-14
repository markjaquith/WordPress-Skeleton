<?php
add_action( 'widgets_init', 'gpoll_register_poll_widget' );

if(!function_exists("gpoll_register_poll_widget")){
    function gpoll_register_poll_widget() {
        register_widget( 'GFPollsPollWidget' );
    }
}

if(!class_exists("GFPollsPollWidget")){
    class GFPollsPollWidget extends WP_Widget {

        function GFPollsPollWidget() {
            $this->WP_Widget( 'gpoll_poll_widget', __('Poll','gravityformspolls'),
                array( 'classname' => 'gpoll_poll_widget', 'description' => __('Gravity Forms Poll Widget', "gravityformspolls") ),
                array( 'width' => 200, 'height' => 250, 'id_base' => 'gpoll_poll_widget' )
            );
        }

        function widget( $args, $instance ) {

            $gf_polls = GFPolls::get_instance();

            wp_enqueue_script('gpoll_js', plugins_url('js/gpoll.js', __FILE__), array('jquery'), $gf_polls->_version);
            $gf_polls->localize_scripts();
            wp_enqueue_style('gpoll_css', plugins_url('css/gpoll.css', __FILE__), null, $gf_polls->_version);

            extract( $args );
            echo $before_widget;
            $title = apply_filters('widget_title', $instance['title'] );
            $form_id= $instance['form_id'];

            if ( $title )
                echo $before_title . $title . $after_title;
            $mode =  rgar($instance, "mode");
            $override_form_settings = rgar($instance, 'override_form_settings');
            if ( "" === $override_form_settings )
                $override_form_settings = true;

            if ( $override_form_settings ) {
                $style = rgar($instance, "style");
                $display_results = rgar($instance, "display_results");
                $display_results_string =  $display_results == "1" ? "true" : "false";
                $show_results_link = rgar($instance, "show_results_link");
                $show_results_link_string =  $show_results_link == "1" ? "true" : "false";
                $show_percentages =  rgar($instance, "show_percentages");
                $show_percentages_string =  $show_percentages == "1" ? "true" : "false";
                $show_counts = rgar($instance, "show_counts");
                $show_counts_string =  $show_counts == "1" ? "true" : "false";
                $block_repeat_voters = rgar($instance, "block_repeat_voters");
                $cookie = $block_repeat_voters == "1" ? rgar($instance, "cookie") :  "";

                $displayconfirmation = rgar($instance, "displayconfirmation");
                $displayconfirmation = $displayconfirmation == "1" ? "true" : "false";


            } else {

                $form = GFFormsModel::get_form_meta($form_id);
                $style = $gf_polls->get_form_setting($form, "style");
                $display_results = $gf_polls->get_form_setting($form, "displayResults");
                $display_results_string =  $display_results ? "true" : "false";
                $show_results_link = $gf_polls->get_form_setting($form, "showResultsLink");
                $show_results_link_string =  $show_results_link ? "true" : "false";
                $show_percentages =  $gf_polls->get_form_setting($form, "showPercentages");
                $show_percentages_string =  $show_percentages ? "true" : "false";
                $show_counts = $gf_polls->get_form_setting($form, "showCounts");
                $show_counts_string =  $show_counts ? "true" : "false";
                $block_repeat_voters = $gf_polls->get_form_setting($form, "blockRepeatVoters");
                $cookie = $block_repeat_voters ? $gf_polls->get_form_setting($form, "cookie") : "";
                $displayconfirmation = "true";

            }
            $tabindex = rgar($instance, "tabindex");
            $showtitle = rgar($instance, "showtitle");
            $showtitle = $showtitle == "1" ? "true" : "false";

            $showdescription =  rgar($instance, "showdescription");
            $showdescription = $showdescription == "1" ? "true" : "false";
            $ajax = rgar($instance, "ajax");
            $ajax = $ajax == "1" ? "true" : "false";
            $disable_scripts = rgar($instance, "disable_scripts");
            $disable_scripts = $disable_scripts == "1" ? "true" : "false";


            $shortcode = "[gravityforms action=\"polls\" field=\"0\" id=\"{$form_id}\" style=\"{$style}\" mode=\"{$mode}\" display_results=\"{$display_results_string}\" show_results_link=\"{$show_results_link_string}\" cookie=\"{$cookie}\" ajax=\"{$ajax}\" disable_scripts=\"{$disable_scripts}\" tabindex=\"{$tabindex}\" title=\"{$showtitle}\" description=\"{$showdescription}\" confirmation=\"{$displayconfirmation}\" percentages=\"{$show_percentages_string}\" counts=\"{$show_counts_string}\"]";


            echo do_shortcode($shortcode);

            echo $after_widget;

        }

        function update( $new_instance, $old_instance ) {

            $instance = $old_instance;
            $instance["title"] = strip_tags( $new_instance["title"] );
            $instance["form_id"] = $new_instance["form_id"];


            $instance["override_form_settings"] = $new_instance["override_form_settings"];
            $instance["showtitle"] = empty( $new_instance["showtitle"] ) ? __("Poll", "gravityformspolls") : $new_instance["showtitle"];
            $instance["mode"] = $new_instance["mode"];
            $instance["ajax"] = empty( $new_instance["ajax"] ) ? "0" : $new_instance["ajax"];
            $instance["disable_scripts"] = empty( $new_instance["disable_scripts"] ) ? "0" : $new_instance["disable_scripts"];
            $instance["showdescription"] = empty( $new_instance["showdescription"] ) ? "0" : $new_instance["showdescription"];
            $instance["tabindex"] = empty( $new_instance["tabindex"] ) ? "0" : $new_instance["tabindex"];
            $instance["displayconfirmation"] = empty( $new_instance["displayconfirmation"] ) ? "0" : $new_instance["displayconfirmation"];
            $instance["style"] = $new_instance["style"];
            $instance["display_results"] = empty( $new_instance["display_results"] ) ? "0" : $new_instance["display_results"];
            $instance["show_results_link"] = empty( $new_instance["show_results_link"] ) ? "0" : $new_instance["show_results_link"];
            $instance["show_percentages"] = empty( $new_instance["show_percentages"] ) ? "0" : $new_instance["show_percentages"];
            $instance["show_counts"] = empty( $new_instance["show_counts"] ) ? "0" : $new_instance["show_counts"];
            $instance["block_repeat_voters"] = empty( $new_instance["block_repeat_voters"] ) ? "0" : $new_instance["block_repeat_voters"];
            $instance["cookie"] = $new_instance["cookie"];

            return $instance;
        }

        function form( $instance ) {
            $first_form_id = 1;
            $forms = RGFormsModel::get_forms();
            if(!empty($forms)) {
                $first_form_id = $forms[0]->id;
            }

            $override_form_settings = rgar($instance, 'override_form_settings');
            $widget_has_legacy_override_settings = "" === $override_form_settings && isset($instance["style"]);
            $instance = wp_parse_args( (array) $instance, array(
                'title' => __("Poll", "gravityformspolls"),
                'tabindex' => '1',
                'showtitle' => '0',
                'showdescription' => '0',
                'displayconfirmation' => '0',
                'ajax' => '0',
                'disable_scripts' => '0',
                'form_id' => $first_form_id,
                'mode' => 'results',
                'style' => 'green',
                'display_results' => '1',
                'show_results_link' => '1',
                'show_percentages' => '1',
                'show_counts' => '1',
                'block_repeat_voters' => '0',
                'cookie' => '',
                'override_form_settings' => '0'

            ) );

            if ( $widget_has_legacy_override_settings  )
                $instance['override_form_settings']='1';

            $override_form_ids = apply_filters('gpoll_widget_override' , array());
            $form_id = $instance['form_id'];

            if (  $widget_has_legacy_override_settings || $override_form_settings ) {
                $show_override_radio = true;
                $show_override_settings = true;
            } elseif (  in_array($form_id, $override_form_ids) && false === $widget_has_legacy_override_settings ) {
                $show_override_radio = true;
                $show_override_settings = false;
            } else {
                $show_override_radio = false;
                $show_override_settings = false;
            }

            ?>

            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title", "gravityformspolls"); ?>:</label>
                <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
            </p>

            <p>

                <label for="<?php echo $this->get_field_id( 'form_id' ); ?>"><?php _e("Select a Form", "gravityformspolls"); ?>:</label>
                <select class="gpoll_forms_dropdown" id="<?php echo $this->get_field_id( 'form_id' ); ?>" name="<?php echo $this->get_field_name( 'form_id' ); ?>" style="width:90%;" <?php if ( false === $widget_has_legacy_override_settings ) : ?>onchange="radioButtonsSelector = '#<?php echo $this->id ?>_gpoll_override_radio'; radioOffSelector = '#widget-<?php echo $this->id ?>-override_form_settings_0'; settingsSelector = '#<?php echo $this->id ?>_gpoll_overrides, #<?php echo $this->id . "_gpoll_override_radio"?>'; if (jQuery.inArray(parseInt(this.value), <?php echo json_encode($override_form_ids) ?>) >= 0 ) {jQuery(radioButtonsSelector).show(); } else { jQuery(settingsSelector).hide();}jQuery(radioOffSelector).trigger('click');"<?php endif ?>>
                    <?php
                    $forms = RGFormsModel::get_forms(1, "title");
                    foreach ($forms as $f) {
                        $form = RGFormsModel::get_form_meta($f->id);
                        $poll_fields = GFCommon::get_fields_by_type( $form, array( 'poll' ) );
                        if ( false === empty ( $poll_fields ) ) {
                            $selected = $f->id == $instance['form_id'] ? 'selected="selected"' : "";
                            echo '<option value="'.$f->id.'" ' . $selected . '>'.$f->title.'</option>';
                        }

                    }
                    ?>
                </select>
            </p>



            <p>
                <label for="<?php echo $this->get_field_id( 'mode' ); ?>"><?php _e("Display Mode", "gravityformspolls"); ?>:</label>
                <select id="<?php echo $this->get_field_id( 'mode' ); ?>" name="<?php echo $this->get_field_name( 'mode' ); ?>" style="width:90%;" onchange="if ('poll' === this.value) jQuery('#<?php echo $this->id ?>_gpoll_override_poll_settings, #<?php echo $this->id ?>_gpoll_form_settings').show('slow'); else jQuery('#<?php echo $this->id ?>_gpoll_override_poll_settings, #<?php echo $this->id ?>_gpoll_form_settings').hide('slow', function(){jQuery(this).css('display', 'none')}); ">
                    <option value="poll" <?php echo $instance['mode'] == "poll" ? 'selected="selected"' : '' ; ?>><?php _e("Poll", "gravityformspolls"); ?></option>
                    <option value="results" <?php echo $instance['mode'] == "results" ? 'selected="selected"' : '' ?>><?php _e("Results only", "gravityformspolls"); ?></option>
                </select>
            </p>
            <div id="<?php echo $this->id ?>_gpoll_override_radio" style="<?php echo $show_override_radio ? "" : "display:none"   ?>">
                <p>
                    <input type="radio" name="<?php echo $this->get_field_name( 'override_form_settings' ); ?>" value="0" <?php checked($instance['override_form_settings'], "0"); ?> id="<?php echo $this->get_field_id( 'override_form_settings' ) . "_0"; ?>" onclick="jQuery('#<?php echo $this->id ?>_gpoll_overrides').hide('slow');"> <label for="<?php echo $this->get_field_id( 'override_form_settings' ) . "_0"; ?>"><?php _e("Use form settings", "gravityformspolls"); ?></label><br>
                    <input type="radio" name="<?php echo $this->get_field_name( 'override_form_settings' ); ?>" value="1" <?php checked($instance['override_form_settings'], "1"); ?> id="<?php echo $this->get_field_id( 'override_form_settings' ) . "_1"; ?>" onclick="jQuery('#<?php echo $this->id ?>_gpoll_overrides').show('slow');"> <label for="<?php echo $this->get_field_id( 'override_form_settings' ) . "_1"; ?>"><?php _e("Override form settings", "gravityformspolls"); ?></label><br>
                </p>
            </div>

            <div id="<?php echo $this->id ?>_gpoll_overrides" style="<?php echo $show_override_settings ? "" : "display:none"  ?>">


                <p>
                    <strong><?php _e("Results Settings", "gravityformspolls"); ?></strong><br />

                    <input type="checkbox" name="<?php echo $this->get_field_name( 'show_percentages' ); ?>" id="<?php echo $this->get_field_id( 'show_percentages' ); ?>" <?php checked($instance['show_percentages']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'show_percentages' ); ?>"><?php _e("Show percentages", "gravityformspolls"); ?></label><br/>

                    <input type="checkbox" name="<?php echo $this->get_field_name( 'show_counts' ); ?>" id="<?php echo $this->get_field_id( 'show_counts' ); ?>" <?php checked($instance['show_counts']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'show_counts' ); ?>"><?php _e("Show counts", "gravityformspolls"); ?></label><br/>
                <p>
                    <label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e("Style", "gravityformspolls"); ?>:</label>
                    <select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" style="width:90%;">
                        <option value="green" <?php echo $instance['style'] == "green" ? 'selected="selected"' : '' ?>><?php _e("Green","gravityformspolls") ?></option>
                        <option value="blue" <?php echo $instance['style'] == "blue" ? 'selected="selected"' : '' ?>><?php _e("Blue","gravityformspolls") ?></option>
                        <option value="red" <?php echo $instance['style'] == "red" ? 'selected="selected"' : '' ?>><?php _e("Red","gravityformspolls") ?></option>
                        <option value="orange" <?php echo $instance['style'] == "orange" ? 'selected="selected"' : '' ?>><?php _e("Orange","gravityformspolls") ?></option>
                    </select>
                </p>

                <div id="<?php echo $this->id ?>_gpoll_override_poll_settings" style="<?php  echo   $instance['mode'] == "results" ? "display:none" : "";  ?>">
                    <p>


                        <input type="checkbox" name="<?php echo $this->get_field_name( 'displayconfirmation' ); ?>" id="<?php echo $this->get_field_id( 'displayconfirmation' ); ?>" <?php checked($instance['displayconfirmation']); ?> value="1"/> <label for="<?php echo $this->get_field_id( 'displayconfirmation' ); ?>"><?php _e("Display form confirmation", "gravityformspolls"); ?></label><br/>

                        <input type="checkbox" name="<?php echo $this->get_field_name( 'display_results' ); ?>" id="<?php echo $this->get_field_id( 'display_results' ); ?>" <?php checked($instance['display_results']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'display_results' ); ?>"><?php _e("Display results of submitted poll fields", "gravityformspolls"); ?></label><br/>

                        <input type="checkbox" name="<?php echo $this->get_field_name( 'show_results_link' ); ?>" id="<?php echo $this->get_field_id( 'show_results_link' ); ?>" <?php checked($instance['show_results_link']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'show_results_link' ); ?>"><?php _e("Show link to view results", "gravityformspolls"); ?></label><br/>

                    </p>

                    <p>

                        <?php $cookie_expriation_div_id = $this->id . "_cookie_expriation" ?>
                        <strong><?php _e("Repeat Voters", "gravityformspolls"); ?>:</strong> <br />

                        <input type="radio" name="<?php echo $this->get_field_name( 'block_repeat_voters' ); ?>" value="0" <?php checked($instance['block_repeat_voters'], "0"); ?> id="<?php echo $this->get_field_id( 'block_repeat_voters' ) . "_0"; ?>" onclick="jQuery('#<?php echo $cookie_expriation_div_id ?>').hide('slow');"> <label for="<?php echo $this->get_field_id( 'block_repeat_voters' ) . "_0"; ?>"><?php _e("Don't block repeat voting", "gravityformspolls"); ?></label><br>
                        <input type="radio" name="<?php echo $this->get_field_name( 'block_repeat_voters' ); ?>" value="1" <?php checked($instance['block_repeat_voters'], "1"); ?> id="<?php echo $this->get_field_id( 'block_repeat_voters' ) . "_1"; ?>" onclick="jQuery('#<?php echo $cookie_expriation_div_id ?>').show('slow');"> <label for="<?php echo $this->get_field_id( 'block_repeat_voters' ) . "_1"; ?>"><?php _e("Block repeat voting using cookie", "gravityformspolls"); ?></label>
                    </p>
                    <div id="<?php echo $cookie_expriation_div_id?>" <?php echo $instance['block_repeat_voters'] == '0' ? 'style="display:none;"' : '' ?>>
                        <label for="<?php echo $this->get_field_id( 'cookie' ); ?>"><?php _e("Expires:", "gravityformspolls"); ?></label>
                        <select id="<?php echo $this->get_field_id( 'cookie' ); ?>" name="<?php echo $this->get_field_name( 'cookie' ); ?>" style="width:90%;">
                            <?php
                            $options = array(
                                "20 years"	=> __("Never","gravityformspolls"),
                                "1 hour" 	=> __("1 hour","gravityformspolls"),
                                "6 hours"	=> __("6 hours","gravityformspolls"),
                                "12 hours"	=> __("12 hours","gravityformspolls"),
                                "1 day"		=> __("1 day","gravityformspolls"),
                                "1 week"	=> __("1 week","gravityformspolls"),
                                "1 month"	=> __("1 month","gravityformspolls")
                            );
                            foreach ($options as $key => $value) {
                                $selected = '';
                                if ($key == rgar($instance, 'cookie'))
                                    $selected = ' selected="selected"';
                                echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                            }
                            ?>
                        </select>
                        <br /><br />
                    </div>

                </div>
            </div>
            <div id="<?php echo $this->id ?>_gpoll_form_settings" style="<?php  echo  $instance['mode'] == "results" ? "display:none" : ""; ?>">
                <p>
                    <input type="checkbox" name="<?php echo $this->get_field_name( 'showtitle' ); ?>" id="<?php echo $this->get_field_id( 'showtitle' ); ?>" <?php checked($instance['showtitle']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'showtitle' ); ?>"><?php _e("Display form title", "gravityformspolls"); ?></label><br/>
                    <input type="checkbox" name="<?php echo $this->get_field_name( 'showdescription' ); ?>" id="<?php echo $this->get_field_id( 'showdescription' ); ?>" <?php checked($instance['showdescription']); ?> value="1"/> <label for="<?php echo $this->get_field_id( 'showdescription' ); ?>"><?php _e("Display form description", "gravityformspolls"); ?></label><br/>
                </p>
                <p>
                    <a href="javascript: var obj = jQuery('.gf_widget_advanced'); if(!obj.is(':visible')) {var a = obj.show('slow');} else {var a = obj.hide('slow');}"><?php _e("advanced options", "gravityformspolls"); ?></a>
                </p>

                <p class="gf_widget_advanced" style="display:none;">
                    <input type="checkbox" name="<?php echo $this->get_field_name( 'ajax' ); ?>" id="<?php echo $this->get_field_id( 'ajax' ); ?>" <?php checked($instance['ajax']); ?> value="1"/> <label for="<?php echo $this->get_field_id( 'ajax' ); ?>"><?php _e("Enable AJAX", "gravityformspolls"); ?></label><br/>
                    <input type="checkbox" name="<?php echo $this->get_field_name( 'disable_scripts' ); ?>" id="<?php echo $this->get_field_id( 'disable_scripts' ); ?>" <?php checked($instance['disable_scripts']); ?> value="1"/> <label for="<?php echo $this->get_field_id( 'disable_scripts' ); ?>"><?php _e("Disable script output", "gravityformspolls"); ?></label><br/>
                    <label for="<?php echo $this->get_field_id( 'tabindex' ); ?>"><?php _e("Tab Index Start", "gravityformspolls"); ?>: </label>
                    <input id="<?php echo $this->get_field_id( 'tabindex' ); ?>" name="<?php echo $this->get_field_name( 'tabindex' ); ?>" value="<?php echo $instance['tabindex']; ?>" style="width:15%;" /><br/>
                    <small><?php _e("If you have other forms on the page (i.e. Comments Form), specify a higher tabindex start value so that your Gravity Form does not end up with the same tabindices as your other forms. To disable the tabindex, enter 0 (zero).", "gravityformspolls"); ?></small>
                </p>
            </div>


        <?php
        }
    }
}
