<?php
/**
 * class-rb-spectacle.php
 * 
 * Project: wp-production2
 * User:    Félix Dion Robidoux
 * Date:    11/02/2015
 * Time:    4:29 PM
 */

class RB_Spectacle
{
	/** @var RB_Spectacle_Loader */
	protected $loader;

	/** @var String */
	protected $slug;

	/** @var String */
	protected $version;

	/** @var bool */
	private $isAdmin;

	/**
	 *
	 */
	public function __construct()
	{
		// Définir le nom de la slug. (Pour les pages)
		$this->plugin_slug = 'rb-spectacle-slug';

		// Définir le numéro de version. (À changer de temps en temps)
		$this->version = '0.1.0';

		// Définir la valeur de is_admin afin de ne pas à la re-vérifier tout le temps.
		// (Réduit le temps de chargement; Chaque seconde compte, ti-gars!)
		$this->isAdmin = is_admin();

		// Charger les dépendances dans la mémoire, dont les sous-classes Admin et Loader.
		$this->load_dependencies();

		// Charger les hooks du panneau d'administration... si on est admin!
		if ( $this->isAdmin ) {
			$this->define_admin_hooks();
		}
	}

	/**
	 * Retourne la valeur booléenne de la fonction WP « is_admin() ».
	 *
	 * @return bool Vrai si l'utilisateur est admin, faux sinon.
	 */
	public function isAdmin()
	{
		return $this->isAdmin;
	}

	/**
	 * Charge les dépendances du programme.
	 *
	 * La liste des dépendances est la suivante :
	 *  - Loader
	 */
	private function load_dependencies()
	{
		// Inclure les fonctions d'administration, si on est loggé en tant qu'admin.
		// Ça va réduire le load.
		if( $this->isAdmin ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/class-rb-spectacle-admin.php";
		}

		// Inclure le loader pour les styles.
		require_once plugin_dir_path( __FILE__  ) . "class-rb-spectacle-loader.php";

		// Créer le loader.
		$this->loader = new RB_Spectacle_Loader();
	}

	/**
	 * 
	 */
	private function define_admin_hooks()
	{

	}

	public function run()
	{

	}

	public function get_version()
	{

	}
}