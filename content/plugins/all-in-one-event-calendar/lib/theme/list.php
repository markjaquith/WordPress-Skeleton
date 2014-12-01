<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Extends WP_List_Table to list our calerndar themes.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Theme
 */
class Ai1ec_Theme_List extends WP_List_Table {

	/**
	 * @var array List of search terms
	 */
	public $search = array();

	/**
	 * @var array List of features
	 */
	public $features = array();

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * Constructor
	 *
	 * Overriding constructor to allow inhibiting parents startup sequence.
	 * If in some wild case you need to inhibit startup sequence of parent
	 * class - pass `array( 'inhibit' => true )` as argument to this one.
	 *
	 * @param array $args Options to pass to parent constructor
	 *
	 * @return void Constructor does not return
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		$args = array()
	) {
		$this->_registry = $registry;
		if ( ! isset( $args['inhibit'] ) ) {
			parent::__construct( $args );
		}
	}

	/**
	 * prepare_items function
	 *
	 * Prepares themes for display, applies search filters if available
	 *
	 * @return void
	 **/
	public function prepare_items() {
		global $ct;

		// setting wp_themes to null in case
		// other plugins have changed its value
		unset( $GLOBALS['wp_themes'] );

		// get available themes
		$ct     = $this->current_theme_info();

		$themes = $this->_registry->get( 'theme.search' )
			->filter_themes();

		if ( isset( $ct->name ) && isset( $themes[$ct->name] ) ) {
			unset( $themes[$ct->name] );
		}

		// sort themes using strnatcasecmp function
		uksort( $themes, 'strnatcasecmp' );

		// themes per page
		$per_page = 24;

		// get current page
		$page  = $this->get_pagenum();
		$start = ( $page - 1 ) * $per_page;

		$this->items = array_slice( $themes, $start, $per_page );

		// set total themes and themes per page
		$this->set_pagination_args( array(
			'total_items' => count( $themes ),
			'per_page'    => $per_page,
		) );
	}

	/**
	 * Returns html display of themes table
	 *
	 * @return string
	 */
	public function display() {
		$this->tablenav( 'top' );
		echo '<div id="availablethemes">',
			$this->display_rows_or_placeholder(),
			'</div>';
		$this->tablenav( 'bottom' );
	}

	/**
	 * tablenav function
	 *
	 * @return void
	 */
	public function tablenav( $which = 'top' ) {
		if ( $this->get_pagination_arg( 'total_pages' ) <= 1 ) {
			return '';
		}
		?>
		<div class="tablenav themes <?php echo $which; ?>">
			<?php $this->pagination( $which ); ?>
		   <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"
				class="ajax-loading list-ajax-loading"
				alt="" />
		  <br class="clear" />
		</div>
		<?php
	}

	/**
	 * ajax_user_can function
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		// Do not check edit_theme_options here.
		// AJAX calls for available themes require switch_themes.
		return current_user_can( 'switch_themes' );
	}

	/**
	 * no_items function
	 *
	 * @return void
	 **/
	public function no_items() {
		if ( is_multisite() ) {
			if (
				current_user_can( 'install_themes' ) &&
				current_user_can( 'manage_network_themes' )
			) {
				printf(
					Ai1ec_I18n::__(
						'You only have one theme enabled for this site right now. Visit the Network Admin to <a href="%1$s">enable</a> or <a href="%2$s">install</a> more themes.'
					),
					network_admin_url(
						'site-themes.php?id=' . $GLOBALS['blog_id']
					),
					network_admin_url( 'theme-install.php' )
				);

				return;
			} elseif ( current_user_can( 'manage_network_themes' ) ) {
				printf(
					Ai1ec_I18n::__(
						'You only have one theme enabled for this site right now. Visit the Network Admin to <a href="%1$s">enable</a> more themes.'
					),
					network_admin_url(
						'site-themes.php?id=' . $GLOBALS['blog_id']
					)
				);

				return;
			}
			// else, fallthrough. install_themes doesn't help if you
			// can't enable it.
		} else {
			if ( current_user_can( 'install_themes' ) ) {
				printf(
					Ai1ec_I18n::__(
						'You only have one theme installed right now. You can choose from many free themes in the Timely Theme Directory at any time: just click on the <a href="%s">Install Themes</a> tab above.'
					),
					admin_url( AI1EC_THEME_SELECTION_BASE_URL )
				);

				return;
			}
		}
		// Fallthrough.
		printf(
			Ai1ec_I18n::__(
				'Only the active theme is available to you. Contact the <em>%s</em> administrator to add more themes.'
			),
            get_site_option( 'site_name' )
		);
	}

	/**
	 * get_columns function
	 *
	 * @return array
	 **/
	public function get_columns() {
		return array();
	}

	/**
	 * display_rows function
	 *
	 * @return void
	 **/
	function display_rows() {
		$themes = $this->items;
		$theme_names = array_keys( $themes );
		natcasesort( $theme_names );

		foreach ( $theme_names as $theme_name ) {
			$class = array( 'available-theme' );
			?>
			<div class="<?php echo join( ' ', $class ); ?>">
			<?php
			if ( !empty( $theme_name ) ) :
				$template       = $themes[$theme_name]['Template'];
				$stylesheet     = $themes[$theme_name]['Stylesheet'];
				$title          = $themes[$theme_name]['Title'];
				$version        = $themes[$theme_name]['Version'];
				$description    = $themes[$theme_name]['Description'];
				$author         = $themes[$theme_name]['Author'];
				$screenshot     = $themes[$theme_name]['Screenshot'];
				$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
				$template_dir   = $themes[$theme_name]['Template Dir'];
				$parent_theme   = $themes[$theme_name]['Parent Theme'];
				$theme_root     = $themes[$theme_name]['Theme Root'];
				$theme_dir      = $themes[$theme_name]->get_stylesheet_directory();
				$legacy         = ! is_dir( $theme_dir . '/twig' );
				$theme_root_uri = esc_url( $themes[$theme_name]['Theme Root URI'] );
				$tags           = $themes[$theme_name]['Tags'];
				$thickbox_class = 'thickbox thickbox-preview';
				$legacy         = $legacy ? '1' : '0';

				// Generate theme activation link.
				$activate_link  = admin_url( AI1EC_THEME_SELECTION_BASE_URL );
				$activate_link  = add_query_arg(
					array(
						'ai1ec_action'     => 'activate_theme',
						'ai1ec_theme_dir'  => $theme_dir,
						'ai1ec_legacy'     => $legacy,
						'ai1ec_stylesheet' => $stylesheet,
						'ai1ec_theme_root' => $theme_root,
						'ai1ec_theme_url'  => $theme_root_uri . '/' . $stylesheet,
					),
					$activate_link
				);
				$activate_link  = wp_nonce_url(
					$activate_link,
					'switch-ai1ec_theme_' . $template
				);

				$activate_text  = esc_attr(
					sprintf(
						Ai1ec_I18n::__( 'Activate &#8220;%s&#8221;' ),
						$title
					)
				);
				$actions        = array();
				$actions[]      = '<a href="' . $activate_link .
					'" class="activatelink" title="' . $activate_text . '">' .
					Ai1ec_I18n::__( 'Activate' ) . '</a>';

				$actions = apply_filters(
					'theme_action_links',
					$actions,
					$themes[$theme_name]
				);

				$actions = implode ( ' | ', $actions );
			?>
				<?php if ( $screenshot ) : ?>
					<img src="<?php echo $theme_root_uri . '/' . $stylesheet . '/' . $screenshot; ?>" alt="" />
				<?php endif; ?>
				<h3>
			<?php
				/* translators: 1: theme title, 2: theme version, 3: theme author */
				printf(
					Ai1ec_I18n::__( '%1$s %2$s by %3$s' ),
					$title,
					$version,
					$author
				); ?></h3>
				<p class="description"><?php echo $description; ?></p>
				<span class='action-links'><?php echo $actions; ?></span>
				<?php if ( current_user_can( 'edit_themes' ) && $parent_theme ) {
					/* translators: 1: theme title, 2:  template dir, 3: stylesheet_dir, 4: theme title, 5: parent_theme */ ?>
					<p>
						<?php
						printf(
							Ai1ec_I18n::__(
								'The template files are located in <code>%2$s</code>. The stylesheet files are located in <code>%3$s</code>. <strong>%4$s</strong> uses templates from <strong>%5$s</strong>. Changes made to the templates will affect both themes.'
							),
							$title,
							str_replace( WP_CONTENT_DIR, '', $template_dir ),
							str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ),
							$title,
							$parent_theme
						);
						?>
					</p>
			<?php } else { ?>
				<p>
					<?php
					printf(
						Ai1ec_I18n::__(
							'All of this theme&#8217;s files are located in <code>%2$s</code>.'
						),
						$title,
						str_replace( WP_CONTENT_DIR, '', $template_dir ),
						str_replace( WP_CONTENT_DIR, '', $stylesheet_dir )
					);
					?>
				</p>
			<?php } ?>
			<?php if ( $tags ) : ?>
				<p>
				 <?php echo Ai1ec_I18n::__( 'Tags:' ); ?> <?php echo join( ', ', $tags ); ?>
				</p>
			<?php endif; ?>
		<?php endif; // end if not empty theme_name ?>
			</div>
		<?php
		} // end foreach $theme_names
	}

	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since 2.0.0
	 *
	 * @return unknown
	 */
	function current_theme_info() {
		$themes        = $this->_registry->get( 'theme.search' )
			->filter_themes();
		$current_theme = $this->get_current_ai1ec_theme();
		if ( ! $themes ) {
			$ct       = new stdClass;
			$ct->name = $current_theme;
			return $ct;
		}

		if ( ! isset( $themes[$current_theme] ) ) {
			delete_option( 'ai1ec_current_theme' );
			$current_theme = $this->get_current_ai1ec_theme();
		}

		$ct                 = new stdClass;
		$ct->name           = $current_theme;
		$ct->title          = $themes[$current_theme]['Title'];
		$ct->version        = $themes[$current_theme]['Version'];
		$ct->parent_theme   = $themes[$current_theme]['Parent Theme'];
		$ct->template_dir   = $themes[$current_theme]['Template Dir'];
		$ct->stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
		$ct->template       = $themes[$current_theme]['Template'];
		$ct->stylesheet     = $themes[$current_theme]['Stylesheet'];
		$ct->screenshot     = $themes[$current_theme]['Screenshot'];
		$ct->description    = $themes[$current_theme]['Description'];
		$ct->author         = $themes[$current_theme]['Author'];
		$ct->tags           = $themes[$current_theme]['Tags'];
		$ct->theme_root     = $themes[$current_theme]['Theme Root'];
		$ct->theme_root_uri = esc_url( $themes[$current_theme]['Theme Root URI'] );
		return $ct;
	}
	/**
	 * Retrieve current theme display name.
	 *
	 * If the 'current_theme' option has already been set, then it will be returned
	 * instead. If it is not set, then each theme will be iterated over until both
	 * the current stylesheet and current template name.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_current_ai1ec_theme() {
		$option = $this->_registry->get( 'model.option' );
		$theme  = $option->get( 'ai1ec_current_theme', array() );
		return $theme['stylesheet'];
	}

}
