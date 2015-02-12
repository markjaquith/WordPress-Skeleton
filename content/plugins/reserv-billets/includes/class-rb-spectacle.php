<?php

/**
 * Contrôleur principal des spectacles.
 *
 * @since Le début début.
 */
class RB_Spectacle
{
	/** @var RB_Spectacle_Loader     Le loader de l'élément. */
	protected $loader;

	/** @var String     L'identifiant de la slug */
	protected $slug;

	/** @var String     Le numéro de version. Pas sûr de garder ça longtemps. */
	protected $version;

	/** @var bool       Détermine si l'utilisateur est admin. */
	private $isAdmin;

	/**
	 * Constructeur. Fais pas mal de choses!
	 * <br /><br />
	 * <h3 style="color: white"><u>NOTE DE FÉLIX</u></h3>
	 * C't'une pas pire de bonne idée d'inspecter le code pour cte fonction-là!
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
	final public function is_admin()
	{
		return $this->isAdmin;
	}

	/**
	 * Charge les dépendances du programme.
	 *
	 * Lorsqu'on crée une nouvelle
	 *
	 * @see RB_Generic::RB_Spectacle_Loader
	 * @see Object::RB_Spectacle_Admin
	 */
	private function load_dependencies()
	{
		// Inclure les fonctions d'administration, si on est loggé en tant qu'admin.
		// Ça va réduire le load.
		if ( $this->isAdmin ) {
			// @ignore-
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rb-spectacle-admin.php';
		}

		// Inclure le loader pour les styles.
		require_once plugin_dir_path( __FILE__ ) . 'class-rb-spectacle-loader.php';

		// Créer le loader.
		$this->loader = new RB_Spectacle_Loader();
	}

	/**
	 * dsadsa
	 */
	private function define_admin_hooks()
	{
		// Créer l'objet qui gère le panneau d'administration.
		$admin = new RB_Spectacle_Admin( $this->get_version() );

		// Ajouter les actions du panneau d'admin à la queue d'action du composant loader.
		$this->loader->push_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
	}

	/**
	 * Exécute les
	 */
	public function run()
	{

	}

	/**
	 * Retourne la version du plugin.
	 *
	 * @see __construct()
	 * @return String La version du plugin.
	 */
	public function get_version()
	{
		return $this->get_version();
	}
}