<?php $current_options = get_option('elitepress_lite_options',theme_data_setup()); ?>
<!-- Footer Section -->

				<div class="container">
		<!-- Footer Widget -->	
		<div class="row footer-widget-section">
			
			<?php 
			if ( is_active_sidebar( 'footer_widget_area' ) )
			{ dynamic_sidebar( 'footer_widget_area' );	}
			?>
		</div>
		<!-- /Footer Widget -->	
		</div>

<!-- /Footer Section -->
<!-- Custom css --->
<?php
if($current_options['webrit_custom_css']!='') {  ?>
<style>
<?php echo $current_options['webrit_custom_css']; ?>
</style>
<?php }  ?>

<!-- Footer Copyright Section -->
<div class="footer-copyright-section">
	<div class="container">
		
		<div class="row">
			<div class="col-md-7">
				<div class="footer-copyright">
					<?php echo $current_options['footer_copyright_text'];?>
				</div>
			</div>
			<div class="col-md-5">
			<?php if($current_options['footer_menu_bar_enabled']=='on') { ?>	
			<?php
			wp_nav_menu( array(  
					'theme_location' => 'footer_menu',
					'container'  => 'nav-collapse collapse navbar-inverse-collapse',
					'menu_class' => 'footer-menu-links',
					'fallback_cb' => 'webriti_fallback_page_menu',
					'walker' => new webriti_nav_walker()
					)
				);	
			?>
			<?php } ?>
			</div>
		</div> 
	</div>
</div>
<!-- /Footer Copyright Section -->
</div><!-- /Close of wrapper -->  

<!--Scroll To Top--> 
<a href="#" class="hc_scrollup"><i class="fa fa-chevron-up"></i></a>
<!--/Scroll To Top--> 
<?php wp_footer(); ?>
	</body>
</html>