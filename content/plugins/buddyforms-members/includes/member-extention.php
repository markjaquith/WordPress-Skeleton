<?php

class BuddyForms_Members_Extention extends BP_Component{

public $id = 'buddyforms';

 	/**
	 * Initiate the class
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	*/
	public function __construct() {
		global $bp;

		parent::start(
			$this->id,
			'BuddyForms',
			BUDDYFORMS_MEMBERS_INSTALL_PATH
		);

		$bp->active_components[$this->id] = '1';
		$this->setup_hooks();
	}

	function setup_hooks() {
		add_action('bp_located_template',	array($this, 'buddyforms_load_template_filter'), 10, 2);
		add_action('wp_enqueue_scripts',	array($this, 'wp_enqueue_style'), 10, 2);
	}

	/**
     * Setup globals
     *
     * @since     Marketplace 0.9
     * @global    object $bp The one true BuddyPress instance
     */
    public function setup_globals($args = Array()) {

        $globals = array(
            'path'          => BUDDYFORMS_MEMBERS_INSTALL_PATH,
            'slug'          => 'buddyforms',
            'has_directory' => false
        );

        parent::setup_globals( $globals );
    }

	/**
	 * Get the user posts count
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	*/
	function get_user_posts_count($user_id, $post_type, $form_slug) {
		global $buddyforms;

		$args['author'] = $user_id;
		$args['post_type'] = $post_type;
        $args['fields'] = 'ids';
		$args['posts_per_page'] = -1;

		if(isset($buddyforms[$form_slug]['list_posts_option']) && $buddyforms[$form_slug]['list_posts_option'] == 'list_all_form'){
			$args['meta_key'] = '_bf_form_slug';
			$args['meta_value'] = $form_slug;
		}

		$post_status_array = bf_get_post_status_array();
		$args['post_status'] = array_keys($post_status_array);

        return count(get_posts($args));

	}

