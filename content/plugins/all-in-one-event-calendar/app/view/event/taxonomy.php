<?php

/**
 * This class renders the html for the event taxonomy.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View.Event
 */
class Ai1ec_View_Event_Taxonomy extends Ai1ec_Base {

	/**
	 * @var Ai1ec_Taxonomy Taxonomy abstraction layer.
	 */
	protected $_taxonomy_model = null;

	/**
	 * Style attribute for event category
	 */
	public function get_color_style( Ai1ec_Event $event ) {
		static $color_styles = array();
		$categories = $this->_taxonomy_model->get_post_categories(
			$event->get( 'post_id' )
		);
		// No specific styling for events not in categories.
		if ( ! $categories ) {
			return '';
		}

		$type = $event->is_allday() || $event->is_multiday();
		// If not yet cached, fetch and save style.
		if ( ! isset( $color_styles[$categories[0]->term_id][$type] ) ) {
			$color_styles[$categories[0]->term_id][$type] =
				$this->get_event_category_color_style(
					$categories[0]->term_id,
					$type
				);
		}

		return $color_styles[$categories[0]->term_id][$type];
	}

	/**
	 * Returns the style attribute assigning the category color style to an event.
	 *
	 * @param int  $term_id Term ID of event category
	 * @param bool $allday  Whether the event is all-day
	 *
	 * @return string
	 */
	public function get_event_category_color_style(
		$term_id,
		$allday = false
	) {
		$color = $this->_taxonomy_model->get_category_color( $term_id );
		if ( ! is_null( $color ) && ! empty( $color ) ) {
			if ( $allday )
				return 'background-color: ' . $color . ';';
			else
				return 'color: ' . $color . ' !important;';
		}
		return '';
	}

	/**
	 * HTML of category color boxes for this event
	 */
	public function get_category_colors( Ai1ec_Event $event ) {
		static $category_colors = array();
		$id = $event->get( 'post_id' );
		if ( ! isset( $category_colors[$id] ) ) {
			$categories           = $this->_taxonomy_model
				->get_post_categories( $id );
			$category_colors[$id] = '';
			if ( false !== $categories ) {
				$category_colors[$id] = $this->get_event_category_colors(
					$categories
				);
			}
		}
		return $category_colors[$id];
	}

	/**
	 * Returns the HTML markup for the category color square.
	 *
	 * @param int $term_id The term ID of event category
	 *
	 * @return string
	 */
	public function get_category_color_square( $term_id ) {
		$taxonomy = $this->_registry->get( 'model.taxonomy' );
		$color = $taxonomy->get_category_color( $term_id );
		if ( null !== $color ) {
			$cat = get_term( $term_id, 'events_categories' );
			return '<span class="ai1ec-color-swatch ai1ec-tooltip-trigger" ' .
				'style="background:' . $color . '" title="' .
				esc_attr( $cat->name ) . '"></span>';
		}
		return '';
	}

	/**
	 * Returns the HTML markup for the category image square.
	 *
	 * @param int $term_id The term ID of event category.
	 *
	 * @return string HTML snippet to use for category image.
	 */
	public function get_category_image_square( $term_id ) {
		$image = $this->_registry->get( 'model.taxonomy' )
			->get_category_image( $term_id );
		if ( null !== $image ) {
			return '<img src="' . $image . '" alt="' .
				Ai1ec_I18n::__( 'Category image' ) .
				'" class="ai1ec_category_small_image_preview" />';
		}
		return '';
	}

	/**
	 * Returns category color squares for the list of Event Category objects.
	 *
	 * @param array $cats The Event Category objects as returned by get_terms()
	 *
	 * @return string
	 */
	public function get_event_category_colors( array $cats ) {
		$sqrs = '';
		foreach ( $cats as $cat ) {
			$tmp = $this->get_category_color_square( $cat->term_id );
			if ( ! empty( $tmp ) ) {
				$sqrs .= $tmp;
			}
		}
		return $sqrs;
	}

	/**
	 * Style attribute for event bg color
	 */
	public function get_category_bg_color( Ai1ec_Event $event ) {
		$category_bg_color = null;
		$categories        = $this->_taxonomy_model->get_post_categories(
			$event->get( 'post_id' )
		);
		if ( ! empty( $categories ) ) {
			$category_bg_color = $this
				->get_event_category_bg_color(
					$categories[0]->term_id,
					$event->is_allday() || $event->is_multiday()
				);
		}
		return $category_bg_color;
	}

