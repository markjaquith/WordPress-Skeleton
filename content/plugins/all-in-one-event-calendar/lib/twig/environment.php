<?php

/**
 * Wrapper for Twig_Environment
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Twig
 */
class Ai1ec_Twig_Environment extends Twig_Environment {

	/**
	 * @var Ai1ec_Registry_Object The registry Object.
	 */
	protected $_registry = null;

	/**
	 * Loads a template by name.
	 *
	 * @param string  $name	 The template name
	 * @param integer $index The index if it is an embedded template
	 *
	 * @return Twig_TemplateInterface A template instance representing the given template name
	 */
	public function loadTemplate( $name, $index = null ) {
		try {
			return parent::loadTemplate( $name, $index );
		} catch ( RuntimeException $excpt ) {
			$message = Ai1ec_I18n::__(
				'We detected that your cache directory (%s) is not writable. This will make your calendar slow. Please contact your web host or server administrator to make it writable by the web server.'
			);
			$message = sprintf( $message, $this->cache );
			$this->_registry->get( 'notification.admin' )
					->store( $message, 'error', 1 );
			$settings = $this->_registry->get( 'model.settings' );
			/* @var $settings Ai1ec_Settings */
			// used shutdown to store settings
			// after this line exception occurs
			$type = ( is_writable( parent::getCache() ) ) ? 'AI1EC_CACHE_UNAVAILABLE' : '';
			$settings->set( 'twig_cache', $type )->persist();
		}
	}

	/**
	 * Set Ai1ec_Registry_Object
	 *
	 * @param Ai1ec_Registry_Object $registry
	 *
	 * @return void
	 */
	public function set_registry( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
	}

}