	/**
	 * Setup profile navigation
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	*/
	public function setup_nav( $main_nav = Array(), $sub_nav = Array() ) {
		global $buddyforms, $buddyforms_member_tabs, $bp, $wp_admin_bar, $current_user;

		if(!bp_is_user())
			return;

		get_currentuserinfo();

		$position = 20;

		if (empty($buddyforms))
			return;

		foreach ($buddyforms as $key => $member_form) {
			$position++;

			if (isset($member_form['profiles_integration'])) :

				if (current_user_can('buddyforms_' . $key . '_create') || user_can( bp_displayed_user_id(), 'buddyforms_' . $key . '_create' )) {

					$post_type_object = get_post_type_object($member_form['post_type']);
					$count = $this->get_user_posts_count($bp->displayed_user->id, $member_form['post_type'],$key);

					if (isset($post_type_object->labels->name))
						$name = $post_type_object->labels->name;

					if (isset($member_form['name']))
						$name = $member_form['name'];

					$parent_tab = buddyforms_members_parent_tab($member_form);

					if ( $parent_tab  ) {

						$buddyforms_member_tabs[$parent_tab][$member_form['slug']] = $key;
						$parent_tab_name = $name;

						if (isset($member_form['profiles_parent_tab'])
								&& isset($member_form['attached_page'])
								&& isset($parent_tab)){
							$attached_page = $member_form['attached_page'];
							$parent_tab_page = get_post($attached_page, 'OBJECT');
							$parent_tab_name = $parent_tab_page->post_title;
						}

						if (!array_key_exists($parent_tab, (array)$bp->bp_nav)) {
							$main_nav = array(
									'name' => sprintf('%s <span>%d</span>', $parent_tab_name, $count),
									'slug' => $parent_tab,
									'position' => $position,
									'default_subnav_slug' => $key . '-my-posts-all',
							);
							$buddyforms_member_tabs[$parent_tab][$key . '-my-posts-all'] = $key;
						}

						$sub_nav[] = array(
								'name' => sprintf(__(' All %s', 'buddyforms'), $parent_tab_name),
								'slug' => $key . '-my-posts-all',
								'parent_slug' => $parent_tab,
								'parent_url' => trailingslashit(bp_loggedin_user_domain() . $parent_tab),
								'item_css_id' => 'sub_nav_home',
								'screen_function' => array($this, 'buddyforms_screen_settings'),
						);
						$buddyforms_member_tabs[$parent_tab][$key . '-my-posts-all'] = $key;

						$sub_nav[] = array(
								'name' => sprintf(__(' My %s', 'buddyforms'), $name),
								'slug' => $key . '-my-posts',
								'parent_slug' => $parent_tab,
								'parent_url' => trailingslashit(bp_loggedin_user_domain() . $parent_tab),
								'item_css_id' => 'my-posts',
								'screen_function' => array($this, 'buddyforms_screen_settings'),
						);
						$buddyforms_member_tabs[$parent_tab][$key . '-my-posts'] = $key;

						$sub_nav[] = array(
								'name' => sprintf(__(' Add %s', 'buddyforms'), $member_form['singular_name']),
								'slug' => $key . '-create',
								'parent_slug' => $parent_tab,
								'parent_url' => trailingslashit(bp_loggedin_user_domain() . $parent_tab),
								'item_css_id' => 'apps_sub_nav',
								'screen_function' => array($this, 'load_members_post_create'),
								'user_has_access' => bp_is_my_profile()
						);
						$buddyforms_member_tabs[$parent_tab][$key . '-create'] = $key;

						$sub_nav[] = array(
								'name' => sprintf(__(' Edit %s', 'buddyforms'), $member_form['singular_name']),
								'slug' => $key . '-edit',
								'parent_slug' => $parent_tab,
								'parent_url' => trailingslashit(bp_loggedin_user_domain() . $parent_tab),
								'item_css_id' => 'sub_nav_edit',
								'screen_function' => array($this, 'buddyforms_screen_settings'),
								'user_has_access' => bp_is_my_profile()
						);
						$buddyforms_member_tabs[$parent_tab][$key . '-edit'] = $key;

						$sub_nav[] = array(
								'name' => sprintf(__(' Revision %s', 'buddyforms'), $member_form['singular_name']),
								'slug' => $key . '-revision',
								'parent_slug' => $parent_tab,
								'parent_url' => trailingslashit(bp_loggedin_user_domain() . $parent_tab),
								'item_css_id' => 'sub_nav_edit',
								'screen_function' => array($this, 'buddyforms_screen_settings'),
								'user_has_access' => bp_is_my_profile(),
						);
						$buddyforms_member_tabs[$parent_tab][$key . '-revision'] = $key;

						$sub_nav[] = array(
								'name' => sprintf(__(' Page %s', 'buddyforms'), $member_form['singular_name']),
								'slug' => $key . '-page',
								'parent_slug' => $parent_tab,
								'parent_url' => trailingslashit(bp_loggedin_user_domain() . $parent_tab),
								'item_css_id' => 'sub_nav_edit',
								'screen_function' => array($this, 'buddyforms_screen_settings'),
						);
						$buddyforms_member_tabs[$parent_tab][$key . '-page'] = $key;
					}
					if ($current_user->ID != bp_displayed_user_id()) {
						parent::setup_nav($main_nav, $sub_nav);
					} elseif (current_user_can('buddyforms_' . $key . '_create')) {
						parent::setup_nav($main_nav, $sub_nav);
					}

				}

		endif;
		}
	}

