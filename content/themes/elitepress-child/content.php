<div id="post-<?php the_ID(); ?>" <?php post_class('blog-area-full'); ?>>
	<div class="blog-post-img">
		<?php 
				// Check Image size for fullwidth template
				if( is_page_template('blog-full-width.php'))
				elitepress_image_thumbnail('','img-responsive'); 
				
				
				// Check Image size for Different format like Single post,page
				elseif(is_single() || is_page())
				elitepress_post_thumbnail('','img-responsive');
				
				else
				elitepress_post_thumbnail('','img-responsive');	
		
				// Close div if page is call
				if(is_page() )  echo "</div>";
				
				//hide permalink for fullwidth template
				echo "<div class='blog-info'>"; 
				if( !is_page_template('fullwidth.php') && get_the_title() && !is_page() ) { ?>
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<div class="blog-seprator"></div>
		<?php   } ?>
		<?php
				if(is_page() )
				// call post if any page is call 
				{
				the_post();
				}
				else 
				{
				// call post related meta contant like posted by, author name and comment
				elitepress_post_meta_content();
				}
			
                // call editor content of post/page	
				echo "<div class='blog-description'>";
				the_content();
				echo "</div>"; 	
				// allow support for <!--nextpage-->
				wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'elitepress' ), 'after' => '</div>' ) ); 
				echo "</div>";
				// close div if page is not call
				if(!is_page())  echo "</div>";
		?>
</div>		