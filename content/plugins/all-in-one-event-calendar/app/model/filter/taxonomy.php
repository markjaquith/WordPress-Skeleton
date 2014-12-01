<?php

/**
 * Base class for taxonomies filtering.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Filter
 */
abstract class Ai1ec_Filter_Taxonomy extends Ai1ec_Filter_Int {

	/**
	 * @var Ai1ec_Dbi Instance of database interface.
	 */
	protected $_dbi = null;

	/**
	 * Sanitize input values upon construction.
	 *
	 * @param Ai1ec_Registry_Object $registry      Injected registry.
	 * @param array                 $filter_values Values to sanitize.
	 *
	 * @return void
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		array $filter_values = array()
	) {
		parent::__construct( $registry, $filter_values );
		$this->_dbi = $this->_registry->get( 'dbi.dbi' );
	}

	/**
	 * Build SQL snippet for `FROM` particle.
	 *
	 * @return string Valid SQL snippet.
	 */
	public function get_join() {
		if ( empty( $this->_values ) ) {
			return '';
		}
		$sql_query =
			'LEFT JOIN `{{RELATIONSHIPS_TABLE}}` AS `{{RELATIONSHIP_ALIAS}}` ' .
			    'ON ( `e` . `post_id` = `{{RELATIONSHIP_ALIAS}}` . `object_id` ) ' .
			'LEFT JOIN `{{TAXONOMY_TABLE}}` AS `{{TAXONOMY_ALIAS}}` ' .
			    'ON (' .
			        '`{{RELATIONSHIP_ALIAS}}` . `term_taxonomy_id` = ' .
			            '`{{TAXONOMY_ALIAS}}` . `term_taxonomy_id` ' .
			        'AND `{{TAXONOMY_ALIAS}}` . taxonomy = {{TAXONOMY}} ' .
			    ')';
		return str_replace(
			array(
				'{{RELATIONSHIPS_TABLE}}',
				'{{RELATIONSHIP_ALIAS}}',
				'{{TAXONOMY_TABLE}}',
				'{{TAXONOMY_ALIAS}}',
				'{{TAXONOMY}}',
			),
			array(
				$this->_dbi->get_table_name( 'term_relationships' ),
				$this->_table_alias( 'term_relationships' ),
				$this->_dbi->get_table_name( 'term_taxonomy' ),
				$this->_table_alias( 'term_taxonomy' ),
				'\'' . addslashes( $this->get_taxonomy() ) . '\'',
			),
			$sql_query
		);
	}

	/**
	 * Required by parent class. Using internal abstractions.
	 *
	 * @return string Field name to use in `WHERE` particle.
	 */
	public function get_field() {
		return $this->_table_alias( 'term_taxonomy' ) . '.term_id';
	}

	/**
	 * Return the qualified name for the taxonomy.
	 *
	 * @return string Valid taxonomy name (see `term_taxonomy` table).
	 */
	abstract public function get_taxonomy();

	/**
	 * Generate table alias given taxonomy.
	 *
	 * @param string $table Table to generate alias for.
	 *
	 * @return string Table alias.
	 */
	protected function _table_alias( $table ) {
		return $table . '_' . $this->get_taxonomy();
	}

}