	/**
	 * Display the posts or the edit screen
	 *
	 * @package BuddyForms
	 * @since 0.2 beta
	*/
	public function buddyforms_screen_settings() {
		global $bp, $buddyforms, $buddyforms_member_tabs;

		$form_slug = $buddyforms_member_tabs[$bp->current_component][$bp->current_action];

		if($bp->current_action == $form_slug . '-my-posts-all'){
			if ( bp_is_my_profile() ) {
				$url = bp_loggedin_user_domain();
			} else {
				$url = bp_displayed_user_domain();
			}
			wp_redirect(trailingslashit($url . $bp->current_component .'/'. $form_slug . '-my-posts'));
			exit;
		}

		if($bp->current_action ==  $form_slug . '-my-posts' )
			bp_core_load_template('buddyforms/members/members-post-display');

		if($bp->current_action ==  $form_slug . '-page' )
			bp_core_load_template('buddyforms/members/members-post-display');

		if($bp->current_action == $form_slug . '-create' )
			bp_core_load_template('buddyforms/members/members-post-create');

		if($bp->current_action == $form_slug . '-edit' )
			bp_core_load_template('buddyforms/members/members-post-create');

		if($bp->current_action == $form_slug . '-revision')
			bp_core_load_template('buddyforms/members/members-post-create');


	}

	/**
	 * Show the post create form
	 *
	 * @package BuddyForms
	 * @since 0.2 beta
	*/
	public function load_members_post_create() {
		bp_core_load_template('buddyforms/members/members-post-create');
	}

	/**
	 * BuddyForms template loader.
	 *
	 * I copied this function from the buddypress.org website and modified it for my needs.
	 *
	 * This function sets up BuddyForms to use custom templates.
	 *
	 * If a template does not exist in the current theme, we will use our own
	 * bundled templates.
	 *
	 * We're doing two things here:
	 *  1) Support the older template format for themes that are using them
	 *     for backwards-compatibility (the template passed in
	 *     {@link bp_core_load_template()}).
	 *  2) Route older template names to use our new template locations and
	 *     format.
	 *
	 * View the inline doc for more details.
	 *
	 * @since 1.0
	 */
	function buddyforms_load_template_filter($found_template, $templates) {
	global $bp, $buddyforms, $buddyforms_member_tabs;

		$form_slug = $buddyforms_member_tabs[$bp->current_component][$bp->current_action];

		if(!bp_current_component())
            return apply_filters('buddyforms_members_load_template_filter', $found_template);

			if (is_array($templates) && is_array($buddyforms) && empty($found_template) && isset($buddyforms) && array_key_exists($form_slug,$buddyforms)) {

				// register our theme compat directory
				//
				// this tells BP to look for templates in our plugin directory last
				// when the template isn't found in the parent / child theme
				bp_register_template_stack('buddyforms_members_get_template_directory', 14);

				// locate_template() will attempt to find the plugins.php template in the
				// child and parent theme and return the located template when found
				//
				// plugins.php is the preferred template to use, since all we'd need to do is
				// inject our content into BP
				//
				// note: this is only really relevant for bp-default themes as theme compat
				// will kick in on its own when this template isn't found
				$found_template = locate_template('members/single/plugins.php', false, false);

				// add our hook to inject content into BP
				if ($bp->current_action == $form_slug . '-my-posts' ) {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				"));
				} elseif ($bp->current_action == $form_slug . '-create') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				"));
				} elseif ($bp->current_action == $form_slug . '-edit' ) {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				"));
				} elseif ($bp->current_action == $form_slug . '-revision') {
                    add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				"));
                } elseif ($bp->current_action == $form_slug . '-page') {
                    add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				"));
                }
			}



		return apply_filters('buddyforms_members_load_template_filter', $found_template);
	}

	function wp_enqueue_style(){
    	wp_enqueue_style('member-profile-css', plugins_url('css/member-profile.css', __FILE__));

	}

}

function buddyforms_members_parent_tab($member_form){

	$parent_tab_name = $member_form['slug'];

	if (isset($member_form['profiles_parent_tab']))
		$parent_tab = $member_form['profiles_parent_tab'];

	if (isset($member_form['attached_page']) && isset($parent_tab)){
		$attached_page = $member_form['attached_page'];
		$parent_tab_page = get_post($attached_page, 'OBJECT');
		$parent_tab_name = $parent_tab_page->post_name;
	}
	return $parent_tab_name;
}
?>
