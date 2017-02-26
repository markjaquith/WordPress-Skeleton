<?php
/**
 * @wordpress-plugin
 * Plugin Name: Réservations de Billets
 * Description: Un système de réservation de billets.
 * Version: EARLY ALPHA
 * Author: Jonathan Martin & Félix Dion Robidoux
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: reserv-billets-locale
 * Domain Path: /lang
 *
 * @license
 * ReservBillets is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * ReservBillets is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ReservationBillets.
 * If not, see http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Protection extra contre les script-kiddies!
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Prendre la classe de base du plugin.
/** @noinspection PhpIncludeInspection */
require_once plugin_dir_path( __FILE__ ) . "includes/class-rb-spectacle.php";

/**
 * Cette fonction sera appelée lors de l'exécution du plugin.
 */
function reserv_billets_exec()
{
	// TODO la fonction d'init.
	$rb = new RB_Spectacle();
	$rb->run();
}

/**
 * Cette méthode s'exécute lors de l'INSTALLATION du plugin dans le
 * panneau de contrôle de WordPress.
 *
 * @notesDeFelix Je propose qu'on y ajoute des tables dans la BD ou des dossiers
 * et fichiers utilisés par notre plugin.
 *
 * De plus, d'après la documentation de WP sur le plugins :
 *
 * « One of the most common uses for an activation hook is to refresh
 * WordPress permalinks when a plugin registers a custom post type.
 * This gets rid of the nasty 404 errors. »
 *
 * Donc on va probablement devoir faire ça.
 *
 * VOIR: https://developer.wordpress.org/plugins/the-basics/activation-deactivation-hooks/
 */
function rb_installation()
{
	// TODO le processus d'activation.

	// TODO Ajout des custom post-types.

	// Flusher les liens permanents après l'ajout de custom post-types.
	flush_rewrite_rules();
}

/**
 * Cette méthode s'exécute lors de la DÉSACTIVATION du plugin.
 *
 * VOIR: https://developer.wordpress.org/plugins/the-basics/activation-deactivation-hooks/
 */
function rb_deactivation()
{
	// TODO le processus de désactivation.

	// Note: Les post-types se désenregistrent automatiquement lors de la désinstallation d'un plugin, donc pas vraiment besoin de faire quoi que ce soit là-dessus.

	//...cependant, y faut flusher les liens permanents; Ça, t'as pas le choix!
	flush_rewrite_rules();
}

// Ajouter la fonction d'init ci-dessus à l'action d'initialisation.
//add_action( "init", "reserv_billets_init" );

// Enregistrer le hook pour l'activation du plugin.
register_activation_hook( __FILE__, "rb_installation" );

// Enregistrer le hook pour la désactivation du plugin.
register_deactivation_hook( __FILE__, "rb_deactivation" );

reserv_billets_exec();

