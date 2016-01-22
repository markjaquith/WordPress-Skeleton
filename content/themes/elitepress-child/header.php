<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />    
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<?php $current_options=get_option('elitepress_lite_options'); ?>
	<?php if($current_options['upload_image_favicon']!=''){ ?>
	<link rel="shortcut icon" href="<?php  echo esc_url($current_options['upload_image_favicon']); ?>" />
	<link href='https://fonts.googleapis.com/css?family=Raleway:400,700' rel='stylesheet' type='text/css'>
		
	<?php } wp_head(); ?>
</head>
<body <?php body_class(); ?> >
<!-- Wrapper -->
<div id="wrapper">
<!-- Header Section -->
<div class="header-section">

	<!-- Header social & Contact Info -->
	<?php get_template_part('header','social-section'); ?>
	<!-- /Header social & Contact Info -->
	
	<!-- Logo goes here -->
	<?php get_template_part('header','logo-section'); ?>
	<!-- /Logo goes here -->
	
	<!-- Navigation Section -->
	<?php get_template_part('header','menu-section'); ?>
	<!-- /Navigation Section -->
	
</div>	
<!-- /Header Section -->	