<!-- Navigation Section -->
<?php $current_options = get_option('elitepress_lite_options',theme_data_setup());?>
<div class="menu-section">
		<nav role="navigation" class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->		
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			  <span class="sr-only"><?php _e('Toggle navigation','elitepress'); ?></span><?php _e('Navigation Menu','elitepress'); ?>
			</button>
		</div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
		
          <?php
			wp_nav_menu( array(  
					'theme_location' => 'primary',
					'container'  => 'nav-collapse collapse navbar-inverse-collapse',
					'menu_class' => 'nav navbar-nav',
					'fallback_cb' => 'webriti_fallback_page_menu',
					'walker' => new webriti_nav_walker()
					)
				);	
			?>
			<?php if($current_options['header_search_bar_enabled']=='on') { ?>	
			<form class="menu-box" id="top-menu-search" class="navbar-form navbar-left" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
						<input type="text" placeholder="Search" name="s">
			</form>
			<?php }  ?>	
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
	</div>
<!-- /Navigation Section -->