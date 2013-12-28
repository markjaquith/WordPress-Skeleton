<footer id="footer">
	<div class="container">
		<div id="footer-widgets" class="row">
			<?php dynamic_sidebar('footer-widgets'); ?>
		</div>
	</div> <!-- .container < #footer -->

	<div id="credits">
		<div class="container">
			<div class="row">
				<span class="ten columns"><?php echo ci_footer(); ?></span>
				<span class="six columns back-top-hold">
					<a class="back-top" href=""><?php _e('Return to top', 'ci_theme'); ?></a>
				</span>
			</div>
		</div> <!-- .container -->
	</div> <!-- #credits -->
</footer> <!-- #footer -->
</div> <!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
