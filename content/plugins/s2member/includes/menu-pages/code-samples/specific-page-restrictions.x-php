<?php
if ($pages = get_pages ())
	{
		foreach ($pages as $page)
			{
				if (!is_permitted_by_s2member ($page->ID, "page"))
					continue;
				/* Skip it. The current User/Member
				CANNOT access this particular Page. */
			}
	}
?>