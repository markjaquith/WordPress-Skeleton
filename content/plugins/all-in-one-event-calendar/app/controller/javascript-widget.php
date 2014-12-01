<?php

/**
 * Handles Super Widget.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Javascript
 */
class Ai1ec_Controller_Javascript_Widget extends Ai1ec_Base {
	
	const WIDGET_PARAMETER = 'ai1ec_js_widget';

	protected $_widgets = array();
	
	
	public function add_widget( $widget_id, $widget_class ) {
		$this->_widgets[$widget_id] = $widget_class;
	}

	public function get_widgets() {
		return $this->_widgets;
	}

	/**
	 * Adds Super Widget JS to admin screen.
	 *
	 * @param array  $files
	 * @param string $page_to_load
	 *
	 * @return array
	 */
	public function add_js( array $files, $page_to_load ) {
		if ( 'admin_settings.js' === $page_to_load ) {
			$files[] = AI1ECSW_PATH . '/public/js/pages/admin_settings.js';
		}
		return $files;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function add_js_translation( array $data ) {
		$data['set_calendar_page'] = __( 
			'You must choose the Calendar page before using the Super Widget',
			AI1EC_PLUGIN_NAME
		);
		return $data;
	}

	/**
	 * Renders everything that's needed for the embedded widget.
	 */
	public function render_js_widget() {
		if ( isset( $_GET['render'] ) && 'true' === $_GET['render'] ) {
			$widget = $_GET[self::WIDGET_PARAMETER];
			$widget_class = null;
			if ( isset( $this->_widgets[$widget] ) ) {
				$widget_class = $this->_widgets[$widget];
			}
			if ( null === $widget_class ) {
				return;
			}
			$widget_instance = $this->_registry->get( $widget_class );
			$this->render_content( $widget_instance );
		}
		$this->render_javascript();
	}
	
	public function render_javascript() {
		header( 'Content-Type: application/javascript' );
		header(
			'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 31536000 ) . ' GMT'
		);
		header( 'Cache-Control: public, max-age=31536000' );
	
	
		$jscontroller   = $this->_registry->get( 'controller.javascript' );
		$css_controller = $this->_registry->get( 'css.frontend' );
		$require_main   = AI1EC_ADMIN_THEME_JS_PATH . DIRECTORY_SEPARATOR . 'require.js';
		$widget_file    = AI1EC_PATH . '/public/js/widget/common_widget.js';
		$translation    = $jscontroller->get_frontend_translation_data();
		$permalink      = get_permalink(
			$this->_registry->get( 'model.settings' )
			->get( 'calendar_page_id' )
		);
		// load the css to hardcode, saving a call
		$css_rules        = $css_controller->get_compiled_css();
		$css_rules = addslashes( $css_rules );
		$translation['calendar_url'] = $permalink;
		// Let extensions add their scripts.
		// look at Extended Views or Super Widget for examples
		$extension_urls = array();
		$extension_urls = apply_filters(
			'ai1ec_render_js',
			$extension_urls,
			'ai1ec_widget.js'
		);
	
		$translation['extension_urls'] = $extension_urls;
		// the single event page js is loaded dinamically.
		$translation['event_page'] = array(
			'id' => 'ai1ec_event',
			'url' => AI1EC_URL . '/public/js/pages/event.js',
		);
		$translation_module = $jscontroller->create_require_js_module(
			Ai1ec_Javascript_Controller::FRONTEND_CONFIG_MODULE,
			$translation
		);
		// get requirejs
		$require = file_get_contents( $require_main );
		$main_widget = file_get_contents( $widget_file );
		$require_config = $jscontroller->create_require_js_config_object();
		$config         = $jscontroller->create_require_js_module(
			'ai1ec_config',
			$jscontroller->get_translation_data()
		);
		// get jquery
		$jquery = $jscontroller->get_jquery_version_based_on_browser(
			$_SERVER['HTTP_USER_AGENT']
		);
	
		$domready = $jscontroller->get_module(
			'domReady.js'
		);
		$frontend = $jscontroller->get_module(
			'scripts/common_scripts/frontend/common_frontend.js'
		);
	
		// compress data if possible
		$compatibility_ob = $this->_registry->get( 'compatibility.ob' );
		$js = <<<JS
		/******** Called once Require.js has loaded ******/
	
		(function() {
	
			var timely_css = document.createElement( 'style' );
			timely_css.innerHTML = '$css_rules';
			( document.getElementsByTagName( "head" )[0] || document.documentElement ).appendChild( timely_css );
			// bring in requires
			$require
			// make timely global
			window.timely = timely;
			$require_config
			// Load other modules
			$translation_module
			$config
			$jquery
			$frontend

			// start up the widget
			$main_widget
		})(); // We call our anonymous function immediately
JS;
			$compatibility_ob->gzip_if_possible( $js );
		
			exit( 0 );
	}
	
	public function render_content( Ai1ec_Embeddable $widget_instance ) {
		$args = array();
		$defaults = $widget_instance->get_js_widget_configurable_defaults();
		foreach ( $defaults as $id => $value ) {
			if ( isset( $_GET[$id] ) ) {
				$args[$id] = $_GET[$id];
			}
		}
		$html = $widget_instance->javascript_widget( $args );
		$jsonp = $this->_registry->get( 'http.response.render.strategy.jsonp' );
		$jsonp->render(
			array(
				'data' => array( 'html' => $html )
			)
		);
	}

}
