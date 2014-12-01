<?php
/**
 * Backend Class for use in all amr plugins
 * Version 0.1
 */
//------------------------------------------------------------------------------------------------------------------	
if (!class_exists('amrical_plugin_admin')) {
	class amrical_Plugin_Admin {
		var $hook 		= '';
		var $filename	= '';
		var $longname	= '';
		var $shortname	= '';
		var $optionname = '';
		var $homepage	= '';
		var $parent_slug = 'plugin_listings_menu';
		var $accesslvl	= 'manage_options';
		
		function amr_Plugin_Admin() {
			add_action('admin_menu', array(&$this, 'register_settings_page') );
			add_filter('plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );			
			add_action('admin_print_scripts', array(&$this,'config_page_scripts'));
			add_action('admin_print_styles', array(&$this,'config_page_styles'));				
//			add_action('wp_dashboard_setup', array(&$this,'widget_setup'));	
		}		
		
		function config_page_styles() {
			if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
				wp_enqueue_style('dashboard');
				wp_enqueue_style('thickbox');
				wp_enqueue_style('global');
				wp_enqueue_style('wp-admin');
				wp_enqueue_style('blogicons-admin-css', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)). '/amr_plugin_tools.css');
			}
		}
		function register_settings_page() {
			add_submenu_page( $this->parent_slug, $this->shortname, $this->shortname, $this->accesslvl, $this->hook, array(&$this,'config_page'));
		}		
		function plugin_options_url() {
			return admin_url( 'options-general.php?page='.$this->hook );
		}		
		/**
		 * Add a link to the settings page to the plugins list
		 */
		function add_action_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) $this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}		
		function config_page() {
			
		}		
		function config_page_scripts() {
			if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
				wp_enqueue_script('postbox');
				wp_enqueue_script('dashboard');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('media-upload');
			}
		}
		/**
		 * Create a Checkbox input field
		 */
		function radiobutton($id, $label, $value, $selected) {
			$sel = checked($value,$selected, false); 
			return "<input type='radio' id='".$id."' name='".$id."' value='".$value."'"
			. $sel."/>&nbsp;".$label."<br />";
		}	
		/**
		 * Create a Checkbox input field
		 */
		function checkbox($id, $label, $value) {
			return '<input type="checkbox" id="'.$id.'" name="'.$id.'"'. checked($value,true,false).'/>&nbsp;<label for="'.$id.'">'.$label.'</label><br/>';
		}
		/**
		 * Create a Dropdown input field
		 */
		function dropdown($id, $label, $options, $selected) {
//			
			$html = '<label for="'.$id.'">'.$label.':</label><br/>'
			.'<select id=\''.$id.'\' name=\''.$id.'\'>';
			foreach ($options as $i => $option) {
//				
				$sel = selected($i, $selected, false); //wordpress function returns with single quotes, not double 
				$html .= '<OPTION '.$sel.' label=\''.$option.'\' value=\''.$i.'\'>'.$option.'</OPTION>';
			}
			$html .= '</select>';
			return ($html);
		}			
		/**
		 * Create a Text input field
		 */
		function textinput($id, $label, $value, $length='45') {
			return '<label for="'.$id.'">'.$label.':</label><br/><input size="'
			.$length.'" type="text" id="'.$id.'" name="'.$id.'" value="'.$value.'"/><br/><br/>';
		}
				/**
		 * Create a Text area field
		 */
		function textarea($id, $label, $value, $cols='45', $rows='10') {
			return '<label for="'.$id.'">'.$label.':</label><br/>'
			.'<textarea rows="'.$rows.'" cols="'.$cols
			.'" id="'.$id.'" name="'.$id.'"/>'.$value.'</TEXTAREA><br/><br/>';
		}
		/**
		 * Create a postbox widget
		 */
		function postbox($id, $title, $content) {
		?>
			<div id="<?php echo $id; ?>" class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span><?php echo $title; ?></span></h3>
				<div class="inside">
					<?php echo $content; ?>
				</div>
			</div>
		<?php
		}	
		/**
		 * Create a form table from an array of rows
		 */
		function form_table($rows) { //  array of rows () id, label, desc, content 
			$content = '<table class="form-table">';
			foreach ($rows as $row) {
				$content .= '<tr><th valign="top" scrope="row">';
				if (isset($row['id']) && $row['id'] != '')
					$content .= '<label for="'.$row['id'].'">'.$row['label'].':</label>';
				else
					$content .= $row['label'];
				if (isset($row['desc']) && $row['desc'] != '')
					$content .= '<br/><small>'.$row['desc'].'</small>';
				$content .= '</th><td valign="top">';
				$content .= $row['content'];
				$content .= '</td></tr>'; 
			}
			$content .= '</table>';
			return $content;
		}
		/**
		 * Create a "plugin like" box.
		 */
		function plugin_like() {
			$content = '<p>'.__('Why not do any or all of the following:','amr-ical-events-list').'</p>';
			$content .= '<ul>';
			$content .= '<li><a href="'.$this->homepage.'">'.__('Link to it so other folks can find out about it.','amr-ical-events-list').'</a></li>';
			$content .= '</ul>';
			$this->postbox($this->hook.'like', 'Like this plugin?', $content);
		}			
		/**
		 * Info box with link to the support forums.
		 */
		function plugin_support() {
			$content = '<p>'.__('If you have any problems with this plugin or good ideas for improvements or new features, please talk about them in the','amr-ical-events-list').' <a href="http://wordpress.org/tags/'.$this->hook.'">'.__("Support forums",'amr-ical-events-list').'</a>.</p>';
			$this->postbox($this->hook.'support', 'Need support?', $content);
		}
		/**
		 * Box with latest news from amr.com
		 */
		function news() {
			require_once(ABSPATH.WPINC.'/rss.php');  
			if ( $rss = fetch_rss( 'http://icalevents.com/feed' ) ) {
				$content = '<ul>';
				$rss->items = array_slice( $rss->items, 0, 3 );
				foreach ( (array) $rss->items as $item ) {
					$content .= '<li class="amr">';
					$content .= '<a class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. htmlentities($item['title']) .'</a> ';
					$content .= '</li>';
				}
				$content .= '<li class="rss"><a href="http://icalevents.com/feed/">Subscribe with RSS</a></li>';
				$content .= '<li class="email"><a href="http://icalevents.com/">Subscribe by email</a></li>';
				$this->postbox('amrlatest', 'Latest news', $content);
			} else {
				$this->postbox('amrlatest', 'Latest news', 'Nothing to say...');
			}
		}
		function text_limit( $text, $limit, $finish = ' [&hellip;]') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}
	}
}
?>