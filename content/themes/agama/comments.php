<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to agama_comment() which is
 * located in the agama-functions.php file.
 *
 * @package Theme-Vision
 * @subpackage Agama
 * @since Agama 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( _n( '<span>%1$s</span> Comment', '<span>%1$s</span> Comments', get_comments_number(), 'agama' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h2>

		<ol class="commentlist clearfix">
			<?php wp_list_comments( array( 'callback' => 'agama_comment', 'style' => 'ol' ) ); ?>
		</ol><!-- .commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation" role="navigation">
			<h1 class="assistive-text section-heading"><?php _e( 'Comment navigation', 'agama' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'agama' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'agama' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php
		/* If there are no comments and comments are closed, let's leave a note.
		 * But we only want the note on posts and pages that had comments in the first place.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php _e( 'Comments are closed.' , 'agama' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php 
	$commenter 	= wp_get_current_commenter();
	$req 		= get_option( 'require_name_email' );
	$aria_req 	= ( $req ? " aria-required='true'" : '' );
	
	$comment_args = array
	( 
		'class_submit'	=> 'button button-3d button-large button-rounded',
		'title_reply' 	=> sprintf( '%s <span>%s</span>', __( 'Leave a', 'agama' ), __( 'Comment', 'agama' ) ),
		'fields' 		=> apply_filters
		( 'comment_form_default_fields', array
			(
				'author' 	=>	'<div class="col-md-4">' . 
								'<label for="author">' . __( 'Name', 'agama' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
								'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" class="sm-form-control"' . $aria_req . ' /></div>',   
				'email'  	=> 	'<div class="col-md-4">' .
								'<label for="email">' . __( 'Email', 'agama' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
								'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" class="sm-form-control"' . $aria_req . ' />'.'</div>',
				'url'    	=> 	'<div class="col-md-4">' .
								'<label for="url">' . __( 'Website', 'agama' ) . '</label>' .
								'<input id="url" name="url" type="text" value="' . esc_url( $commenter['comment_author_url'] ) . '" class="sm-form-control" /></div>',
			) 
		),
		'comment_field' => 	'<div class="col-md-12">' .
							'<label for="comment">' . __( 'Comment', 'agama' ) . '</label>' .
							'<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" class="sm-form-control"></textarea>' .
							'</div>',
		'logged_in_as' 	=> '<div class="col-md-12 logged-in-as">' .
		sprintf
		(	'%s <a href="%s">%s</a>. <a href="%s" title="%s">%s</a>',
			__('Logged in as', 'agama'), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ),
			__('Log out of this account', 'agama'), __('Log out?', 'agama')
		) . '</div>',
		'comment_notes_after' => '<div class="col-md-12" style="margin-top: 15px; margin-bottom: 15px;">' .
		sprintf
		(	'%s <abbr title="HyperText Markup Language">HTML</abbr> %s: %s',
			__( 'You may use these', 'agama' ), __( 'tags and attributes', 'agama' ), '<code>' . allowed_tags() . '</code>'
		) . '</div>',
	);
	
	comment_form( $comment_args ); ?>

</div><!-- #comments .comments-area -->