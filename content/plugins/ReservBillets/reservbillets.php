<?php defined('ABSPATH') or die("No script kiddies please!");
/*
 * Plugin Name: Réservations de Billets
 * Plugin URI: n/a
 * Description: Un système de réservation de billets.
 * Version: EARLY ALPHA
 * Author: Jonathan Martin, Félix Dion Robidoux
 * Author URI: n/a
 * License: GPL2
 * Text Domain: reservation-billets
 *
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
 * along with ReservationBillets. If not, see {License URI}.
 */

/**
 * Cette fonction sera appelée à chaque rafraîchissement de la page.
 */
function reserv_billets_init()
{
	// TODO la fonction d'init.

}

// Ajouter la fonction d'init ci-dessus à l'action d'initialisation.
add_action("init", "reserv_billets_init");

/**
 * Cette méthode s'exécute lors de l'activation du plugin dans le
 * panneau de contrôle de WordPress.
 *
 * Je propose qu'on y ajoute des tables dans la BD ou des dossiers
 * et fichiers utilisés par notre plugin.
 *
 * De plus, d'après la documentation de WP sur le plugins :
 *
 * « One of the most common uses for an activation hook is to refresh
 * WordPress permalinks when a plugin registers a custom post type.
 * This gets rid of the nasty 404 errors. »
 *
 * Donc on va probablement devoir faire ça.
 */
function reserv_billets_installation()
{
	// TODO le processus d'activation.

	// TODO Ajout des custom post-types.

	// Flusher les liens permanents après l'ajout de custom post-types.
	flush_rewrite_rules();
}

// Enregistrer le hook pour l'activation du plugin.
register_activation_hook( __FILE__, "reserv_billets_installation" );

/**
 * Cette méthode s'exécute lors de la désactivation du plugin.
 *
 * Je suggère qu'elle enlève les éléments assignés dans
 * « activation_reserv_billets » afin de ne laisser la moindre trace!
 */
function reserv_billets_deactivation()
{
	// TODO le processus de désactivation.

	// Les post-types se désenregistrent automatiquement lors de la désinstallation
	// d'un plugin, donc pas vraiment besoin de faire quoi que ce soit là-dessus.

	//...cependant, y faut flusher les liens permanents; Ça, t'as pas le choix!
	flush_rewrite_rules();
}

// Enregistrer le hook pour la désactivation du plugin.
register_deactivation_hook( __FILE__, "reserv_billets_deactivation" );





