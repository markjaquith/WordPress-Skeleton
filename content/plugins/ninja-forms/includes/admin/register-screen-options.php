<?php if ( ! defined( 'ABSPATH' ) ) exit;
$args = array(
	'display_function' => 'ninja_forms_screen_option_tabs',
	'save_function' => 'ninja_forms_save_screen_option_tabs',
	'page' => 'ninja-forms'
);
//ninja_forms_register_screen_option('tabs', $args);

function ninja_forms_screen_option_tabs(){
	global $ninja_forms_tabs, $ninja_forms_sidebars;
	$current_tab = ninja_forms_get_current_tab();
	$current_page = $_REQUEST['page'];
	$opt = nf_get_settings();
	if(isset($ninja_forms_tabs[$current_page]) AND is_array($ninja_forms_tabs[$current_page])){
		?>
		<div class="tabs-prefs">
			<h5>Show These Tabs</h5>
			<?php
			foreach($ninja_forms_tabs[$current_page] as $slug => $tab){
				if(!isset($opt['screen_options']['tab'][$slug]['visible']) OR $opt['screen_options']['tab'][$slug]['visible'] == 1){
					$checked = 'checked = "checked"';
				}else{
					$checked = '';
				}
			?>
			<input type="hidden" name="ninja-forms-tab[<?php echo $slug;?>]" value="0">
			<label for="ninja-forms-tab-<?php echo $slug;?>"><input class="hide-tab-tog" name="ninja-forms-tab[<?php echo $slug;?>]" type="checkbox" id="ninja-forms-tab-<?php echo $slug;?>" value="1" <?php echo $checked;?> ><?php echo $tab['name'];?></label>
			<?php
			}
			?>
			<br class="clear">
		</div>
		<?php if(isset($ninja_forms_sidebars[$current_page][$current_tab]) AND is_array($ninja_forms_sidebars[$current_page][$current_tab])){?>
			<div class="sidebar-prefs">
				<h5>Show These Sidebars</h5>
				<?php
				foreach($ninja_forms_sidebars[$current_page][$current_tab] as $slug => $sidebar){
					if(!isset($opt['screen_options']['tab'][$current_tab]['sidebars'][$slug]['visible']) OR $opt['screen_options']['tab'][$current_tab]['sidebars'][$slug]['visible'] == 1){
						$checked = 'checked = "checked"';
					}else{
						$checked = '';
					}
				?>
				<input type="hidden" name="ninja-forms-sidebar[<?php echo $slug;?>]" value="0">
				<label for="ninja-forms-sidebar-<?php echo $slug;?>"><input class="hide-sidebar-tog" name="ninja-forms-sidebar[<?php echo $slug;?>]" type="checkbox" id="ninja-forms-sidebar-<?php echo $slug;?>" value="1" <?php echo $checked;?> ><?php echo $sidebar['name'];?></label>
				<?php
				}
				?>
				<br class="clear">
			</div>
		<?php
		}
	}
}

function ninja_forms_save_screen_option_tabs(){
	$current_tab = ninja_forms_get_current_tab();
	$current_page = $_REQUEST['page'];
	$opt = nf_get_settings();
	if(is_array($_POST['ninja-forms-tab'])){
		foreach($_POST['ninja-forms-tab'] as $slug => $val){
			$opt['screen_options']['tab'][$slug]['visible'] = $val;
		}
	}
	if(is_array($_POST['ninja-forms-sidebar'])){
		foreach($_POST['ninja-forms-sidebar'] as $slug => $val){
			$opt['screen_options']['tab'][$current_tab]['sidebars'][$slug]['visible'] = $val;
		}
	}
	update_option('ninja_forms_settings', $opt);
}