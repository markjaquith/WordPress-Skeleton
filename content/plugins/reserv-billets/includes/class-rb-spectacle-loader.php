<?php
/**
 * class-rb-spectacles-loader.php
 *
 * Project: wp-production2
 * User:    Félix Dion Robidoux
 * Date:    11/02/2015
 * Time:    4:20 PM
 */

class RB_Spectacle_Loader
{
	/** @var $actions Array */
	protected $actions;
	/** @var  Array */
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
		$this->actions = $this->add( $this->actions, $hook, $composant, $fnCallback);
	}

	/**
	 * Ajoute un filtre dans la table de filtres.
	 * Note: Les filtres ne sont pas encore appliqués à ce stade-là;
	 * @see RB_Spectacle_Loader::run() <<< C'est là qu'ils sont appliqués.
	 *
	 * @param $hook String L'identifiant de l'action. Exemple: "init"
	 * @param $composant Object Le composant (objet) ayant la fonction à assigner au hook.
	 * @param $fnCallback String La fonction dans la composante qui sera appelée pour le hook.
	 */
	public function push_filter( $hook, $composant, $fnCallback )
	{
		$this->filters = $this->add( $this->filters, $hook, $composant, $fnCallback);
	}

	/**
	 * Ajoute un hook dans la liste de hooks spécifiée en paramètres.
	 *
	 * @param $hookListe Array La liste d'actions ou de filtres auquel on ajoutera le hook
	 *                         (voir 3 prochains params).
	 * @param $hook String L'identifiant du hook. Exemple pour une action: "init"
	 * @param $composant Object Le composant (objet) ayant la fonction à assigner au hook.
	 * @param $fnCallback String La fonction dans la composante qui sera appelée pour le hook.
	 *
	 * @see add_action, add_filter
	 *
	 * @return Array la liste des hooks auquel on a ajouté un hook avec les 3 derniers paramètres.
	 */
	private function add( $hookListe, $hook, $composant, $fnCallback )
	{
		// Encapsuler les 3 derniers paramètres dans un array de hooking.
		$encapsulation = array(
			"hook" => $hook,
			"composant" => $composant,
			"fnCallback" => $fnCallback
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
		foreach( $this->filters as $filterHook )
		{
			// Ajouter un filtre.
			add_filter( $filterHook['hook'], array( $filterHook["composant"], $filterHook["fnCallback"] ));
		}

		// Parcourir l'array d'actions qui ont été assignées à l'avance.
		foreach( $this->actions as $actionHook )
		{
			// Ajouter une action.
			add_action( $actionHook["hook"], array( $actionHook["composant"], $actionHook["fnCallback"] ));
		}
	}
}