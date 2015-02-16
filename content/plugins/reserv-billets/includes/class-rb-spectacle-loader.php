<?php

/**
 * RB_Spectacle_Loader
 * ===========
 *
 * Le Loader de l'élément `Spectacle`.
 *
 * @package RB
 */
class RB_Spectacle_Loader
{
	/** @var Array Les actions */
	protected $actions;
	/** @var Array Les filtres */
	protected $filters;

	/**
	 * Constructeur.
	 *
	 * Crée les listes de queues d'actions et de filtres.
	 */
	public function __construct()
	{
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Ajoute une action à la liste.
	 * Note: Les actions ne sont pas encore appliqués à ce stade-là;
	 * @see RB_Spectacle_Loader::run() <<< C'est là qu'ils sont appliqués.
	 *
	 * @param String  $tag         L'identifiant de l'action. Exemple: "init"
	 * @param Mixed   $composant   Le composant (objet) ayant la fonction à assigner au hook.
	 * @param String  $fnCallback  La fonction dans la composante qui sera appelée par le hook.
	 */
	public function queue_action( $tag, $composant, $fnCallback )
	{
		$this->actions = self::add( $this->actions, $tag, $composant, $fnCallback );
	}

	/**
	 * Ajoute un filtre dans la table de filtres.
	 * Note: Les filtres ne sont pas encore appliqués à ce stade-là;
	 *
	 * @see RB_Spectacle_Loader::run() <<< C'est là qu'ils sont appliqués.
	 *
	 * @param String  $tag         L'identifiant du filtre. Exemple: "the_content"
	 * @param Mixed   $composant   Le composant (objet) ayant la fonction à assigner au hook.
	 * @param String  $fnCallback  La fonction dans la composante qui sera appelée pour le hook.
	 */
	public function queue_filter( $tag, $composant, $fnCallback )
	{
		$this->filters = self::add( $this->filters, $tag, $composant, $fnCallback );
	}

	/**
	 * Ajoute un hook dans l'exécution de Wordpress.
	 *
	 * N'ayant nul besoin de savoir le type de hook impliqué, cette fonction représente vulgairement
	 * l'immigré mexicain illégal qui prend tes meubles et qui les transporte dans le camion de déménagement!
	 *
	 * > NOTE DE FÉLIX: <br />
	 * > On va changer cte description-là, assurément.
	 *
	 * @param $hookListe Array La liste d'actions ou de filtres auquel on ajoutera le hook
	 *                         (voir 3 prochains params).
	 * @param String  $tag         L'identifiant du hook. <u>Exemple pour une action:</u> <em>"init"</em>
	 * @param Mixed   $composant   Le composant <u>(objet)</u> ayant la fonction à assigner au hook.
	 * @param String  $fnCallback  La fonction dans la composante qui sera appelée pour le hook.
	 *
	 * @see   RB_Spectacle_Loader::queue_action
	 * @see   RB_Spectacle_Loader::queue_filter
	 * @see   add_action
	 * @see   add_filter
	 *
	 * @return Array                La liste des hooks auquel on a ajouté un hook avec les 3
	 *                 derniers paramètres.
	 */
	private static function add(array $hookListe, $tag, $composant, $fnCallback )
	{
		// Pousser les 3 derniers paramètres dans la liste de hooks.
		$hookListe[] = array(
			'tag' => $tag,
			'composant' => $composant,
			'fnCallback' => $fnCallback,
		);

		// Retourner la liste des hooks.
		return $hookListe;
	}

	/**
	 * Ajoutes toutes les actions et tous les filtres qui ont été mis dans les deux listes
	 * afin que leurs contenus soient considérés durant l'exécution du core de Wordpress.
	 *
	 * @see   RB_Spectacle::run()
	 */
	public function run()
	{
		// Parcourir l'array de filtres qui ont été assignés à l'avance.
		foreach ( $this->filters as $filter ) {
			// Ajouter un filtre à WP.
			add_filter( $filter['tag'], array( $filter['composant'], $filter['fnCallback'] ) );
		}

		// Parcourir l'array d'actions qui ont été assignées à l'avance.
		foreach ( $this->actions as $action ) {
			// Ajouter une action à WP.
			add_action( $action['tag'], array( $action['composant'], $action['fnCallback'] ) );
		}
	}
}