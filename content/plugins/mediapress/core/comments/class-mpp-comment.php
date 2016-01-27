<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class MPP_Comment{
    
    public $id;
    
    public $content;
    
    public $user_id;
    
    public $post_id;
    
    public $user_domain;
    
    public $date_posted;
    
    public $parent_id;
    
}
/**
 * 
 * @param type $comment
 * @return MPP_Comment
 */
function mpp_comment_migrate( $comment ) {
    
    $mpp_comment= new MPP_Comment;
    
    $mpp_comment->id = $comment->comment_ID;
    
    $mpp_comment->content = $comment->comment_content;
    
    $mpp_comment->user_id = $comment->user_id;
    
    $mpp_comment->post_id = $comment->comment_post_ID;
    
    $mpp_comment->user_domain = $comment->comment_author_url;
    
    $mpp_comment->date_posted = $comment->comment_date;
    
    $mpp_comment->parent_id = $comment->comment_parent;
    
    return $mpp_comment;
    
}


