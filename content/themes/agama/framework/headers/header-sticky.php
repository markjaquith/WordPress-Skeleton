<?php if( get_theme_mod( 'agama_top_navigation', true ) ): ?>
<div id="top-bar">
	<div id="top-bar-wrap">
		<div class="container-fullwidth clearfix">

			<div class="pull-left nobottommargin">
				
				<!-- Top Links -->
				<div class="top-links">
				
					<?php echo Agama::menu( 'top' ); ?>
					
				</div><!-- / Top Links -->

			</div>

			<div class="pull-right nobottommargin">

				<!-- Top Social -->
				<div id="top-social">
					<?php Agama::sociali( false, 'animated' ); ?>
				</div><!-- / Top Social -->

			</div>

		</div>
	</div>
</div><!-- #top-bar end -->
<?php endif; ?>

<div class="sticky-header clear">
	<div class="sticky-header-inner clear">
	
		<div class="pull-left">
			<?php if( get_theme_mod( 'agama_logo', '' ) ): ?>
				<a href="<?php echo esc_url( home_url('/') ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
					<img src="<?php echo esc_url( get_theme_mod( 'agama_logo' ) ); ?>" class="logo">
				</a>
			<?php else: ?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<?php endif; ?>
		</div>
		
		<nav role="navigation" class="pull-right">
			
			<?php echo Agama::menu( 'primary', 'sticky-nav' ); ?>
			
		</nav><!-- .top-navigation -->
		
	</div>
	<div class="mobile-nav clearfix">
		<div class="mobile-nav-icons">
			<?php if( class_exists('Woocommerce') ): global $woocommerce; ?>
			<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" class="fa fa-2x fa-shopping-cart"></a>
			<?php endif; ?>
			<a class="fa fa-2x fa-bars"></a>
		</div>
		<?php echo Agama::menu( 'primary', 'mobile-nav-menu' ); ?>
	</div><!-- .mobile-nav -->
</div>