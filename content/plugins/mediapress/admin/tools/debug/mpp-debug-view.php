<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<div class="wrap">
	<style type="text/css">
	#mpp-admin-debug-textarea{
		height: 500px;
	}
</style>
	<h2 class="mpp-admin-title"><?php _e( 'System Info', 'mediapress' ); ?></h2>

		<div id="mpp-admin-debuf-info-section-wrapper">
			<?php // form tag to avoid invalid html ?>
			<form action="" method="post" enctype="multipart/form-data" >
				
				<div id="template">
					
					<textarea readonly="readonly" onclick="this.focus();this.select()" id="mpp-admin-debug-textarea" name="mpp-admin-debug-textarea" title="<?php _e( 'To copy the System Info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'mediapress' ); ?>">
<?php //Non standard indentation needed for plain-text display ?>
<?php echo esc_html( $this->display() ) ?>
					</textarea>
				</div>
				
			</form>
			
		</div>
</div>
