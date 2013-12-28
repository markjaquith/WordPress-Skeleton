<!doctype html>
<!--[if lt IE 7]> <html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7" > <![endif]-->
<!--[if IE 7]>    <html <?php language_attributes(); ?> class="no-js ie7 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html <?php language_attributes(); ?> class="no-js ie8 lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title><?php ci_e_title(); ?></title>

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Mobile Specific Metas
================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<?php // CSS files are loaded via /functions/styles.php ?>

	<?php // JS files are loaded via /functions/scripts.php ?>

	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

<div id="page-wrap">
	<header id="header">
		<div class="container">
			<div id="prehead" class="row">

				<?php if ( ci_setting('header_contact_text') and ci_setting('header_contact_text_emph') ): ?>
					<div id="head-contact" class="four columns">
						<span>
							<?php ci_e_setting('header_contact_text'); ?> 
							<?php if(ci_setting('header_contact_text_emph')): ?>
								<b><?php ci_e_setting('header_contact_text_emph'); ?></b>
							<?php endif; ?>
						</span>
					</div> <!-- .head-contact -->
				<?php endif; ?>

				<?php dynamic_sidebar('header-wgt'); ?>
			</div> <!-- #prehead -->

			<div id="main-head" class="row">
				<div class="five columns">
					<hgroup class="logo <?php logo_class(); ?>">
						<?php ci_e_logo('<h1>', '</h1>'); ?>
						<?php ci_e_slogan('<h2>', '</h2>'); ?>
					</hgroup>

				</div> <!-- logo container -->

				<div class="eleven columns">
					<nav id="nav" class="group">
						<?php
							if(has_nav_menu('ci_main_menu'))
								wp_nav_menu( array(
									'theme_location' 	=> 'ci_main_menu',
									'fallback_cb' 		=> '',
									'container' 		=> '',
									'menu_id' 			=> 'navigation',
									'menu_class' 		=> 'group sf-menu'
								));
							else
								wp_page_menu(array('menu_class'=>''));
						?>
					</nav><!-- /nav -->
				</div>
			</div>
		</div> <!-- .container < #header -->
	</header>

