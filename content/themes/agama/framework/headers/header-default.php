	
	<?php if( get_theme_mod( 'agama_top_navigation', true ) ): ?>
	<div class="top-nav-wrapper">
		<div class="top-nav-sub-wrapper">

			<nav id="top-navigation" class="top-navigation pull-left" role="navigation">
				
				<?php echo Agama::menu( 'top', 'top-nav-menu' ); ?>
			
			</nav><!-- .top-navigation -->
			
			
			<?php if( get_theme_mod( 'agama_top_nav_social', true ) ): ?>
			
				<!-- Top Social -->
				<div id="top-social" class="pull-right">
					<?php Agama::sociali( false, 'animated' ); ?>
				</div><!-- / Top Social -->
				
			<?php endif; ?>
		
		</div>
	</div><!-- .top-wrapper -->
	<?php endif; ?>

	<hgroup>
	
		<?php if( get_theme_mod( 'agama_logo' ) ): ?>
		<a href="<?php echo esc_url( home_url('/') ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
			<img src="<?php echo esc_url( get_theme_mod( 'agama_logo', '' ) ); ?>" class="logo">
		</a>
		<?php else: ?>
		<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		<?php endif; ?>
		
	</hgroup><!-- hgroup -->

	<nav id="site-navigation" class="main-navigation" role="navigation">
	
		<?php echo Agama::menu( 'primary', 'nav-menu' ); ?>
		
	</nav><!-- #main-navigation -->

	<div class="mobile-nav">
		<div class="mobile-nav-icons">
			<?php if( class_exists('Woocommerce') ): global $woocommerce; ?>
			<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" class="fa fa-2x fa-shopping-cart"></a>
			<?php endif; ?>
			<a class="fa fa-2x fa-bars"></a>
		</div>
		<?php echo Agama::menu( 'primary', 'mobile-nav-menu' ); ?>
	</div><!-- .mobile-nav -->