<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_display_tabs(){
	global $ninja_forms_tabs;
	$current_tab = ninja_forms_get_current_tab();
	$current_page = esc_html( $_REQUEST['page'] );
	$opt = nf_get_settings();
	if(isset($_REQUEST['form_id'])){
		$form_id = absint( $_REQUEST['form_id'] );
	}else{
		$form_id = '';
	}
	if(isset($ninja_forms_tabs[$current_page]) AND is_array($ninja_forms_tabs[$current_page])){
		foreach($ninja_forms_tabs[$current_page] as $slug => $tab){
			if((isset($opt['screen_options']['tab'][$slug]['visible']) AND $opt['screen_options']['tab'][$slug]['visible'] == 1) OR !isset($opt['screen_options']['tab'][$slug]['visible'])){
				if($tab['add_form_id'] == 1){
					$link = remove_query_arg( array( 'update_message','notification-action' ) );
					if($form_id != ''){
						$link = esc_url( add_query_arg( array( 'tab' => $slug, 'form_id' => $form_id ), $link ) );
					}else{
						$link = esc_url( add_query_arg( array( 'tab' => $slug ), $link ) );
					}
				}else{
					$link = esc_url( remove_query_arg( array( 'form_id', 'tab', 'update_message' ) ) );
					$link = esc_url( add_query_arg( array( 'tab' => $slug ), $link ) );
				}

				if($tab['disable_no_form_id'] AND ($form_id == '' OR $form_id == 'new')){
					$link = '';
				}

				if( isset( $tab['url'] ) ){
					$link = $tab['url'];
				}

				if( isset( $tab['target'] ) ){
					$target = $tab['target'];
				}else{
					$target = '';
				}

				if($tab['show_this_tab_link']){
					if($current_tab == $slug){
						?>
							<span class="nav-tab nav-tab-active <?php echo $tab['active_class'];?>"><?php echo $tab['name'];?></span>
						<?php
					}else{
						?>
							<a href="<?php echo $link;?>" target="<?php echo $target;?>" class="nav-tab <?php echo $tab['inactive_class'];?>"><?php echo $tab['name'];?></a>
						<?php
					}
				}
			}
		}
	}
}
?>