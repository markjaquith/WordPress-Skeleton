<center>
<p class="vendor-links">
        <a href="<?php echo $shop_page; ?>" class="button"><?php echo _e( 'View Your Store', 'wcvendors' ); ?></a>
        <a href="<?php echo $settings_page; ?>" class="button"><?php echo _e( 'Store Settings', 'wcvendors' ); ?></a>
<?php
$wcv_profile_id = get_current_user_id(); //gets current user id
$profile_url = bp_core_get_user_domain ( $wcv_profile_id ); //gets url of members profile dashboard based on current user id
?>
<?php if ( $can_submit ) { ?>
                <a target="_TOP" href="<?php echo $profile_url.'products/products-create'; ?>" class="button"><?php echo _e( 'Add New Product', 'wcvendors' ); ?></a>
                <a target="_TOP" href="<?php echo $profile_url.'products/products-my-posts'; ?>" class="button"><?php echo _e( 'Edit Products', 'wcvendors' ); ?></a>
<?php } ?>
</center>

<hr>
<?php
$author_id = get_current_user_id();
$shop_name_set = WCV_Vendors::get_vendor_shop_name( $author_id );
$vendor_login = get_userdata($author_id);
if ($shop_name_set == $vendor_login->user_login) { //outputs warning to vendor if they haven't configured their shop settings
  echo '<div class="alert alert-warning"><h4>You haven\'t configured your shop yet!</h4><p>Also don\'t forget to set your paypal email address in the shop settings to receive payments.<br><br><a href="'.$settings_page.'">Click here to configure your store</a>.</p></div>';
}

?>
