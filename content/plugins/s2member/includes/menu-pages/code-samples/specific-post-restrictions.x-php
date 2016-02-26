<?php
if ($posts = get_posts ())
	{
		foreach ($posts as $post)
			{
				if (!is_permitted_by_s2member ($post->ID, "post"))
					continue;
				/* Skip it. The current User/Member
				CANNOT access this particular Post. */

				$post_or_page_id = $post->ID;
				if (!is_permitted_by_s2member ($post_or_page_id, "singular"))
					continue;
				/* The "singular" attribute can check both Pages and Posts the same time.
				So if this was actually a "Page", that would be valid, w/ "singular". */
			}
	}
?>