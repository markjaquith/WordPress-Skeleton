<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Ce fichier est exécuté automatiquement lors de la désinstallation et la suppression du plugin.
 *
 * Cela pourrait s'avérer inutile à priori, vu que le site n'aura JAMAIS à se débarasser du système
 * de réservations, mais vu qu'on doit coder un plugin, c'est important de faire ce qui est
 * recommandé par Wordpress.
 *
 * VOIR: https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
 *
 * D'après la page ci-dessus :
 * « Less experienced developers sometimes make the mistake of using the deactivation hook
 * for this purpose. »
 *
 * Donc ouin, on va éviter d'être des amateurs.
 */

// If uninstall is not called from WordPress, exit
// Si la désinstanllation
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$option_name = 'plugin_option_name';

delete_option( $option_name );

// Au cas où c'est un multi-site.
// #### CHOSE À SAVOIR POUR MULTI-SITE:
// « Note: In Multisite, looping through all blogs to delete options
//   can be very resource intensive. »
delete_site_option( $option_name );


// Enlève la table spécifiée de la base de données.
global $wpdb;
$nomDeLaTable = ''; // TODO effectuer un checkup automatique pour le préfixe du nom de la table.
$wpdb->get_results( 'DROP TABLE IF EXISTS wp_rb_billets' );