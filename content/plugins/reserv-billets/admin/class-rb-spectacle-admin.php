<?php

/**
 * Le gestionnaire de tout ce qui a rapport avec le panneau administration
 * et l'entité « Spectacle ».
 *
 * @since Le début début.
 */
class RB_Spectacle_Admin
{
	/** @var  String Le numéro de version.
	 * <h3 style="color: white"><u>NOTE DE FÉLIX</u></h3>
	 * J'sais fuckall pourquoi on a ça, mais posons pas
	 * trop de questions là-dessus pour le moment.
	 */
	protected $version;

	/**
	 * Constructeur. 'Nuff said.
	 * @param $version String
	 */
	public function __construct( $version )
	{
		$this->version = $version;
	}

	public function enqueue_styles()
	{

	}

	public function add_meta_box()
	{

	}
}