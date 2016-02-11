<?php
/**
 * A set of filter functions which handle the behavior of the roles column in
 * user list tables.
 */
class MDMR_Column_Controller {

	/**
	 * The model object.
	 *
	 * @var object
	 */
	var $model;

	/**
	 * Constructor. Define properties.
	 *
	 * @param object $model The model object.
	 */
	public function __construct( $model ) {
		$this->model = $model;
	}

	/**
	 * Remove the default role column and replace it with a custom version.
	 *
	 * @param array $columns Existing columns in name => label pairs.
	 * @return array An updated list of columns.
	 */
	public function replace_column( $columns ) {
		unset( $columns['role'] );
		$columns['md_multiple_roles_column'] = 'Roles';
		return $columns;
	}

	/**
	 * Output the content of the Roles column.
	 *
	 * @param string $output The existing HTML to display. Should be empty.
	 * @param string $column The name of the current column.
	 * @param int $user_id The user ID whose roles are about to be displayed.
	 * @return string The new HTML output.
	 */
	public function output_column_content( $output, $column, $user_id ) {

		if ( $column !== 'md_multiple_roles_column' )
			return $output;

		$roles = $this->model->get_user_roles( $user_id );

		ob_start();
		include( MDMR_PATH . 'views/column.html.php' );
		return ob_get_clean();

	}

}