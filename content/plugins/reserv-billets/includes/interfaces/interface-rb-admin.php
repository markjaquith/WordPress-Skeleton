<?php
/**
 * Interface
 */
interface RB_Admin
{
	public function __construct( $version );

	public function enqueue_styles();

	public function add_meta_box();
}