<?php

/*--------------------------------------------------------------------*/
/*     Register Google Fonts
/*--------------------------------------------------------------------*/

function elitepress_fonts_url() {
	
    $fonts_url = '';
		
    $font_families = array();
 
	$font_families = array('Lato:100,200,300,400,500,600,700,800,900','italic','Courgette','Roboto:100,300,400,700,900');
 
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );
 
        $fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );

    return $fonts_url;
}

function elitepress_scripts_styles() {
    wp_enqueue_style( 'elitepress-fonts', elitepress_fonts_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'elitepress_scripts_styles' );


?>