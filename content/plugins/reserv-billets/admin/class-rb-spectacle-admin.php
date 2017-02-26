<?php

/**
 * RB_Spectacle_Admin
 * ===========
 *
 * Le gestionnaire de tout ce qui a rapport avec le panneau administration
 * et l'entité « Spectacle ».
 *
 * @package RB
 * @see RB_Spectacle::define_admin_hooks()
 */
class RB_Spectacle_Admin
{
	/** @var String Le numéro de version du plugin. */
	protected $version;

	/**
	 * Constructeur. 'Nuff said.
	 *
	 * @param String $version Le numéro de version du plugin.
	 */
	public function __construct( $version )
	{
		$this->version = $version;
	}

	/**
	 * Pousse toutes les feuilles de styles requises du plugin pour le panneau d'administration.
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style(
			'rb-spectacle-admin',   // Le nom de la feuille de style.
			plugin_dir_url( __FILE__ ) . 'css/rb-spectacle-metabox.css', // Source
			array(),                /** Dépendances des handles de style.
                                     * @see WP_Dependencies::add() */
			$this->version,         // Version
			FALSE                   // Media query specification
		);

		// TODO faire un wp_dequeue_style durant la désactivation.
	}

	/**
	 * Crée des metabox pour le panneau d'administration.
	 */
	public function add_meta_box()
	{
		$metabox_title = '<h1>Billets pour le Spectacle '
		                 .'<span class="dashicons dashicons-tickets-alt icone-billets">'
		                 .'</span></h1>';

		add_meta_box(
			'rb-spectacle-admin',        // valeur de l'attribut « id » dans la balise.
			$metabox_title, // Titre.
			array( $this, 'render_meta_box' ), // Callback qui va echo l'affichage.
			'spectacle',                 // L'écran où est affiché le meta-box.
			'normal',                    // Le contexte. ex. "side", "normal" ou "advanced".
			'core'                       // La priorité.
		);

		// TODO faire un remove_meta_box() durant la désactivation.
	}

	/**
	 * Crée le post type `Spectacle`
	 */
	public function create_post_type()
	{
		$labels = array(
			'name'                => _x( 'Spectacles', 'Post Type General Name', '/langage' ),
			'singular_name'       => _x( 'Spectacle', 'Post Type Singular Name', '/langage' ),
			'menu_name'           => __( 'Spectacle', '/langage' ),
			'parent_item_colon'   => __( 'Parent', '/langage' ),
			'all_items'           => __( 'Tous les Spectacles', '/langage' ),
			'view_item'           => __( 'Voir les infos du Spectacle', '/langage' ),
			'add_new_item'        => __( 'Ajouter un Spectacle', '/langage' ),
			'add_new'             => __( 'Ajouter', '/langage' ),
			'edit_item'           => __( 'Éditer les infos du Spectacle', '/langage' ),
			'update_item'         => __( 'Mettre à jour les infos du Spectacle', '/langage' ),
			'search_items'        => __( 'Chercher un Spectacle', '/langage' ),
			'not_found'           => __( 'Non-trouvé', '/langage' ),
			'not_found_in_trash'  => __( 'Non-trouvé dans la corbeille', '/langage' ),
		);
		$rewrite = array(
			'slug'                => 'spectacle',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'spectacle', '/langage' ),
			'description'         => __( 'Un spectacle.', '/langage' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', ),
			'taxonomies'          => array( 'category', 'post_tag' ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-tickets-alt',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type( 'spectacle', $args );
	}

	/**
	 * Vérifie si la metadata est celle des billets,
	 * et si oui, éviter la marde.
	 *
	 * @param null   $null       Toujours null. Demandez-moi pas pourquoi, jsais pô!
	 * @param int    $object_id  L'ID de l'objet metadata.
	 * @param string $meta_key   La clé de la metadata.
	 * @param mixed  $meta_value La valeur courante de la metadata.
	 * @param mixed  $prev_value La valeur précédente de la metadata.
	 *
	 * @return bool|int|null     BOOLEAN si la valeur est pas valide.
	 *                           INT     si on doit la changer manuellement.
	 *                           NULL    si la valeur entrée est correcte.
	 */
	public function update_spectacle_nb_billets( $null = null, $object_id, $meta_key, $meta_value, $prev_value )
	{
		var_dump($meta_key);

		if ( $meta_key == "nb_billets" && empty( $meta_value ) )
		{
			return true;
		}

		return null;
	}

	/**
	 * Effectue le rendu de la metabox.
	 */
	public function render_meta_box()
	{
		/** @noinspection PhpIncludeInspection */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/rb-spectacle-metabox.php';
	}
}