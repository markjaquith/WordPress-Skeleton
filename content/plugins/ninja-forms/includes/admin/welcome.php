<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * About Page Class
 *
 * @package     NF
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2014, WP Ninjas
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NF_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.4
 */
class NF_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';
	public $display_version = NF_PLUGIN_VERSION;
	public $header_text;
	public $header_desc;

	/**
	 * Get things started
	 *
	 * @since 1.4
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );

		$this->header_text = sprintf( __( 'Welcome to Ninja Forms %s', 'ninja-forms' ), $this->display_version );
		$this->header_desc = sprintf( __( 'Thank you for updating! Ninja Forms %s makes form building easier than ever before!', 'ninja-forms' ), $this->display_version );
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function admin_menus() {
		// About Page
		add_dashboard_page(
			__( 'Welcome to Ninja Forms', 'ninja-forms' ),
			__( 'Welcome to Ninja Forms', 'ninja-forms' ),
			$this->minimum_capability,
			'nf-about',
			array( $this, 'about_screen' )
		);

		// Changelog Page
		add_dashboard_page(
			__( 'Ninja Forms Changelog', 'ninja-forms' ),
			__( 'Ninja Forms Changelog', 'ninja-forms' ),
			$this->minimum_capability,
			'nf-changelog',
			array( $this, 'changelog_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with Ninja Forms', 'ninja-forms' ),
			__( 'Getting started with Ninja Forms', 'ninja-forms' ),
			$this->minimum_capability,
			'nf-getting-started',
			array( $this, 'getting_started_screen' )
		);

		// Credits Page
		add_dashboard_page(
			__( 'The people who build Ninja Forms', 'ninja-forms' ),
			__( 'The people who build Ninja Forms', 'ninja-forms' ),
			$this->minimum_capability,
			'nf-credits',
			array( $this, 'credits_screen' )
		);
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'nf-about' );
		remove_submenu_page( 'index.php', 'nf-changelog' );
		remove_submenu_page( 'index.php', 'nf-getting-started' );
		remove_submenu_page( 'index.php', 'nf-credits' );

		// Ensures style is only on welcome page
		if ((isset($_GET['page'])) && ($_GET['page']=='nf-about' || $_GET['page']=='nf-getting-started' || $_GET['page']=='nf-credits' || $_GET['page']=='nf-changelog')){

                // Badge for welcome page
		$badge_url = NF_PLUGIN_URL . 'assets/images/nf-badge.png';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.nf-badge {
			padding-top: 125px;
			height: 52px;
			width: 185px;
			color: #fff;
			font-weight: bold;
			font-size: 14px;
			text-align: center;
			margin: 0 -5px;
			background: url('<?php echo $badge_url; ?>') no-repeat;
		}

		.about-wrap .nf-badge {
			position: absolute;
			top: 0;
			right: 0;
		}

		.nf-welcome-screenshots {
			float: right;
			margin-left: 10px!important;
		}

		.about-wrap .feature-section {
			margin-top: 20px;
		}
		.about-overview {
			padding: 20px;
		}
		.about-overview iframe {
			display: block;
			margin: 0 auto;
		}

		/*]]>*/
		</style>
		<?php
		}
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'nf-about';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'nf-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'nf-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'ninja-forms' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'nf-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'nf-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'ninja-forms' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'nf-credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'nf-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'ninja-forms' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function about_screen() {
		?>
		<div class="wrap about-wrap">
			<h1><?php echo $this->header_text; ?></h1>
			<div class="about-text"><?php echo $this->header_desc; ?></div>
			<div class="nf-badge"><?php printf( __( 'Version %s', 'ninja-forms' ), $this->display_version ); ?></div>

			<?php $this->tabs(); ?>

			<div class="changelog">

				<div class="about-overview">
					<iframe width="640" height="360" src="//www.youtube.com/embed/todRiV7Cel0" frameborder="0" allowfullscreen></iframe>
				</div>
				<h2 class="about-headline-callout"><?php _e( 'A simplified and more powerful form building experience.', 'ninja-forms' );?></h2>

				<div class="feature-section col two-col">

					<div class="col-1">
						<img src="<?php echo NF_PLUGIN_URL . 'assets/images/screenshots/ss-builder.png'; ?>">
						<h4><?php _e( 'New Builder Tab', 'ninja-forms' );?></h4>
						<p><?php _e( 'When creating and editing forms, go directly to the section that matters most.', 'ninja-forms' ); ?></p>
					</div>

					<div class="col-2 last-feature">
						<img src="<?php echo NF_PLUGIN_URL . 'assets/images/screenshots/ss-field-settings.png'; ?>">
						<h4><?php _e( 'Better Organized Field Settings', 'ninja-forms' );?></h4>
						<p><?php printf( __( 'The most common settings are shown immediately, while other, non-essential, settings are tucked away inside expandable sections.', 'ninja-forms' ), admin_url( 'edit.php?post_type=download&page=nf-settings&tab=misc' ) ); ?></p>
					</div>

				</div>

				<hr />

				<div class="feature-section col three-col">

					<div class="col-1">
						<img src="<?php echo NF_PLUGIN_URL . 'assets/images/screenshots/ss-emails-actions.png'; ?>">
						<h4><?php _e( 'Improved clarity', 'ninja-forms' );?></h4>
						<p><?php _e( 'Along with the "Build Your Form" tab, we\'ve removed "Notifications" in favor of "Emails & Actions." This is a much clearer indication of what can be done on this tab.', 'ninja-forms' ); ?></p>
					</div>

					<div class="col-2">
						<img src="<?php echo NF_PLUGIN_URL . 'assets/images/screenshots/ss-nuke-option.png'; ?>">
						<h4><?php _e( 'Remove all Ninja Forms data', 'ninja-forms' );?></h4>
						<p><?php _e( 'We\'ve added the option to remove all Ninja Forms data (submissions, forms, fields, options) when you delete the plugin. We call it the nuclear option.', 'ninja-forms' ); ?></p>
					</div>

					<div class="col-3 last-feature">
						<img src="<?php echo NF_PLUGIN_URL . 'assets/images/screenshots/ss-licenses.png'; ?>">
						<h4><?php _e( 'Better license management', 'ninja-forms' );?></h4>
						<p><?php _e( 'Deactivate Ninja Forms extension licenses individually or as a group from the settings tab.', 'ninja-forms' ); ?></p>
					</div>

				</div>

				<hr />

				<div class="feature-section col two-col">

					<div class="col-1">
						<h4><?php _e( 'More to come', 'ninja-forms' ); ?></h4>
						<p><?php _e( 'The interface updates in this version lay the groundwork for some great improvements in the future. Version 3.0 will build on these changes to make Ninja Forms an even more stable, powerful, and user-friendly form builder.', 'ninja-forms' ); ?></p>
					</div>

					<div class="col-2 last-feature">
						<h4><?php _e( 'Documentation', 'ninja-forms' );?></h4>
						<p><?php _e( 'Take a look at our in-depth Ninja Forms documentation below.', 'ninja-forms' ); ?></p>
						<p>
							<a href="<?php echo esc_url( 'http://docs.ninjaforms.com/?utm_medium=plugin&utm_source=welcome-screen&utm_campaign=Ninja+Forms+Welcome&utm_content=Ninja+Forms+Docs' ); ?>"><?php _e( 'Ninja Forms Documentation', 'ninja-forms' ); ?></a> &middot;
							<a href="<?php echo esc_url( 'https://ninjaforms.com/contact/?utm_medium=plugin&utm_source=welcome-screen&utm_campaign=Ninja+Forms+Welcome&utm_content=Ninja+Forms+Support' ); ?>"><?php _e( 'Get Support', 'ninja-forms' ); ?></a>
						</p>
					</div>

				</div>

			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( 'index.php?page=ninja-forms' ) ); ?>"><?php _e( 'Return to Ninja Forms', 'ninja-forms' ); ?></a> &middot;
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'nf-changelog' ), 'index.php' ) ) ); ?>"><?php _e( 'View the Full Changelog', 'ninja-forms' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Changelog Screen
	 *
	 * @access public
	 * @since 2.0.3
	 * @return void
	 */
	public function changelog_screen() {
		list( $display_version ) = explode( '-', NF_PLUGIN_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php echo $this->header_text; ?></h1>
			<div class="about-text"><?php echo $this->header_desc; ?></div>
			<div class="nf-badge"><?php printf( __( 'Version %s', 'ninja-forms' ), $this->display_version ); ?></div>

			<?php $this->tabs(); ?>

			<div class="changelog">
				<h3><?php _e( 'Full Changelog', 'ninja-forms' );?></h3>

				<div class="feature-section">
					<?php echo $this->parse_readme(); ?>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( 'index.php?page=ninja-forms' ) ); ?>"><?php _e( 'Go to Ninja Forms', 'ninja-forms' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	public function getting_started_screen() {
		list( $display_version ) = explode( '-', NF_PLUGIN_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php echo $this->header_text; ?></h1>
			<div class="about-text"><?php echo $this->header_desc; ?></div>
			<div class="nf-badge"><?php printf( __( 'Version %s', 'ninja-forms' ), $this->display_version ); ?></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Use the tips below to get started using Ninja Forms. You will be up and running in no time!', 'ninja-forms' ); ?></p>

			<div class="changelog">

				<div class="feature-section">
					<h4><?php _e( 'All About Forms', 'ninja-forms' );?></h4>
					<img style="width: 500px; height: 292px;" src="<?php echo NF_PLUGIN_URL . 'assets/images/screenshots/ss-new-form.png'; ?>" class="nf-welcome-screenshots">

					<p><?php printf( __( 'The Forms menu is your access point for all things Ninja Forms. We\'ve already created your first %scontact form%s so that you have an example. You can also create your own by clicking %sAdd New%s.', 'ninja-forms' ), '<a href="admin.php?page=ninja-forms&tab=builder&form_id=1">', '</a>', '<a href="admin.php?page=ninja-forms&tab=builder&form_id=new">', '</a>' ); ?></p>

					<h4><?php _e( 'Build Your Form', 'ninja-forms' );?></h4>
					<p><?php _e( 'This is where you\'ll build your form by adding fields and dragging them into the order you want them to appear. Each field will have an assortment of options such as label, label position, and placeholder.', 'ninja-forms' );?></p>

					<h4><?php _e( 'Emails & Actions', 'ninja-forms' );?></h4>
					<p><?php _e( 'If you would like for your form to notify you via email when a user clicks submit, you can set those up on this tab. You can create an unlimited number of emails, including emails sent to the user who filled out the form.', 'ninja-forms' );?></p>

					<h4><?php _e( 'Settings', 'ninja-forms' );?></h4>
					<p><?php _e( 'This tab hold general form settings, such as title and submission method, as well as display settings like hiding a form when it is successfully completed.', 'ninja-forms' );?></p>

				</div>

			</div>

			<hr />

			<div class="changelog">
				<h3><?php _e( 'Displaying Your Form', 'ninja-forms' );?></h3>

				<div class="feature-section col two-col">

					<div class="col-1">
						<h4><?php _e( 'Append to Page', 'ninja-forms' );?></h4>
						<p><?php _e( 'Under Basic Form Behavior in the Form Settings you can easily select a page that you would like the form automatically appended to the end of that page\'s content. A similiar option is avaiable in every content edit screen in its sidebar.', 'ninja-forms' ); ?></p>
					</div>

					<div class="col-2 last-feature">
						<h4><?php _e( 'Shortcode', 'ninja-forms' );?></h4>
						<p><?php printf( __( 'Place %s in any area that accepts shortcodes to display your form anywhere you like. Even in the middle of your page or posts content.', 'ninja-forms' ), '[ninja_form id=1]' ); ?></p>
					</div>

				</div>

				<div class="feature-section col two-col">

					<div class="col-1">
						<h4><?php _e( 'Ninja Forms Widget', 'ninja-forms' );?></h4>
						<p><?php printf( __( 'Ninja Forms provides a widget that you can place in any widgetized area of your site and select exactly which form you would like displayed in that space.', 'ninja-forms' ), admin_url( 'edit.php?post_type=download&page=nf-settings&tab=misc' ) ); ?></p>
					</div>

					<div class="col-2 last-feature">
						<h4><?php _e( 'Template Function', 'ninja-forms' );?></h4>
						<p><?php printf( __( 'Ninja Forms also comes with a simple template function that can be placed directly into a php template file. %s', 'ninja-forms' ), '<code>if( function_exists( \'ninja_forms_display_form\' ) ){ ninja_forms_display_form( 1 ); }</code>' ); ?></p>
					</div>

				</div>

			</div>

			<hr />

			<div class="changelog">
				<h3><?php _e( 'Need Help?', 'ninja-forms' );?></h3>

				<div class="feature-section col two-col">

					<div class="col-1">
						<h4><?php _e( 'Growing Documentation', 'ninja-forms' );?></h4>
						<p><?php printf( __( 'Documentation is available covering everything from %sTroubleshooting%s to our %sDeveloper API%s. New Documents are always being added.', 'ninja-forms' ), '<a href="http://docs.ninjaforms.com/customer/portal/articles/2045713-troubleshooting-ninja-forms/?utm_medium=plugin&utm_source=welcome-screen&utm_campaign=Ninja+Forms+Welcome&utm_content=Ninja+Forms+Docs">', '</a>', '<a href="http://docs.ninjaforms.com/customer/portal/topics/798123-developer-api/articles/?utm_medium=plugin&utm_source=welcome-screen&utm_campaign=Ninja+Forms+Welcome&utm_content=Ninja+Forms+Docs">', '</a>' ); ?></p>
					</div>

					<div class="col-2 last-feature">
						<h4><?php _e( 'Best Support in the Business', 'ninja-forms' );?></h4>
						<p><?php printf( __( 'We do all we can to provide every Ninja Forms user with the best support possible. If you encounter a problem or have a question, %splease contact us%s.', 'ninja-forms' ), '<a href="https://ninjaforms.com/contact/?utm_medium=plugin&utm_source=welcome-screen&utm_campaign=Ninja+Forms+Welcome&utm_content=Ninja+Forms+Support">', '</a>' ); ?></p>
					</div>

				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Render Credits Screen
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function credits_screen() {
		list( $display_version ) = explode( '-', NF_PLUGIN_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Ninja Forms %s', 'ninja-forms' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Ninja Forms %s is primed to make your experience managing submissions an enjoyable one!', 'ninja-forms' ), $display_version ); ?></div>
			<div class="nf-badge"><?php printf( __( 'Version %s', 'ninja-forms' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Ninja Forms is created by a worldwide team of developers who aim to provide the #1 WordPress community form creation plugin.', 'ninja-forms' ); ?></p>

			<?php echo $this->contributors(); ?>
		</div>
		<?php
	}


	/**
	 * Parse the NF readme.txt file
	 *
	 * @since 2.0.3
	 * @return string $readme HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( NF_PLUGIN_DIR . 'readme.txt' ) ? NF_PLUGIN_DIR . 'readme.txt' : null;

		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changelog was found.', 'ninja-forms' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );

			$readme = explode( '== Changelog ==', $readme );
                        $readme = end( $readme );

			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
	}


	/**
	 * Render Contributors List
	 *
	 * @since 1.4
	 * @uses NF_Welcome::get_contributors()
	 * @return string $contributor_list HTML formatted list of all the contributors for NF
	 */
	public function contributors() {
		$contributors = $this->get_contributors();

		if ( empty( $contributors ) )
			return '';

		$contributor_list = '<ul class="wp-people-group">';

		foreach ( $contributors as $contributor ) {
			$contributor_list .= '<li class="wp-person">';
			$contributor_list .= sprintf( '<a href="%s" title="%s">',
				esc_url( 'https://github.com/' . $contributor->login ),
				esc_html( sprintf( __( 'View %s', 'ninja-forms' ), $contributor->login ) )
			);
			$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= '</li>';
		}

		$contributor_list .= '</ul>';

		return $contributor_list;
	}

	/**
	 * Retreive list of contributors from GitHub.
	 *
	 * @access public
	 * @since 1.4
	 * @return array $contributors List of contributors
	 */
	public function get_contributors() {
		$contributors = get_transient( 'nf_contributors' );

		if ( false !== $contributors )
			return $contributors;

		$response = wp_remote_get( 'https://api.github.com/repos/wpninjas/ninja-forms/contributors?&per_page=100', array( 'sslverify' => false ) );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return array();

		$contributors = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $contributors ) )
			return array();

		set_transient( 'nf_contributors', $contributors, 3600 );

		return $contributors;
	}

	/**
	 * Sends user to the Welcome page on first activation of NF as well as each
	 * time NF is upgraded to a new version
	 *
	 * @access public
	 * @since 1.4
	 * @global $nf_options Array of all the NF Options
	 * @return void
	 */
	public function welcome() {
		global $nf_options;

		// Bail if no activation redirect
		if ( ! get_transient( '_nf_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_nf_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		$upgrade = get_option( 'nf_version_upgraded_from' );

		if( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=nf-getting-started' ) ); exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'index.php?page=nf-about' ) ); exit;
		}
	}
}
new NF_Welcome();
