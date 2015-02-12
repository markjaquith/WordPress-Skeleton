<?php

/**
 * Class RB_Spectacle_Loader
 */
class RB_Spectacle_Loader
{
	/** @var Array */
	protected $actions;
	/** @var Array */
	protected $filters;

	/**
	 * Constructeur. Crée les listes de queues d'actions et de filtres.
	 */
	public function __construct()
	{
		$this->actions = array();
		$this->filtres = array();
	}

	/**
	 * Ajoute une action à la liste.
	 * Note: Les actions ne sont pas encore appliqués à ce stade-là;
	 * @see RB_Spectacle_Loader::run() <<< C'est là qu'ils sont appliqués.
	 *
	 * @param $hook
	 * @param $composant
	 * @param $fnCallback
	 */
	public function push_action( $hook, $composant, $fnCallback )
	{
		$this->actions = self::add( $this->actions, $hook, $composant, $fnCallback );
	}

	/**
	 * Ajoute un filtre dans la table de filtres.
	 * Note: Les filtres ne sont pas encore appliqués à ce stade-là;
	 *
	 * @see RB_Spectacle_Loader::run() <<< C'est là qu'ils sont appliqués.
	 *
	 * @param $hook String L'identifiant de l'action. Exemple: "init"
	 * @param $composant Object Le composant (objet) ayant la fonction à assigner au hook.
	 * @param $fnCallback String La fonction dans la composante qui sera appelée pour le hook.
	 */
	public function push_filter( $hook, $composant, $fnCallback )
	{
		$this->filters = self::add( $this->filters, $hook, $composant, $fnCallback );
	}

	/**
	 * Ajoute un hook.
	 *
	 * N'ayant nul besoin de savoir le type de hook impliqué, cette fonction représente vulgairement
	 * l'immigré mexicain illégal qui prend tes meubles et qui les transporte dans le camion de déménagement!
	 * Note: on va changer cte description-là, assurément.
	 *
	 * @param $hookListe Array La liste d'actions ou de filtres auquel on ajoutera le hook
	 *                         (voir 3 prochains params).
	 * @param $hook String L'identifiant du hook. <u>Exemple pour une action:</u> <em>"init"</em>
	 * @param $composant Object Le composant <u>(objet)</u> ayant la fonction à assigner au hook.
	 * @param $fnCallback String La fonction dans la composante qui sera appelée pour le hook.
	 *
	 * @see RB_Spectacle_Loader::push_action
	 * @see RB_Spectacle_Loader::push_filter
	 * @see add_action
	 * @see add_filter
	 *
	 * @return Array la liste des hooks auquel on a ajouté un hook avec les 3 derniers paramètres.
	 */
	private static function add(array $hookListe, $hook, $composant, $fnCallback )
	{
		// Encapsuler les 3 derniers paramètres dans un array de hooking.
		$encapsulation = array(
			'hook' => $hook,
			'composant' => $composant,
			'fnCallback' => $fnCallback,
		);

		// Pousser les valeurs encapsulées dans la liste de hooks.
		array_push( $hookListe, $encapsulation );

		// Retourner la liste des hooks.
		return $hookListe;
	}

	/**
	 * Ajoutes toutes les actions et tous les filtres qui ont été mis dans les deux listes
	 * afin que leurs contenus soient considérés durant l'exécution du core de Wordpress.
	 */
	public function run()
	{
		// Parcourir l'array de filtres qui ont été assignés à l'avance.
		foreach ( $this->filters as $filterHook ) {
			// Ajouter un filtre.
			add_filter( $filterHook['hook'], array( $filterHook['composant'], $filterHook['fnCallback'] ) );
		}

		// Parcourir l'array d'actions qui ont été assignées à l'avance.
		foreach ( $this->actions as $actionHook ) {
			// Ajouter une action.
			add_action( $actionHook['hook'], array( $actionHook['composant'], $actionHook['fnCallback'] ) );
		}
	}
}