<?php

/**
 * RB_Spectacle
 * ===========
 *
 * Le contrôleur principal des spectacles.
 *
 * @package RB
 */
class RB_Spectacle
{
	/** @const String Le nom de la slug par défaut. */
	const SLUG_DEFAULT = 'rb-spectacle-slug';

	/** @var RB_Spectacle_Loader Le loader de l'élément. */
	protected $loader;

	/** @var String L'identifiant de la slug */
	protected $slug;

	/** @var String Le numéro de version. Pas sûr de garder ça longtemps. */
	protected $version;

	/** @var bool Détermine si l'utilisateur est admin. */
	protected $is_admin;

	/**
	 * Constructeur. Fais pas mal de choses!
	 *
	 * > NOTE DE FÉLIX: <br />
	 * > C't'une pas pire de bonne idée d'inspecter le code pour cte fonction-là!
	 *
	 * @param String|null $custom_slug Le nom du slug, si on en veut un différent
	 *                                 de celui par défaut.
	 */
	public function __construct($custom_slug = NULL)
	{
		// Définir le nom de la slug, pour les URLs.
		$this->slug = (is_null($custom_slug) ? self::SLUG_DEFAULT : $custom_slug);

		// Définir le numéro de version. (À changer de temps en temps)
		$this->version = '0.1.0';

		// Définir la valeur de is_admin afin de ne pas à la re-vérifier tout le temps.
		// (Réduit le temps de chargement; Chaque seconde compte, ti-gars!)
		$this->is_admin = is_admin();

		// Charger les dépendances dans la mémoire, dont les sous-classes Admin et Loader.
		$this->load_dependencies();


		// Charger les hooks du panneau d'administration... si on est admin!
		if ( $this->is_admin ) {
			$this->define_admin_hooks();
		}
	}

	/**
	 * Charge les dépendances du programme.
	 *
	 * Lorsqu'on crée une nouvelle
	 *
	 * @see RB_Spectacle_Admin
	 */
	private function load_dependencies()
	{
		// Inclure les fonctions d'administration, si on est loggé en tant qu'admin.
		// Ça va réduire le load.
		if ( $this->is_admin ) {
			/** @noinspection PhpIncludeInspection */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rb-spectacle-admin.php';
		}

		// Inclure le loader pour les styles.
		/** @noinspection PhpIncludeInspection */
		require_once plugin_dir_path( __FILE__ ) . 'class-rb-spectacle-loader.php';

		// Créer le loader.
		$this->loader = new RB_Spectacle_Loader();
	}

	/**
	 * Définit les hooks du panneau d'administration.
	 */
	private function define_admin_hooks()
	{
		// Créer l'objet qui gère le panneau d'administration.
		$admin = new RB_Spectacle_Admin( $this->get_version() );

		// Ajouter les actions du panneau d'admin à la queue d'action du composant loader.
		$this->loader->queue_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->queue_action( 'add_meta_boxes', $admin, 'add_meta_box' );

		// Message d'activation.
		$this->loader->queue_action( 'activated_plugin', $admin, 'add_activation_message' );

		// Custom post-type
		$this->loader->queue_action( 'init', $admin, 'create_post_type' );

		// Metadata "nb_billets"
		$this->loader->queue_filter( 'update_spectacle_metadata', $admin, 'update_spectacle_nb_billets' );
	}

	/**
	 * Retourne la version du plugin.
	 *
	 * @see RB_Spectacle::__construct()
	 * @see RB_Spectacle::define_admin_hooks()
	 *
	 * @return String La version du plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Fait battre la classe de ses propres ailes, tel un ange!
	 * ♩ ♩ ♫ Aaaaaleluia~!!! (bis) ♫ ♩ ♫
	 *
	 * ...plus sérieusement, ça exécute cette partie du plugin, en appelant les instructions
	 * d'exécution du loader.
	 */
	public function run()
	{
		$this->loader->run();
	}
}