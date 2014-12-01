<?php
/**
 * The class that handles rendering the shortcode.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_View_Calendar_Shortcode extends Ai1ec_Base {

	/**
	 * Generate replacement content for [ai1ec] shortcode.
	 *
	 * @param array	 $atts	  Attributes provided on shortcode
	 * @param string $content Tag internal content (shall be empty)
	 * @param string $tag	  Used tag name (must be 'ai1ec' always)
	 *
	 * @staticvar $call_count Used to restrict to single calendar per page
	 *
	 * @return string Replacement for shortcode entry
	 */
	public function shortcode( $atts, $content = '', $tag = 'ai1ec' ) {
		$settings_view   = $this->_registry->get( 'model.settings-view' );
		$view_names_list = array_keys( $settings_view->get_all() );
		$default_view    = $settings_view->get_default();

		$view_names      = array();
		foreach ( $view_names_list as $view_name ) {
			$view_names[$view_name] = true;
		}

		$view               = $default_view;
		$_events_categories = $_events_tags = $post_ids = array();
		$events_limit       = null;

		if ( isset( $atts['view'] ) ) {
			if ( 'ly' === substr( $atts['view'], -2 ) ) {
				$atts['view'] = substr( $atts['view'], 0, -2 );
			}
			if ( ! isset( $view_names[$atts['view']] ) ) {
				return false;
			}
			$view = $atts['view'];
		}

		$mappings = array(
			'cat_name'     => 'events_categories',
			'cat_id'       => 'events_categories',
			'tag_name'     => 'events_tags',
			'tag_id'       => 'events_tags',
			'post_id'      => 'post_ids',
			'events_limit' => 'events_limit',
		);
		$matches           = array();
		$custom_taxonomies = array();
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $value ) {
				if (
				! preg_match( '/([a-z0-9\_]+)_(id|name)/', $att, $matches ) ||
				isset( $mappings[$matches[1] . '_id'] )
				) {
					continue;
				}
				${'_' . $matches[1] . '_ids'} = array();
				$custom_taxonomies[]    = $matches[1];
			
				if ( ! isset( $mappings[$matches[1] . '_id'] ) ) {
					$mappings[$matches[1] . '_id']   = $matches[1];
				}
				if ( ! isset( $mappings[$matches[1] . '_name'] ) ) {
					$mappings[$matches[1] . '_name'] = $matches[1];
				}
			}
		}
		foreach ( $mappings as $att_name => $type ) {
			if ( ! isset( $atts[$att_name] ) ) {
				continue;
			}
			$raw_values = explode( ',', $atts[$att_name] );
			foreach ( $raw_values as $argument ) {
				if ( 'post_id' === $att_name ) {
					if ( ( $argument = (int)$argument ) > 0 ) {
						$post_ids[] = $argument;
					}
				} else {
					if ( ! is_numeric( $argument ) ) {
						$search_val = trim( $argument );
						$argument   = false;
						foreach ( array( 'name', 'slug' ) as $field ) {
							$record = get_term_by(
								$field,
								$search_val,
								$type
							);
							if ( false !== $record ) {
								$argument = $record;
								break;
							}
						}
						unset( $search_val, $record, $field );
						if ( false === $argument ) {
							continue;
						}
						$argument = (int)$argument->term_id;
					} else {
						if ( ( $argument = (int)$argument ) <= 0 ) {
							continue;
						}
					}
					${'_' . $type}[] = $argument;
				}
			}
		}
		$query = array(
			'ai1ec_cat_ids'	 => implode( ',', $_events_categories ),
			'ai1ec_tag_ids'	 => implode( ',', $_events_tags ),
			'ai1ec_post_ids' => implode( ',', $post_ids ),
			'action'         => $view,
			'request_type'   => 'jsonp',
			'events_limit'   => ( null !== $events_limit )
			// definition above casts values as array, so we take first element,
			// as there won't be others
				? $events_limit[0]
				: null,
		);
		// this is the opposite of how the SuperWidget works.
		if ( ! isset( $atts['display_filters'] ) ) {
			$query['display_filters'] = 'true';
		} else {
			$query['display_filters'] = $atts['display_filters'];
		}

		foreach ( $custom_taxonomies as $taxonomy ) {
			$query['ai1ec_' . $taxonomy . '_ids'] = implode( ',', ${'_' . $taxonomy} );
		}
		if ( isset( $atts['exact_date'] ) ) {
			$query['exact_date'] = $atts['exact_date'];
		}
		$request = $this->_registry->get(
			'http.request.parser',
			$query,
			$default_view
		);
		$request->parse();
		$page_content = $this->_registry->get( 'view.calendar.page' )
			->get_content( $request );
		$css      = $this->_registry->get( 'css.frontend' )
						->add_link_to_html_for_frontend();
		$js       = $this->_registry->get( 'controller.javascript' )
						->load_frontend_js( true );

		return $page_content['html'];
	}

}