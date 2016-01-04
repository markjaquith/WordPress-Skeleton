<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_tab_addons');

function ninja_forms_register_tab_addons(){
    $args = array(
        'name' => __( 'Extend Ninja Forms', 'ninja-forms' ),
        'page' => 'ninja-forms-extend',
        'display_function' => 'ninja_forms_tab_addons',
        'save_function' => '',
        'show_save' => false,
        'title' => '<div class="nf-brand-header"><div class="nf-logo"></div><div class="wpn-logo">brought by</div></div>',
    );
    ninja_forms_register_tab('extend', $args);
}

function ninja_forms_tab_addons(){
    // $uri = 'https://ninjaforms.com/downloads/category/ninja-forms/feed/';
    //include_once(ABSPATH . WPINC . '/feed.php');
    // $feed = fetch_feed( $uri );

    // if (!is_wp_error( $feed ) ) :
    //     $items = $feed->get_items(0, 0);
    // endif;

    $items = wp_remote_get( 'https://ninjaforms.com/?extend_feed=jlhrbgf89734go7387o4g3h' );

    $items = wp_remote_retrieve_body( $items );

    $items = json_decode( $items, true );

    //shuffle( $items );
    foreach ($items as $item) {
        $plugin_data = array();
        if( !empty( $item['plugin'] ) && file_exists( WP_PLUGIN_DIR.'/'.$item['plugin'] ) ){
            $plugin_data = get_plugin_data( WP_PLUGIN_DIR.'/'.$item['plugin'], false, true );
        }
        $version = isset ( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
        if ( ! empty ( $version ) && $version < $item['version'] ) {
            echo '<div class="error"><p>';
                echo '<strong>' . $item['title'] . '</strong> requires an update. You have version <strong>' . $version . '</strong> installed. The current version is <strong>' . $item['version'] . '</strong>.';
            echo '</p></div>';
        }
    }

    foreach ($items as $item) {

        echo '<div class="nf-extend nf-box">';
            echo '<img src="' . $item['image'] . '" />';
            echo '<h2>' . $item['title'] . '</h2>';
            echo '<div class="nf-extend-content">';
                echo '<p>' . $item['content'] . '</p>';
                echo '<div class="nf-extend-buttons">';
                if( !empty( $item['docs'] ) ) {
                    echo '<a href="' . $item['docs'] . '" class="button-secondary nf-doc-button">' . __( 'Documentation', 'ninja-forms' ) . '</a>';
                } else {
                    echo '<p>' . __( 'Documentation coming soon.', 'ninja-forms' ) . '</a>.</p>';
                }

                if( !empty( $item['plugin'] ) && file_exists( WP_PLUGIN_DIR.'/'.$item['plugin'] ) ){
                  if( is_plugin_active( $item['plugin'] ) ) {
                        echo '<span class="button-secondary nf-button">' . __( 'Active', 'ninja-forms' ) . '</span>';
                    } elseif( is_plugin_inactive( $item['plugin'] ) ) {
                        echo '<span class="button-secondary nf-button">' . __( 'Installed', 'ninja-forms' ) . '</span>';
                    } else {
                        echo '<a href="' . $item['link'] . '" title="' . $item['title'] . '" class="button-primary nf-button">' . __( 'Learn More', 'ninja-forms' ) . '</a>';
                    }
                }else{
                    echo '<a href="' . $item['link'] . '" title="' . $item['title'] . '" class="button-primary nf-button">' . __( 'Learn More', 'ninja-forms' ) . '</a>';
                }
                echo '</div>';

            echo '</div>';

        echo '</div>';
    }
}

function ninja_forms_save_addons($data){
    global $wpdb;
}
