<?php
if ($categories = get_categories ())
	{
		foreach ($categories as $category)
			{
				if (!is_permitted_by_s2member ($category->cat_ID, "category"))
					continue;
				/* Skip it. The current User/Member CANNOT access this Category,
				or any Posts inside this Category, or any of its sub-Categories. */
			}
	}
?>