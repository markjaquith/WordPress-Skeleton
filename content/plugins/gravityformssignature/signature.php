<?php
/*
Plugin Name: Gravity Forms Signature Add-On
Plugin URI: http://www.gravityforms.com
Description: Creates a Gravity Forms signature field that allows users to sign online using a mouse or stylus.
Version: 2.2
Author: Rocketgenius
Author URI: http://www.rocketgenius.com

------------------------------------------------------------------------
Copyright 2012 Rocketgenius, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

add_action('parse_request',  array('GFSignature', 'display_signature'));

if (class_exists("GFForms")) {
GFForms::include_addon_framework();

class GFSignature extends GFAddOn{

    protected $_version = "2.2";
    protected $_min_gravityforms_version = "1.8.3";
    protected $_slug = "gravityformssignature";
    protected $_path = "gravityformssignature/signature.php";
    protected $_full_path = __FILE__;
    protected $_url = "http://www.gravityforms.com";
    protected $_title = "Signature Add-On";
    protected $_short_title = "Signature";

    protected $_enable_rg_autoupgrade = true;

    private static $_instance = null;

    public static function get_instance() {
        if (self::$_instance == null) {
            self::$_instance = new GFSignature();
        }
        return self::$_instance;
    }

    private function __clone() { } /* do nothing */

    protected function scripts(){
        $scripts = array(
            array(  "handle" => "maskedinput",
                    "src" => GFCommon::get_base_url() . "/js/jquery.maskedinput-1.3.1.min.js",
                    "version" => GFCommon::$version,
                    "deps" => array("jquery"),
                    "enqueue" => array(
                                        array("admin_page" => array("form_editor") ),
                                        )
                ),
            array(
                "handle" => "super_signature_script",
                "src" => $this->get_base_url() . "/super_signature/ss.js",
                "version" => $this->_version,
                "deps" => array("jquery"),
                "enqueue" => array(
                                    array("field_types" => array("signature"))
                                    )
            )
        );

        return array_merge(parent::scripts(), $scripts);
    }

    protected function init_admin(){

        parent::init_admin();

        add_filter("gform_entry_field_value", array($this, "signature_entry_detail"), 10, 5);
        add_filter('gform_entries_field_value', array($this, "signature_entry_list"), 10, 3);
        add_filter("gform_add_field_buttons",array($this, 'add_signature_field'));
        add_action("gform_editor_js", array($this,'editor_script'));
        add_filter('gform_tooltips', array($this,'tooltips'));
        add_action("gform_field_standard_settings", array($this,'field_settings'), 10, 2);
        add_action("gform_field_advanced_settings", array($this,'advanced_field_settings'), 10, 2);
        add_filter("gform_field_type_title", array($this, "signature_title"), 10, 2);
        add_action("gform_editor_js_set_default_values", array($this, "signature_label"));
        add_action("gform_delete_lead", array($this, "delete_lead"));
        add_filter('gform_admin_pre_render', array($this, 'edit_lead_script'));

        //displays signature input
        add_action("gform_field_input", array($this, "signature_input"), 10, 5);

        //displays saved signature
        add_filter("gform_merge_tag_filter", array($this, "merge_tag_filter"), 10, 4);

        $this->maybe_save_signature();
    }

    protected function init_ajax(){

        parent::init_ajax();

        add_action('wp_ajax_gf_delete_signature', array($this, 'ajax_delete_signature'));

        //displays signature input
        add_action("gform_field_input", array($this, "signature_input"), 10, 5);
    }

    protected function init_frontend(){

        parent::init_frontend();

        //needed for print entry screen
        add_filter("gform_entry_field_value", array($this, "signature_entry_detail"), 10, 5);

        add_action("gform_pre_submission",array($this, "pre_submission_handler"));
        add_filter("gform_field_validation", array($this, "signature_validation"), 10, 4);

        //displays signature field
        add_action("gform_field_input", array($this, "signature_input"), 10, 5);

        //displays saved signature
        add_filter("gform_merge_tag_filter", array($this, "merge_tag_filter"), 10, 4);
    }

    private function maybe_save_signature(){

        //see if this is an entry and it needs to be updated. abort if not
        if (!(RG_CURRENT_VIEW == "entry" && rgpost("save") == "Update"))
            return;

        $lead_id = rgget("lid");
        $form = RGFormsModel::get_form_meta(rgget("id"));
        if(empty($lead_id)) {
	        //lid is not always in the querystring when paging through entries, use same logic from entry detail page
	        $filter = rgget("filter");
        	$status = in_array($filter, array("trash", "spam")) ? $filter : "active";
        	$search = rgget("s");
	        $position = rgget('pos') ? rgget('pos') : 0;
	        $sort_direction = rgget('dir') ? rgget('dir') : 'DESC';

	        $sort_field = empty($_GET["sort"]) ? 0 : $_GET["sort"];
	        $sort_field_meta = RGFormsModel::get_field($form, $sort_field);
	        $is_numeric = $sort_field_meta["type"] == "number";

	        $star = $filter == "star" ? 1 : null;
	        $read = $filter == "unread" ? 0 : null;

	        $leads = RGFormsModel::get_leads(rgget("id"), $sort_field, $sort_direction, $search, $position, 1, $star, $read, $is_numeric, null, null, $status);

	        if(!$lead_id) {
	            $lead = !empty($leads) ? $leads[0] : false;
	        }
	        else {
	            $lead = RGFormsModel::get_lead($lead_id);
	        }

	        if(!$lead) {
	            _e("Oops! We couldn't find your lead. Please try again", "gravityforms");
	            return;
        	}
        	$lead_id = $lead["id"];
		}

        //get lead and form
        $lead = RGFormsModel::get_lead($lead_id);

        //loop through form fields, get the field name of the signature field
        foreach($form["fields"] as $field){
            if (RGFormsModel::get_input_type($field) == "signature")
            {
                //get field name so the value can be pulled from the post data
                $input_name = "input_" . str_replace('.', '_', $field["id"]);

                //when adding a new signature the data field will be populated
                if (!rgempty("{$input_name}_data")){
                    //new image added, save
                    $filename =  $this->save_signature($input_name . "_data");
                }
                else{
                    //existing image edited
                    $filename = rgpost($input_name. "_signature_filename");
                }
                $_POST["input_{$field["id"]}"] = $filename;

            }
        }

    }

    public function signature_title($title, $field_type){
        if($field_type == "signature")
            $title = __("Signature", "gravityformssignature");

        return $title;
    }

    public function signature_label(){
        //setting default field label
        ?>
        case "signature" :
            field.label = "<?php _e("Signature", "gravityformssignature") ?>";
        break;
        <?php
    }

    //Adds feed tooltips to the list of tooltips
    public function tooltips($tooltips){
        $signature_tooltips = array(
            "signature_background_color" => "<h6>" . __("Background Color", "gravityformssignature") . "</h6>" . __("Select the color to be used for the background of the signature area", "gravityformssignature"),
            "signature_border_color" => "<h6>" . __("Border Color", "gravityformssignature") . "</h6>" . __("Select the color to be used for the border around the signature area", "gravityformssignature"),
            "signature_pen_color" => "<h6>" . __("Pen Color", "gravityformssignature") . "</h6>" . __("Select the color of the pen to be used for the signature", "gravityformssignature"),
            "signature_box_width" => "<h6>" . __("Width", "gravityformssignature") . "</h6>" . __("Enter the width for the signature area in pixels.", "gravityformssignature"),
            "signature_border_style" => "<h6>" . __("Border Style", "gravityformssignature") . "</h6>" . __("Select the border style to be used around the signature area", "gravityformssignature"),
            "signature_pen_size" => "<h6>" . __("Pen Size", "gravityformssignature") . "</h6>" . __("Select the width of the pen to be used for the signature", "gravityformssignature"),
            "signature_border_width" => "<h6>" . __("Border Width", "gravityformssignature") . "</h6>" . __("Select the border width to be used around the signature area", "gravityformssignature"),
            "signature_message" => "<h6>" . __("Message", "gravityformssignature") . "</h6>" . __("Write the message you would like to be sent. You can insert fields submitted by the user by selecting them from the 'Insert Variable' drop down.", "gravityformssignature")
        );
        return array_merge($tooltips, $signature_tooltips);
    }

    public function advanced_field_settings($position, $form_id){
        if($position == 50){
            ?>
            <li class="box_width_setting field_setting">
                <label for="field_signature_box_width">
                    <?php _e("Field Width", "gravityformssignature"); ?>
                    <?php gform_tooltip("signature_box_width") ?>
                </label>
                <input id="field_signature_box_width" type="text" style="width:40px" onkeyup="SetSignatureBoxWidth(jQuery(this).val());" onchange="SetSignatureBoxWidth(jQuery(this).val());"/> px
            </li>
            <?php
        }
    }

    public function field_settings($position, $form_id){

        //create settings on position 25 (right after Field Label)
        if($position == 25){
            ?>
            <li class="background_color_setting field_setting gform_setting_left_half">
                <label for="field_signature_background_color">
                    <?php _e("Background Color", "gravityformssignature"); ?>
                    <?php gform_tooltip("signature_background_color") ?>
                </label>
                <?php GFFormDetail::color_picker("field_signature_background_color", "SetSignatureBackColor") ?>
            </li>
            <li class="border_color_setting field_setting gform_setting_right_half">
                <label for="field_signature_border_color">
                    <?php _e("Border Color", "gravityformssignature"); ?>
                    <?php gform_tooltip("signature_border_color") ?>
                </label>
                <?php GFFormDetail::color_picker("field_signature_border_color", "SetSignatureBorderColor") ?>
            </li>
            <li class="border_width_setting field_setting gform_setting_left_half">
                <label for="field_signature_border_width">
                    <?php _e("Border Width", "gravityformssignature"); ?>
                    <?php gform_tooltip("signature_border_width") ?>
                </label>
                <select id="field_signature_border_width"  onchange="SetSignatureBorderWidth(jQuery(this).val());">
                    <option value="0">None</option>
                    <option value="1">Small</option>
                    <option value="2">Medium</option>
                    <option value="3">Large</option>
                </select>
            </li>
            <li class="border_style_setting field_setting gform_setting_right_half">
                <label for="field_signature_border_style">
                    <?php _e("Border Style", "gravityformssignature"); ?>
                    <?php gform_tooltip("signature_border_style") ?>
                </label>
                <select id="field_signature_border_style"  onchange="SetSignatureBorderStyle(jQuery(this).val());">
                    <option>Dotted</option>
                    <option>Dashed</option>
                    <option>Groove</option>
                    <option>Ridge</option>
                    <option>Inset</option>
                    <option>Outset</option>
                    <option>Double</option>
                    <option>Solid</option>
                </select>
            </li>

            <li class="pen_color_setting field_setting gform_setting_left_half">
                <label for="field_signature_pen_color">
                    <?php _e("Pen Color", "gravityformssignature"); ?>
                    <?php gform_tooltip("signature_pen_color") ?>
                </label>
                <?php GFFormDetail::color_picker("field_signature_pen_color", "SetSignaturePenColor") ?>
            </li>
            <li class="pen_size_setting field_setting gform_setting_right_half">
                <label for="field_signature_pen_size">
                    <?php _e("Pen Size", "gravityformssignature"); ?>
                    <?php gform_tooltip("signature_pen_size") ?>
                </label>
                <select id="field_signature_pen_size"  onchange="SetSignaturePenSize(jQuery(this).val());">
                    <option value="1">Small</option>
                    <option value="2">Medium</option>
                    <option value="3">Large</option>
                </select>
            </li>

            <?php
        }
    }

    public function editor_script(){
        ?>
        <script type='text/javascript'>

            //adding setting to signature fields
            fieldSettings["signature"] = ".pen_size_setting, .border_width_setting, .border_style_setting, .box_width_setting, .pen_color_setting, .border_color_setting, .background_color_setting, .conditional_logic_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .rules_setting, .visibility_setting, .description_setting, .css_class_setting";

            //binding to the load field settings event to initialize the checkbox
            jQuery(document).bind("gform_load_field_settings", function(event, field, form){

                var backColor = field.backgroundColor == undefined ? "" :  field.backgroundColor;
                jQuery("#field_signature_background_color").val(backColor);
                SetColorPickerColor("field_signature_background_color", backColor);

                var borderColor = field.borderColor == undefined ? "" :  field.borderColor;
                jQuery("#field_signature_border_color").val(borderColor);
                SetColorPickerColor("field_signature_border_color", borderColor);

                var penColor = field.penColor == undefined ? "" :  field.penColor;
                jQuery("#field_signature_pen_color").val(penColor);
                SetColorPickerColor("field_signature_pen_color", penColor);

                var boxWidth = field.boxWidth == undefined || field.boxWidth.trim().length == 0 ? "300" :  field.boxWidth;
                jQuery("#field_signature_box_width").val(boxWidth);

                var borderStyle = field.borderStyle == undefined ? "" :  field.borderStyle;
                jQuery("#field_signature_border_style").val(borderStyle);

                var borderWidth = field.borderWidth == undefined ? "" :  field.borderWidth;
                jQuery("#field_signature_border_width").val(borderWidth);

                var penSize = field.penSize == undefined ? "" :  field.penSize;
                jQuery("#field_signature_pen_size").val(penSize);

            });

            jQuery(document).ready(function(){
                jQuery('#field_signature_box_width').mask('?9999', {placeholder:' '});
            });

            function SetSignatureBackColor(color){
                //saving selected color
                SetFieldProperty("backgroundColor", color);

                //updating UI to match new color
                jQuery(".field_selected .gf_signature_container").css("background-color", color);
            }

            function SetSignatureBorderColor(color){
                //saving selected color
                SetFieldProperty("borderColor", color);

                //updating UI to match new color
                jQuery(".field_selected .gf_signature_container").css("border-color", color);
            }

            function SetSignaturePenColor(color){
                //saving selected color
                SetFieldProperty("penColor", color);
            }

            function SetSignatureBoxWidth(size){
                //saving selected box width
                SetFieldProperty("boxWidth", size);
            }

            function SetSignatureBorderStyle(style){
                //saving selected color
                SetFieldProperty("borderStyle", style);

                //updating UI to match new color
                jQuery(".field_selected .gf_signature_container").css("border-style", style);
            }

            function SetSignatureBorderWidth(size){
                //saving selected border width
                SetFieldProperty("borderWidth", size);

                //updating UI to match border width
                jQuery(".field_selected .gf_signature_container").css("border-width", size + "px");
            }

            function SetSignaturePenSize(size){
                //saving selected pen size
                SetFieldProperty("penSize", size);
            }

        </script>
        <?php
    }

    public function merge_tag_filter($value, $merge_tag, $options, $field){
        if($merge_tag == "all_fields" && $field["type"] == "signature"){
            return false;
        }
        else if($merge_tag == $field["id"] && $field["type"] == "signature")
        {
            $path_info = pathinfo($value);
            $filename = $path_info["filename"];
            $signature_path = home_url()."?page=gf_signature&signature={$filename}";
            $newvalue= $signature_path;
            return $newvalue;
        }
        else
            return $value;
    }

    public function signature_entry_list($value, $form_id, $field_id){
        $form = RGFormsModel::get_form_meta($form_id);
        $field = RGFormsModel::get_field($form, $field_id);
        if($field["type"]=="signature" && !empty($value))
        {
            $path_info = pathinfo($value);
            $filename = $path_info["filename"];
            $signature_path = home_url()."?page=gf_signature&signature={$filename}";
            $thumb = GFCommon::get_base_url() . "/images/doctypes/icon_image.gif";
            $newvalue="<a href='$signature_path' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
            return $newvalue;
        }
        else
        {
            return $value;
        }
    }

    public function get_signature_url($filename){
        $path_info = pathinfo($filename);
        $filename = $path_info["filename"];
        return home_url()."?page=gf_signature&signature={$filename}";
    }

    public function signature_entry_detail($value, $field, $lead, $form){
        if($field["type"]=="signature" && !empty($value))
        {
            $signature_path = $this->get_signature_url($value);
            $newvalue="<a href='$signature_path' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img width='100' src='$signature_path'></a>";
            return $newvalue;
        }
        else
        {
            return $value;
        }
    }

    public static function display_signature($wp){
        $is_signature = rgget("page") == "gf_signature";
        if(!$is_signature)
            return;

        $imagename = rgget("signature") . ".png";

        $folder = RGFormsModel::get_upload_root() . "signatures/";

        if(!is_file($folder . $imagename))
            exit();

        $image = imagecreatefrompng($folder . $imagename);

        if(ob_get_length() > 0)
            ob_clean();

        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);

        exit();
    }

    public function add_signature_field($field_groups){
        foreach($field_groups as &$group){
            if($group["name"] == "advanced_fields"){
                $group["fields"][] = array("class"=>"button", "value" => __("Signature", "gravityforms"), "onclick" => "StartAddField('signature');");
                break;
            }
        }
        return $field_groups;
    }

    public function signature_input($input, $field, $value, $lead_id, $form_id){

        if($field['type'] != 'signature')
            return $input;

        $unique_id = IS_ADMIN || $form_id == 0 ? "input_{$field["id"]}" : "input_" . $form_id . "_{$field["id"]}";

        $supports_canvas = true;

        require_once('super_signature/Browser.php');
        $browser = new Browser();
        if($browser->getBrowser() == Browser::BROWSER_IE)
            $supports_canvas = $browser->getVersion() >= 9;

        $bgcolor = rgempty("backgroundColor", $field) ? "#FFFFFF" : rgar($field, "backgroundColor");
        $bordercolor = rgempty("borderColor", $field) ? "#DDDDDD" : rgar($field, "borderColor");
        $pencolor = rgempty("penColor", $field) ? "#000000" : rgar($field, "penColor");
        $boxwidth = rgblank(rgget("boxWidth", $field)) ? "300" : rgar($field, "boxWidth");
        $borderstyle = rgempty("borderStyle", $field) ? "Dashed" : rgar($field, "borderStyle");
        $borderwidth = rgblank(rgget("borderWidth", $field)) ? "2" : rgar($field, "borderWidth");
        $pensize = rgblank(rgget("penSize", $field)) ? "2" : rgar($field, "penSize");

        if(RG_CURRENT_VIEW <> "entry" && is_admin()){

            //box width is hardcoded in the admin
            $input = "<style>" .
                        ".top_label .gf_signature_container {width: 460px;} ".
                        ".left_label .gf_signature_container, .right_label .gf_signature_container {width: 300px;} ".
                    "</style>".
                    "<div style='display:-moz-inline-stack; display: inline-block; zoom: 1; *display: inline;'><div class='gf_signature_container' style='height:180px; border: {$borderwidth}px {$borderstyle} {$bordercolor}; background-color:{$bgcolor};'></div></div>";

        // if frontend OR entry detail
        } else {

            $input = "";
            if (RG_CURRENT_VIEW == "entry")
            {
                //include super signature script when viewing the entry in the admin
                $input = "<script src='" . $this->get_base_url()  . "/super_signature/ss.js?ver=" . $this->_version . "' type='text/javascript'></script>";
            }

            $signature_filename = !empty($value) ? $value : rgpost("{$unique_id}_signature_filename");
            if(!empty($signature_filename)){
                $input .= "<div id='{$unique_id}_signature_image'>".
                            "<img src='" . $this->get_signature_url($signature_filename) . "' width='{$boxwidth}px'/>".
                            "   <div>";
                if (RG_CURRENT_VIEW == "entry" && $value){
                    //include the links to download/delete image
                    $input .= "       <a href='" . $this->get_signature_url($signature_filename) . "' target='_blank' alt='" . __("Download file", "gravityforms") . "' title='" . __("Download file", "gravityforms") . "'><img src='" . GFCommon::get_base_url() . "/images/download.png" . "' /></a>".
                              "       <a href='javascript:void(0);' alt='" . __("Delete file", "gravityforms") . "' title='" . __("Delete file", "gravityforms") . "' onclick='deleteSignature(" . $lead_id . ", " . $field['id'] . ");' ><img src='" . GFCommon::get_base_url() . "/images/delete.png" . "' style='margin-left:8px;'/></a>";
                }
                else{
                     $input .= "       <a href='#' onclick='jQuery(\"#{$unique_id}_signature_filename\").val(\"\"); jQuery(\"#{$unique_id}_signature_image\").hide(); jQuery(\"#{$unique_id}_Container\").show(); jQuery(\"#{$unique_id}_resetbutton\").show(); return false;'>" . __("sign again", "gravityformssignature") . "</a>";
                }
                $input .= "   </div>" .
                         "</div>" .
                         "<input type='hidden' value='{$signature_filename}' name='{$unique_id}_signature_filename' id='{$unique_id}_signature_filename'/>".
                         "<style type='text/css'>#{$unique_id}_resetbutton {display:none}</style>";
            }

            $display = !empty($signature_filename) ? "display:none;" : "";

            $form = RGFormsModel::get_form_meta($form_id);

            $container_style = $form["labelPlacement"] == "top_label" ? "" : "style='display:-moz-inline-stack; display: inline-block; zoom: 1; *display: inline;'";

            $input .= "<div {$container_style}>
                        <div id='{$unique_id}_Container' style='height:180px; width: {$boxwidth}px; {$display}' >
                            <input type='hidden' class='gform_hidden' name='{$unique_id}_valid' id='{$unique_id}_valid' />";

            if ($supports_canvas){
                $input .= "<canvas id='{$unique_id}' width='{$boxwidth}' height='180'></canvas>";
            }
            else{
                $input .= "<div id='{$unique_id}' style='width:{$boxwidth}px; height:180px; border:{$borderstyle} {$borderwidth}px {$bordercolor}; background-color:{$bgcolor};'></div>";
            }

            $input .= "
                        </div>
                      </div>
                      <script type='text/javascript'>" .
                            "if(typeof SuperSignature != 'undefined'){".
                                "var obj{$unique_id} = new SuperSignature({IeModalFix: false, SignObject:'{$unique_id}',BackColor: '{$bgcolor}', PenSize: '{$pensize}', PenColor: '{$pencolor}',SignWidth: '{$boxwidth}',SignHeight: '180' ,BorderStyle:'{$borderstyle}',BorderWidth: '{$borderwidth}px',BorderColor: '{$bordercolor}', RequiredPoints: '15',ClearImage:'" . $this->get_base_url() . "/super_signature/refresh.png', PenCursor:'" . $this->get_base_url() . "/super_signature/pen.cur', Visible: 'true', ErrorMessage: '', StartMessage: '', SuccessMessage: ''});".
                                "obj{$unique_id}.Init();".

                                "jQuery('#gform_{$form_id}').submit(function(){".
                                "       jQuery('#{$unique_id}_valid').val(obj{$unique_id}.IsValid() || jQuery('#{$unique_id}_signature_filename').val() ? '1' : '');".
                                "});".
                            "}".
                      "</script>";
        }

        return $input;
    }

    public function edit_lead_script($form) {
        ?>

        <script type="text/javascript">
        function deleteSignature(leadId, fieldId) {

            if(!confirm(<?php _e("'Would you like to delete this file? \'Cancel\' to stop. \'OK\' to delete'", 'gravityformssignature'); ?>))
                return;

            jQuery.post(ajaxurl, {
                lead_id: leadId,
                field_id: fieldId,
                action: 'gf_delete_signature',
                gf_delete_signature: '<?php echo wp_create_nonce('gf_delete_signature') ?>'
            }, function(response){
                if(!response)
                    //jQuery('#input_' + fieldId).val('');
                    jQuery('#input_' + fieldId + '_signature_filename').val('');
                    jQuery('#input_' + fieldId + '_signature_image').hide();
                    jQuery('#input_' + fieldId + '_Container').show();
                    jQuery('#input_' + fieldId + '_resetbutton').show();
            });
        }
        </script>

        <?php
        return $form;
    }

    public function signature_validation($result, $value, $form, $field){
        if($field["type"] != "signature")
            return $result;

        $unique_id = "input_" . $form["id"] . "_{$field["id"]}";
        $is_invalid = $field["isRequired"] && rgempty("{$unique_id}_valid");

        $result["is_valid"] = !$is_invalid;
        $error_message = rgar($field, "errorMessage") ? rgar($field, "errorMessage") : __("Please enter your signature.", "gravityformssignature");
        $result["message"] = $is_invalid ? $error_message : "";

        //create image if signature is valid
        if(!$is_invalid){
            $filename = $this->save_signature($unique_id . "_data", "temp_");
            $_POST["input_{$field["id"]}"] = $filename;
        }

        return $result;
    }

    public function pre_submission_handler($form){

        $signature_fields = GFCommon::get_fields_by_type($form, array("signature"));
        foreach($signature_fields as $field){
            $input_name = "input_{$field["id"]}";
            $filename = !rgempty($input_name) ? rgpost($input_name) : rgpost("input_{$form["id"]}_{$field["id"]}_signature_filename");
            if(empty($filename)){
                //create signature file
                $filename = $this->save_signature("input_{$form["id"]}_{$field["id"]}_data");
                $_POST["input_{$field["id"]}"] = $filename;
            }
            else{
                //rename signature file
                $new_name = str_replace("temp_", "", $filename);
                $folder = RGFormsModel::get_upload_root() . "/signatures/";

                rename($folder . $filename, $folder . $new_name);
                $_POST["input_{$field["id"]}"] = $new_name;
            }
        }
    }

    public function save_signature($input_name, $name_prefix=""){
        require_once("super_signature/license.php");

        $signature_data = rgpost($input_name);

        $image = GetSignatureImage($signature_data);
        if(!$image)
            return "";

        $folder = RGFormsModel::get_upload_root() . "/signatures/";

        //abort if folder can't be created.
        if(!wp_mkdir_p($folder))
            return;

        $filename = $name_prefix . uniqid("", true) . ".png";
        $path = $folder . $filename;
        imagepng($image, $path, 4);
        imagedestroy($image);

        return $filename;
    }

    public function delete_lead($lead_id){
        $lead = RGFormsModel::get_lead($lead_id);
        $form = RGFormsModel::get_form_meta($lead["form_id"]);

        if(!is_array($form["fields"]))
            return;

        foreach($form["fields"] as $field){
            if($field["type"] == "signature")
                $this->delete_signature($lead, $field['id']);
        }

    }

    public function ajax_delete_signature(){

        check_ajax_referer('gf_delete_signature', 'gf_delete_signature');

        $lead_id =  intval($_POST['lead_id']);
        $field_id =  intval($_POST['field_id']);

        if(!$this->delete_signature($lead_id, $field_id))
            _e('There was an issue deleting this signature.', 'gravityformssignature');

        die();
    }

    public function delete_signature($lead_id, $field_id) {
        global $wpdb;

        $lead = RGFormsModel::get_lead($lead_id);
        $form = RGFormsModel::get_form_meta($lead['form_id']);

        $this->delete_signature_file(rgar($lead, $field_id));

        $lead_detail_table = RGFormsModel::get_lead_details_table_name();
        $sql = $wpdb->prepare("UPDATE $lead_detail_table SET value = '' WHERE lead_id=%d AND field_number BETWEEN %s AND %s", $lead_id, doubleval($field_id) - 0.001, doubleval($field_id) + 0.001);
        return $wpdb->query($sql);

    }

    public function delete_signature_file($filename) {

        $folder = RGFormsModel::get_upload_root() . "/signatures/";
        $file_path = $folder . $filename;

        if(file_exists($file_path))
            unlink($file_path);

    }

}

GFSignature::get_instance();

}