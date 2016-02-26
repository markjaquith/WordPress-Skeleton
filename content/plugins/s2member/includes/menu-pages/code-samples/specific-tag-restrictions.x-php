<?php
if ($tags = get_the_tags ())
	{
		foreach ($tags as $tag)
			{
				if (!is_permitted_by_s2member ($tag->name, "tag"))
					continue;
				/* Skip it. The current User/Member
					CANNOT access this Tag Archive,
				or any Posts/Pages with this Tag. */
			}
	}
?>