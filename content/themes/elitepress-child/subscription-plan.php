<?php
/* Template Name: subscription-plan */
get_header();
?>
<div class="content-section">
	<div class="container">
		<div class="title-section">
			<div class="row">
				<div class="col-md-12">
					<h3>My Subscription Plan</h3>
				</div>
			</div>
		 </div>
		 <?php if(current_user_is(s2member_level1)){?>
				<p>Hi there <?php echo do_shortcode('[s2Get constant="S2MEMBER_CURRENT_USER_DISPLAY_NAME" /]'); ?></p>
				<p>You have an <?php echo do_shortcode('[s2Get constant="S2MEMBER_CURRENT_USER_ACCESS_LABEL" /]'); ?></p>
		 <?php }?>	
	</div>
</div>		
<?php 
get_footer();
?>
