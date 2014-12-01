<?php

/**
 * Renderer of settings page html.
 *
 * @author       Time.ly Network, Inc.
 * @instantiator new
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Html
 */
class Ai1ec_Html_Setting_Html extends Ai1ec_Html_Element_Settings {

	/* (non-PHPdoc)
	 * @see Ai1ec_Html_Element_Settings::render()
	 */
	public function render( $output = '' ) {
		$file   = $this->_args['id'] . '.twig';
		$method = 'get_' . $this->_args['id'] . '_args';
		$args   = array();
		if ( method_exists( $this, $method ) ) {
			$args = $this->{$method}();
		}
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'setting/' . $file, $args, true );
		return parent::render( $file->get_content() );
	}

	/*
	 * Get embedding arguments
	 *
	 * @return array
	 */
	protected function get_embedding_args() {
		return array(
			'viewing_events_shortcodes'     => apply_filters( 'ai1ec_viewing_events_shortcodes', null ),
			'text_embed_shortcode'          => __( 'Embed the calendar using a shortcode', AI1EC_PLUGIN_NAME ),
			'text_insert_shortcode'         => __( 'Insert one of these shortcodes into your page body to embed the calendar into any arbitrary WordPress Page:', AI1EC_PLUGIN_NAME ),
			'text_month_view'               => __( 'Month view:', AI1EC_PLUGIN_NAME ),
			'text_week_view'                => __( 'Week view:', AI1EC_PLUGIN_NAME ),
			'text_day_view'                 => __( 'Day view:', AI1EC_PLUGIN_NAME ),
			'text_agenda_view'              => __( 'Agenda view:', AI1EC_PLUGIN_NAME ),
			'text_other_view'               => __( 'Some Other view:', AI1EC_PLUGIN_NAME ),
			'text_default_view'             => __( 'Default view as per settings:', AI1EC_PLUGIN_NAME ),
			'text_general_form'             => __( 'General form:', AI1EC_PLUGIN_NAME ),
			'text_optional'                 => __( 'Optional.', AI1EC_PLUGIN_NAME ),
			'text_filter_label'             => __( 'Add options to display a filtered calender. (You can find out category and tag IDs by inspecting the URL of your filtered calendar page.)', AI1EC_PLUGIN_NAME ),
			'text_filter_category'          => __( 'Filter by event category name/slug:', AI1EC_PLUGIN_NAME ),
			'text_filter_category_1'        => __( 'Holidays', AI1EC_PLUGIN_NAME ),
			'text_filter_category_2'        => __( 'Lunar Cycles', AI1EC_PLUGIN_NAME ),
			'text_filter_category_3'        => __( 'zodiac-date-ranges', AI1EC_PLUGIN_NAME ),
			'text_filter_category_comma'    => __( 'Filter by event category names/slugs (separate names by comma):', AI1EC_PLUGIN_NAME ),
			'text_filter_category_id'       => __( 'Filter by event category ID:', AI1EC_PLUGIN_NAME ),
			'text_filter_category_id_comma' => __( 'Filter by event category IDs (separate IDs by comma):', AI1EC_PLUGIN_NAME ),
			'text_filter_tag'               => __( 'Filter by event tag name/slug:', AI1EC_PLUGIN_NAME ),
			'text_filter_tag_1'             => __( 'tips-and-tricks', AI1EC_PLUGIN_NAME ),
			'text_filter_tag_2'             => __( 'creative writing', AI1EC_PLUGIN_NAME ),
			'text_filter_tag_3'             => __( 'performing arts', AI1EC_PLUGIN_NAME ),
			'text_filter_tag_comma'         => __( 'Filter by event tag names/slugs (separate names by comma):', AI1EC_PLUGIN_NAME ),
			'text_filter_tag_id'            => __( 'Filter by event tag ID:', AI1EC_PLUGIN_NAME ),
			'text_filter_tag_id_comma'      => __( 'Filter by event tag IDs (separate IDs by comma):', AI1EC_PLUGIN_NAME ),
			'text_filter_post_id'           => __( 'Filter by post ID:', AI1EC_PLUGIN_NAME ),
			'text_filter_post_id_comma'     => __( 'Filter by post IDs (separate IDs by comma):', AI1EC_PLUGIN_NAME ),
			'text_events_limit'             => __( 'Limit number of events per page:', AI1EC_PLUGIN_NAME ),
			'text_warning'                  => __( 'Warning:', AI1EC_PLUGIN_NAME ),
			'text_single_calendar'          => __( 'It is currently not supported to embed more than one calendar in the same page. Do not attempt to embed the calendar via shortcode in a page that already displays the calendar.', AI1EC_PLUGIN_NAME ),
		);
	}

}