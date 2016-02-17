<center>
<p class="vendor-links">
        <a href="<?php echo $shop_page; ?>" class="button"><?php echo _e( 'View Your Store', 'wcvendors' ); ?></a>
        <a href="<?php echo $settings_page; ?>" class="button"><?php echo _e( 'Store Settings', 'wcvendors' ); ?></a>

<?php if ( $can_submit ) { ?>
                <a target="_TOP" href="<?php echo $submit_link; ?>" class="button"><?php echo _e( 'Add New Product', 'wcvendors' ); ?></a>
                <a target="_TOP" href="<?php echo $edit_link; ?>" class="button"><?php echo _e( 'Edit Products', 'wcvendors' ); ?></a>
<?php } ?>
</center>

<hr>
<?php
$author_id = get_current_user_id();
$shop_name_set = WCV_Vendors::get_vendor_shop_name( $author_id );
$vendor_login = get_userdata($author_id);
if ($shop_name_set == $vendor_login->user_login) { //outputs warning to vendor if they haven't configured their shop settings
  echo '<div class="alert alert-warning"><h4>You haven\'t configured your store name!</h4><p>Also make sure to set your paypal email address in the shop settings to receive payments.<br><br><a href="'.$settings_page.'">Click here to configure your store</a>.</p></div>';
}

?>
