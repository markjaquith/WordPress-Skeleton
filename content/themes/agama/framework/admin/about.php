<?php
/**
 * About Agama Page under Appearances
 *
 * @since Agama v1.0.1
 */
if( ! class_exists( 'Agama_About' ) ) {
	class Agama_About
	{
		/**
		 * Class Constructor
		 *
		 * @since Agama v1.0.1
		 */
		public function __construct() {
			// Register page
			add_action('admin_menu', array( $this, 'register_page' ) );
		}
		
		/**
		 * Register 'About Agama' Page
		 *
		 * @since Agama v1.0.1
		 */
		public function register_page() {
			add_theme_page( 
				__( 'About Agama', 'agama' ), 
				__( 'About Agama', 'agama' ), 
				'edit_theme_options', 
				'about-agama', 
				array( $this, 'render_page' )
			);
		}
		
		/**
		 * Render 'About Agama' Page
		 *
		 * @since Agama v1.0.1
		 */
		public function render_page() {
			echo '<div class="wrap about-wrap">';
				echo '<h1>'.sprintf( __( 'Welcome to Agama v%s', 'agama' ), AGAMA_VER ).'</h1>';
				
				echo '<div class="about-text">';
					echo __( 'Thank you for using Agama theme!', 'agama' ).'<br>';
					echo __( 'Consider supporting us with any desired amount donation.', 'agama' ).'<br>';
					echo __( 'Even the smallest donation amount means a lot for us.', 'agama' ).'<br>';
					echo __( 'With donations you are helping us for faster theme development & updates.', 'agama' ).'<br><br>';
					echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
							<div class="paypal-donations">
							<input type="hidden" name="cmd" value="_donations">
							<input type="hidden" name="bn" value="TipsandTricks_SP">
							<input type="hidden" name="business" value="paypal@theme-vision.com">
							<input type="hidden" name="return" value="https://www.theme-vision.com/thank-you/">
							<input type="hidden" name="item_name" value="Agama development support.">
							<input type="hidden" name="rm" value="0">
							<input type="hidden" name="currency_code" value="USD">
							<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online.">
							<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
							</div>
						</form>';
					
					echo __( 'or', 'agama' ).'<br>';
					
					echo sprintf( __( 'You can support us by Buying an %s theme with allot new features & extensions!', 'agama' ), '<a href="http://theme-vision.com/agama-pro/" title="Agama Pro" target="_blank">AgamaPRO</a>' );
				echo '</div>';
				
				echo '<h2 class="nav-tab-wrapper">';
					echo '<a class="nav-tab nav-tab-active">'.__( 'What\'s New', 'agama' ).'</a>';
				echo '</h2>';
				
				echo '<div class="changelog point-releases">';
					echo '<h3>Changelog Agama v1.1.3</h3>';
						echo '<p><strong>Version 1.1.3</strong> FIXED: Sticky top menu max-width issue.</p>';
						echo '<p><strong>Version 1.1.3</strong> ADDED: Image support feature on frontpage boxes.</p>';
						echo '<p><strong>Version 1.1.3</strong> ADDED: Contact Form 7 style support.</p>';
						echo '<p><strong>Version 1.1.3</strong> IMPROVED: bbPress styling.</p>';
					
					echo '<h3>Changelog Agama v1.1.2</h3>';
						echo '<p><strong>Version 1.1.2</strong> FIXED: issues with menu assign checkbox visibility.</p>';
						echo '<p><strong>Version 1.1.2</strong> ADDED: Blog thumbnails url enable / disable feature.</p>';
						echo '<p><strong>Version 1.1.2</strong> ADDED: Top menu feature on sticky header.</p>';
						echo '<p><strong>Version 1.1.2</strong> ADDED: New social icons style.</p>';
						echo '<p><strong>Version 1.1.2</strong> IMPROVED: Menu hover in, hover out animations.</p>';
					
					echo '<h3>Changelog Agama v1.1.1</h3>';
						echo '<p><strong>Version 1.1.1</strong> added Frontpage boxes feature.</p>';
						echo '<p><strong>Version 1.1.1</strong> added About author enable / disable feature.</p>';
						echo '<p><strong>Version 1.1.1</strong> added 1 new blog layout (small thumbs) feature.</p>';
						echo '<p><strong>Version 1.1.1</strong> added sidebar align (left / right) feature.</p>';
						echo '<p><strong>Version 1.1.1</strong> added Enable / Disable "Read More" blog url feature.</p>';
						echo '<p><strong>Version 1.1.1</strong> cleaned Unused files & code.</p>';
						echo '<p><strong>Version 1.1.1</strong> fixed Blog grid style, comments overlap with posts.</p>';
						echo '<p><strong>Version 1.1.1</strong> fixed Few issues in mobile navigation.</p>';
						echo '<p><strong>Version 1.1.1</strong> fixed Duplicated post categories bug.</p>';
						echo '<p><strong>Version 1.1.1</strong> improved Editor stylesheet.</p>';
						echo '<p><strong>Version 1.1.1</strong> improved Infinite scroll, added trigger feature (auto / button).</p>';
						echo '<p><strong>Version 1.1.1</strong> improved Blog posts styling.</p>';
						echo '<p><strong>Version 1.1.1</strong> improved Comments layout restyled.</p>';
						echo '<p><strong>Version 1.1.1</strong> improved overall styling.</p>';
				echo '</div>';
				
			echo '</div>';
		}
	}
	new Agama_About;
}