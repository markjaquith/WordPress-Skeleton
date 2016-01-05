<div class="wrap kamn-easy-twitter-feed-widget-settings">
  
  <?php 
  /** Get the plugin data. */
  $kamn_easy_twitter_feed_widget_plugin_data = kamn_easy_twitter_feed_widget_plugin_data();
  screen_icon();
  ?>
  
  <h2><?php echo sprintf( __( '%1$s Settings', 'kamn-easy-twitter-feed-widget' ), $kamn_easy_twitter_feed_widget_plugin_data['Name'] ); ?></h2>    
  
  <?php settings_errors(); ?>

  <div class="kamn-easy-twitter-feed-widget-promo-wrapper">
    <a href="http://designorbital.com/premium-wordpress-themes/?utm_source=wporg-etfw&utm_medium=button&utm_campaign=premium-wp-themes" class="button button-primary button-hero" target="_blank"><?php _e( 'Premium WordPress Themes', 'kamn-easy-twitter-feed-widget' ); ?></a>
    <a href="http://designorbital.com/free-wordpress-themes/?utm_source=wporg-etfw&utm_medium=button&utm_campaign=free-wp-themes" class="button button-hero" target="_blank"><?php _e( 'Free WordPress Themes', 'kamn-easy-twitter-feed-widget' ); ?></a>
    <a href="https://www.facebook.com/designorbital" class="button button-hero" target="_blank"><?php _e( 'Like Us On Facebook', 'kamn-easy-twitter-feed-widget' ); ?></a>
    <a href="https://twitter.com/designorbital" class="button button-hero" target="_blank"><?php _e( 'Follow On Twitter', 'kamn-easy-twitter-feed-widget' ); ?></a>
  </div>
  
  <form action="options.php" method="post" id="kamn-easy-twitter-feed-widget-form-wrapper">
    
    <div id="kamn-easy-twitter-feed-widget-form-header" class="kamn-easy-twitter-feed-widget-clearfix">
      <input type="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'kamn-easy-twitter-feed-widget' ); ?>">
    </div>
	
	<?php settings_fields( 'kamn_easy_twitter_feed_widget_options_group' ); ?>
    
    <div id="kamn-easy-twitter-feed-widget-sidebar">
      
      <ul id="kamn-easy-twitter-feed-widget-group-menu">
        <li id="0_section_group_li" class="kamn-easy-twitter-feed-widget-group-tab-link-li active"><a href="javascript:void(0);" id="0_section_group_li_a" class="kamn-easy-twitter-feed-widget-group-tab-link-a" data-rel="0"><span><?php _e( 'Twitter Script Settings', 'kamn-easy-twitter-feed-widget' ); ?></span></a></li>
        <li id="1_section_group_li" class="kamn-easy-twitter-feed-widget-group-tab-link-li"><a href="javascript:void(0);" id="1_section_group_li_a" class="kamn-easy-twitter-feed-widget-group-tab-link-a" data-rel="1"><span><?php _e( 'General Settings', 'kamn-easy-twitter-feed-widget' ); ?></span></a></li>
      </ul>
    
    </div>
    
    <div id="kamn-easy-twitter-feed-widget-main">
    
      <div id="0_section_group" class="kamn-easy-twitter-feed-widget-group-tab">
        <?php do_settings_sections( 'kamn_easy_twitter_feed_widget_section_script_page' ); ?>
      </div>
      
      <div id="1_section_group" class="kamn-easy-twitter-feed-widget-group-tab">
        <?php do_settings_sections( 'kamn_easy_twitter_feed_widget_section_general_page' ); ?>
      </div>
      
    </div>
    
    <div class="kamn-easy-twitter-feed-widget-clear"></div>
    
    <div id="kamn-easy-twitter-feed-widget-form-footer" class="kamn-easy-twitter-feed-widget-clearfix">
      <input type="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'kamn-easy-twitter-feed-widget' ); ?>">
    </div>
    
  </form>

</div>