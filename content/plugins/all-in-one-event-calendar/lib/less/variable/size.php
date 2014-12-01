<?php

/**
 * This class represent a LESS variable of type size.
 *
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @instantiator new
 * @package      AI1EC
 * @subpackage   AI1EC.Less.Variable
 */
class Ai1ec_Less_Variable_Size extends Ai1ec_Less_Variable {

	/**
	 *
	 * @see Ai1ec_Renderable::render()
	 *
	 */
	public function render() {
		$args = array(
			'label' => $this->description,
			'id' => $this->id,
			'value' => $this->value,
			'args' => array(
				'class' => 'input-mini ai1ec-less-variable-size',
				'placeholder' => __( 'Length', AI1EC_PLUGIN_NAME ),
			)
		);
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'theme-options/size.twig', $args, true );
		return $file->get_content();
	}
}