	/**
	 * Style attribute for event bg color
	 */
	public function get_category_text_color( Ai1ec_Event $event ) {
		$category_text_color = null;
		$categories          = $this->_taxonomy_model->get_post_categories(
			$event->get( 'post_id' )
		);
		if ( ! empty( $categories ) ) {
			$category_text_color = $this
				->get_event_category_text_color(
					$categories[0]->term_id,
					$event->is_allday() || $event->is_multiday()
				);
		}
		return $category_text_color;
	}

	/**
	 * get_event_text_color function
	 *
	 * Returns the style attribute assigning the category color style to an event.
	 *
	 * @param int $term_id The Event Category's term ID
	 * @param bool $allday Whether the event is all-day
	 * @return string
	 **/
	public function get_event_category_text_color( $term_id ) {
		$taxonomy = $this->_registry->get( 'model.taxonomy' );
		$color    = $taxonomy->get_category_color(
			$term_id
		);
		if ( ! empty( $color ) ) {
			return 'style="color: ' . $color . ';"';
		}
		return '';
	}

	/**
	 * get_event_category_bg_color function
	 *
	 * Returns the style attribute assigning the category color style to an event.
	 *
	 * @param int $term_id The Event Category's term ID
	 * @param bool $allday Whether the event is all-day
	 * @return string
	 **/
	public function get_event_category_bg_color( $term_id ) {
		$taxonomy = $this->_registry->get( 'model.taxonomy' );
		$color    = $taxonomy->get_category_color(
			$term_id
		);
		if ( ! empty( $color ) ) {
			return 'style="background-color: ' . $color . ';"';
		}
		return '';
	}

	/**
	 * Categories as HTML, either as blocks or inline.
	 *
	 * @param Ai1ec_Event $event  Rendered Event.
	 * @param string      $format Return 'blocks' or 'inline' formatted result.
	 *
	 * @return string String of HTML for category blocks.
	 */
	public function get_categories_html(
		Ai1ec_Event $event,
		$format = 'blocks'
	) {
		$categories = $this->_taxonomy_model->get_post_categories(
			$event->get( 'post_id' )
		);
		foreach ( $categories as &$category ) {
			$href = $this->_registry->get(
				'html.element.href',
				array( 'cat_ids' => $category->term_id )
			);

			$class = $data_type = $title = '';
			if ( $category->description ) {
				$title = 'title="' .
					esc_attr( $category->description ) . '" ';
			}

			$html        = '';
			$class      .= ' ai1ec-category';
			$color_style = '';
			if ( $format === 'inline' ) {
				$taxonomy = $this->_registry->get( 'model.taxonomy' );
				$color_style = $taxonomy->get_category_color(
					$category->term_id
				);
				if ( $color_style !== '' ) {
					$color_style = 'style="color: ' . $color_style . ';" ';
				}
				$class .= '-inline';
			}

			$html .= '<a ' . $data_type . ' class="' . $class .
			' ai1ec-term-id-' . $category->term_id . ' p-category" ' .
			$title . $color_style . 'href="' . $href->generate_href() . '">';

			if ( $format === 'blocks' ) {
				$html .= $this->get_category_color_square(
					$category->term_id
				) . ' ';
			} else {
				$html .=
				'<i ' . $color_style .
					'class="ai1ec-fa ai1ec-fa-folder-open"></i>';
			}

			$html .= esc_html( $category->name ) . '</a>';
			$category = $html;
		}
		return implode( ' ', $categories );
	}

	/**
	 * Tags as HTML
	 */
	public function get_tags_html( Ai1ec_Event $event ) {
		$tags = $this->_taxonomy_model->get_post_tags(
			$event->get( 'post_id' )
		);
		if ( ! $tags ) {
			$tags = array();
		}
		foreach ( $tags as &$tag ) {
			$href = $this->_registry->get(
				'html.element.href',
				array( 'tag_ids' => $tag->term_id )
			);
			$class = '';
			$data_type = '';
			$title = '';
			if ( $tag->description ) {
				$title = 'title="' . esc_attr( $tag->description ) . '" ';
			}
			$tag = '<a ' . $data_type . ' class="ai1ec-tag ' . $class .
				' ai1ec-term-id-' . $tag->term_id . '" ' . $title .
				'href="' . $href->generate_href() . '">' .
				'<i class="ai1ec-fa ai1ec-fa-tag"></i>' .
				esc_html( $tag->name ) . '</a>';
		}
		return implode( ' ', $tags );
	}

	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$this->_taxonomy_model = $this->_registry->get( 'model.taxonomy' );
	}

}
