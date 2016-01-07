<div id="post-<?php the_ID(); ?>" <?php post_class('blog-area-full'); ?>>

		<?php 				
				//hide permalink for fullwidth template
				echo "<div>"; 
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
				echo "<div class='page-description'>";
				the_content();
				echo "</div>"; 	
				// allow support for <!--nextpage-->
				wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'elitepress' ), 'after' => '</div>' ) ); 
				echo "</div>";
				// close div if page is not call
				if(!is_page())  echo "</div>";
		?>
</div>		