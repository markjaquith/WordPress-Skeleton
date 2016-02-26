<?php

class MPP_Admin_Edit_Gallery_Panel {
	
	private $tabs = array();

	private static $instance = null;
	
	private function __construct() {
		
	}
	
	/**
	 * 
	 * @return MPP_Admin_Edit_Gallery_Panel
	 */
	public static function get_instance() {
		
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
		
	}
	
	
	public function render() {
		?>
		<div id="mpp-admin-edit-panels">
			
			<?php $this->render_nav(); ?>
			<?php $this->render_panels(); ?>
			<?php wp_nonce_field( 'mpp-manage-gallery', '_mpp_manage_gallery_nonce' ) ;?>
		</div>
	<?php
	}
	
	
	/**
	 * Add a panel
	 * 
	 * @param array $args 
	 *	@type string id unique panel id
	 *	@type callable $callback used to display pane
	 *	@type string $title title to display
	 *    
	 * @return type
	 */
	public function add_panel( $args ) {
		
		if( empty( $args['id'] ) || empty( $args['title'] ) || empty( $args['callback'] ) ) {
			return ;
		}
		
		$this->tabs[ $args['id'] ] = $args;
	}
	/**
	 * Render nav
	 */
	private function render_nav() { 
		$class = 'mpp-admin-edit-panel-tabs';
		?>
			<style type="text/css">
				
				.mpp-clearfix {
				  *zoom: 1;
				}

				.mpp-clearfix:before,
				.mpp-clearfix:after {
				  display: table;
				  line-height: 0;
				  content: "";
				}

				.mpp-clearfix:after {
				  clear: both;
				}
				.mpp-admin-edit-panel{ display:none; }
				.mpp-admin-active-panel{ display:block; }
				.mpp-admin-edit-panel:first{ display: block;}
				#mpp-admin-edit-panel-tabs-nav {
					border-bottom: 1px solid #cecece;
					list-style: outside none none;
					margin: 0 0 10px;
					padding: 0;
				}
				#mpp-admin-edit-panel-tabs-nav li {
					float: left;
					margin: 0 10px -1px 0;
				}
				#mpp-admin-edit-panel-tabs-nav li a {
					color: #aaa;
					display: block;
					font-size: 14px;
					font-weight: 300;
					outline: medium none;
					padding: 7px 10px 5px;
					text-decoration: none;
				}
				#mpp-admin-edit-panel-tabs-nav li.mpp-admin-edit-panel-tab-active a {
					-moz-border-bottom-colors: none;
					-moz-border-left-colors: none;
					-moz-border-right-colors: none;
					-moz-border-top-colors: none;
					border-color: #cecece #cecece #fff;
					border-image: none;
					border-radius: 3px 3px 0 0;
					border-style: solid;
					border-width: 1px;
					color: #21759b;
					padding-top: 6px;
				}
				
			</style>
            <ul id="mpp-admin-edit-panel-tabs-nav" class="mpp-clearfix">
				<?php foreach ( $this->tabs as $tab ) : ?>   
					<li class="<?php echo $class; ?>"><a href="#mpp-admin-edit-panel-tab-<?php echo $tab['id']; ?>" title="<?php echo $tab['title']; ?>"><?php echo $tab['title']; ?></a></li>
				<?php endforeach; ?>
            </ul>
		
			
		<?php 
	}
	
	private function render_panels() {
		
		?>
		<?php foreach ( $this->tabs as $tab ) : ?> 
			<div id="mpp-admin-edit-panel-tab-<?php echo $tab['id']; ?>" class="mpp-admin-edit-panel mpp-clearfix">
				<?php do_action( 'mpp_admin_edit_panel_before_tab_' . $tab['id'] ); ?>
				
					<?php call_user_func( $tab['callback'] );?>
				
				<?php do_action( 'mpp_admin_edit_panel_after_tab_' . $tab['id'] ); ?>
			</div>

		<?php endforeach; ?>
		<?php 
				$this->script();
	}
/**
     * Tabbable JavaScript codes
     *
     * This code uses localstorage for displaying active tabs
     */
    public function script() {
        ?>
        <script>
            jQuery(document).ready(function($) {
				
				 $('.mpp-admin-edit-panel:first').addClass('mpp-admin-active-panel');
                // Switches option sections
                $('.mpp-admin-edit-panel').not('.mpp-admin-active-panel').hide();
                
                //always show the first tab
                //$('.mpp-admin-edit-panel:first').fadeIn();
               
               

                $('#mpp-admin-edit-panel-tabs-nav li:first').addClass('mpp-admin-edit-panel-tab-active');
               
                //on click of the tab navigation
                $('#mpp-admin-edit-panel-tabs-nav a').click(function(evt) {
                    $li = $(this).parent();
					$('#mpp-admin-edit-panel-tabs-nav li').removeClass('mpp-admin-edit-panel-tab-active');
                    
					$li.addClass('mpp-admin-edit-panel-tab-active').blur();
					
                    var clicked_group = $(this).attr('href');
                    $('.mpp-admin-edit-panel').hide();
                    $('.mpp-admin-edit-panel').removeClass( 'mpp-admin-active-panel' );
                    $(clicked_group).fadeIn();
                    $(clicked_group).addClass('mpp-admin-active-panel');
					
                    evt.preventDefault();
                });
            });
        </script>
        <?php
    }
}
/**
 * 
 * @return MPP_Admin_Edit_Gallery_Panel
 */
function mpp_admin_edit_gallery_panel_helper() {
	return MPP_Admin_Edit_Gallery_Panel::get_instance();
}