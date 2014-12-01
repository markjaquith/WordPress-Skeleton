<?php


/* -------------------------------------------------------------------------------------------------------------*/
function amr_expand_all ($atts) {
	$html = '<div id="calendar_toggle"><a href="#" id="expandall" >'.__('Show all', 'amr-ical-events-list').'</a>' 
	.' <a href="#" id="hideall" class="inactive" >'.__('Hide all', 'amr-ical-events-list').'</a></div>';
	return($html);
}
/* --------------------------------------------------  */
function amr_list_event_subset ($htm, $columns, $subset, $events) { // now a flat array of ids
	$html = '';
	$alt = false;
	if ((!is_array($events)) or (count($events) < 1 )) return ('');
	if ((!is_array($subset)) or (count($subset) < 1 )) return ('');
	$no_cols = count($columns);
//	foreach ($events as $i => $e) { /* for each event, loop through the properties and see if we should display */
	foreach ($subset as $i => $eventindex) { /* for each event, loop through the properties and see if we should display */
		$e = $events[$eventindex];
		amr_derive_component_further ($e);

		if (!empty($e['Classes']))
			$classes = strtolower($e['Classes']);
		else $classes = '';
		$eprop = ''; /*  each event on a new list */
		$prevcol = 0;
		$colcount = 0;
		$col = 1; /* reset where we are with columns */

		$rowhtml = '';
		foreach ($columns as $col => $order) {  // prepare the row
			$eprop = '';
			foreach ($order as $k => $kv) { /* ie for one column in event, check how to order the bits  */
				
				/* Now check if we should print the component or not, we may have an array of empty string */
				if (isset($e[$k])) {
					$v = amr_check_flatten_array ($e[$k]); // *** huh? shoudl we do this here?
					
					}
				else
					$v =null;

				$selector = $htm['li'];
				$selectorend = $htm['lic'];
				if (!empty($selector)) 	$selector .=' class="'.strtolower($k).'">';
				
				if (!empty($v)) { // some custom eg: TRUMBA fields may have empty or no  values
					$eprop .= $selector
						.amr_format_value($v, $k, $e,$kv['Before'],$kv['After'] )
						.$selectorend;
				}
			} 

			if (empty($eprop)) $eprop = '&nbsp;';  // a value for a dummytable cell if tere were no values in the column
				
			// annoying but only way to pass variables by reference is through an array, must return array to then.	
			// so to allow filter of column and pass which column it is, thsi is how we do it
			$tmp = apply_filters('amr_events_column_html', 
				array('colhtml'=>$eprop, 'col'=>$col));
				
			$eprop = $tmp['colhtml'];
			
			if (!empty($ul))  // will phase the ul's  out eventually
				$eprop = $ul.' class="amrcol'.$col.' amrcol">'
				.$eprop
				.$htm['ulc'];
				
			/* each column in a cell or list */
			$cellclasses = '';
			if (!empty($htm['cell']) ) { // if we have a selector that surounds each property , then add classes.
				$cellclasses .= ' amrcol'.$col;
				if ($col == $no_cols) {
					$cellclasses .= ' lastcol'; /* only want the cell to be lastcol, not the row */
				}
				$thiscolumn = $htm['cell'].' class="'.$cellclasses.'">' .$eprop. (empty($htm['cellc']) ? '' : $htm['cellc']);
			}
			else $thiscolumn = $eprop;

			$rowhtml .= $thiscolumn; // build up the row with each column 
		} // end row

		// so now we have finished that group, start next
		// save the event or row,  for next group
		if (!empty($rowhtml)) {
			$tmp = apply_filters('amr_events_event_html', array('rowhtml'=>$rowhtml, 'event'=>$e));
			$rowhtml = $tmp['rowhtml'];
			$rowhtml = (!empty($htm['row']) ? ($htm['row'].($alt ? ' class="odd alt':' class="').$classes.' event"> ') : '')
			.$rowhtml
			.$htm['rowc'];

		if ($alt) $alt=false;
		else $alt=true;

		$html .= $rowhtml;		/* build  the group of events , adding on eprop */
		$rowhtml = '';
		}
	
	}
			//end of row or event
	return $html;		
}
/* --------------------------------------------------  */
function amr_list_events_in_groupings ($htm, $id='', $columns, $groupedevents, $events) {  // 2 levels of grouping only at moment

	if (empty($events)) return;
	$html = '';
	if (empty($groupedevents)) {
		$all= array_keys($events); 
		$html .= $htm['body'].'>'.amr_list_event_subset ($htm, $columns, $all, $events).$htm['bodyc'];
		return $html;
	}
	
	$prevtitle = '';
	
	foreach ($groupedevents as $g => $nextlevel) {
		
		if (!empty($nextlevel['title'])) { // there are titles
			//echo
			if (is_array($nextlevel['title'])) { 
				$hhtml = '';
				foreach ($nextlevel['title'] as $i => $titlestring) { //if (isset($_GET['debugg'])) {echo '<br />Title='.$titlestring;}
					if ($i == 0 ) {  // for first title only
						if ($titlestring == $prevtitle ) continue; // only show the outer title once
						else $prevtitle = $titlestring;
						if (!empty($nextlevel['parent'])) {
							$id = get_term_by('id',$nextlevel['parent'],$nextlevel['grouping'] );
							$id = 'id="'.$id->slug.'" ';
						}
					}
					else $id='';
					
					$hhtml .= amr_do_grouping_html($htm,$id, 'level'.$i.' '.$nextlevel['grouping'], $titlestring);
				}
				$html .= $htm['body'].' class="toggle_container" >'.$hhtml;  // cannot have multiple thead
			}
			$html = $html.amr_list_event_subset ($htm, $columns, $nextlevel['events'], $events).$htm['bodyc'];
		}
		else {  if (isset($_GET['debugg']))  echo '<br />No titles ? is that no grouping ?';
			$html .= amr_list_events_in_groupings ($htm, $id, $columns, $nextlevel, $events);
		}
		
// check next level ??
		
	}
	
	return $html;
}
/* --------------------------------------------------  */
function amr_produce_tree_from_array ($terms, $parent=0) {
// get all where parent = 0, unset
// then for eah parent get children,unset
//repeat till none left
	$tree = array();
// get all that match parent	
	if (!empty($terms)) {
		foreach($terms as $i=>$term) {
			if ($term->parent == $parent) {
				$tree[$term->term_id] = $term->term_id;  // create top level
				unset ($terms[$i]);
				if (!empty($terms)) { 
					echo '<br />'.$term->term_id.' '.$term->name.' terms left:'.count($terms);
					$tree[$term->term_id] = amr_produce_tree_from_array ($terms, $term->term_id);  // any children ?
				}
			}
			else echo '<br /> not a child '.$term->term_id.' has parent '.$term->parent;
			
		}
	}
	if (WP_DEBUG) {echo '<br /> The tree: ';  var_dump($tree);}
	return ($tree);
}
/* --------------------------------------------------  */
function amr_assign_events_to_dategrouping ($dategrouping, $eventsubset, $events) {

	foreach ($eventsubset['events'] as $i => $index) {
		//if (WP_DEBUG) echo '<br />dategrouping = '.$dategrouping;  
		if (empty($events[$index]['EventDate'])) 
			$datetouse	 = $events[$index]['DTSTART'];
		else 
			$datetouse	 = $events[$index]['EventDate'];
		$groupstring = amr_format_grouping($dategrouping, $datetouse);		
		$groupsubset[$groupstring]['events'][] = $index;
	}
	$first = true;
	foreach ($groupsubset as $groupstring => $garray) {
		
		if (is_array($eventsubset['title'])) 
			$groupsubset[$groupstring]['title'] = $eventsubset['title']; // the top level title
		else 	
			$groupsubset[$groupstring]['title'][] = $eventsubset['title']; // the top level title
		$groupsubset[$groupstring]['title'][] = $groupstring;  
		$groupsubset[$groupstring]['grouping'] = $dategrouping; // remember it
		//echo '<br />Date: '.$groupstring; var_dump($groupsubset[$groupstring]); echo '<hr/>';
	}
	return $groupsubset; // subarray of events eg:   [Jan2001] = array(id1,id2)
}
/* --------------------------------------------------  */
function amr_assign_events_to_taxogrouping ($taxonomy_groupby, $eventsubset, $events) { // still in flat taxonomy

	foreach ($eventsubset['events'] as $i => $index) {
		if (!empty($events[$index][$taxonomy_groupby])) {  //ie the event has a taxonomy term
			if (is_array($events[$index][$taxonomy_groupby])) { // it probably will be
				foreach ($events[$index][$taxonomy_groupby] as $j => $taxoterm) { 
					$groupsubset[$taxoterm]['events'][] = $index;
				}				
			}
			else {
				if (isset($_GET['debugg']))  echo ' ** Taxonomy terms not an array'; var_dump($event[$taxonomy_groupby]);
				}
		}
		else { if (isset($_GET['debugg']))   {echo '<br />*** No value for '.$taxonomy_groupby.' in event '.$events[$index]['id']; }
		}
	}
	if (isset($_GET['debugg'])) {echo '<br />Grouped events by taxo grouping: '.$taxonomy_groupby.'<br />'; var_dump($groupsubset);}
	if (empty($groupsubset)) return false;
	// now check all the groups identified
	foreach ($groupsubset as $taxoterm => $garray) { // garray = array [events]
		$term = get_term_by('id', $taxoterm, $taxonomy_groupby);	
		$terms[] =  $term; 
		if (is_array($eventsubset['title']))
			$groupsubset[$taxoterm]['title'] = $eventsubset['title'];
		else 
			$groupsubset[$taxoterm]['title'][] = $eventsubset['title'];	
		// now add to the titles	
		$groupsubset[$taxoterm]['title'][] = amr_prepare_term_title($taxoterm, $taxonomy_groupby);
		$groupsubset[$taxoterm]['grouping'] = $taxonomy_groupby; // remember it for later
		
		//if (isset($_GET['debugg'])) {echo '<br />Checking for parent:<br /> '; var_dump($term);}
		if (isset($term->parent)) $groupsubset[$taxoterm]['parent'] = $term->parent; // remember it for later
		//build up levels of titlle relative to nesting
		//echo '<br />After taxo groupings: '.$taxoterm; var_dump($groupsubset[$taxoterm]); echo '<hr/>';
	}
	//if (WP_DEBUG) {echo '<br />*** Terms'; VAR_DUMP($terms);}
	
	$taxo = get_taxonomy($taxonomy_groupby);
	//echo '<hr />TAXO: ';var_dump($taxo);
	if ($taxo->hierarchical) { // if a hierarchical taxonomy , do some funky footwork   !!!*** MAYBE CHECK IF WANT TO DO HIERARCHICALLY
		if (isset($_GET['debugg'])) echo '<hr />It is hierarchical, so handle it: <br />';
		$groupsubset = amr_handle_taxonomy_hierarchy($taxonomy_groupby, $groupsubset);
	}
	// now we want to reorder by the nesting / tree structure and add title ?
	return $groupsubset; // subarray of events eg:   [cat1] = array(id1,id2), [cat2]= array(id2,id4)
}
/* --------------------------------------------------  */
function amr_handle_taxonomy_hierarchy ($taxonomy_groupby, $groupsubset) { 	
	// need to sort by parents , so they will appear in correct order hierarchically  (add a class ?)
	$newset = amr_sort_by_key($groupsubset, 'parent');
	//if (isset($_GET['debugg'])) {echo '<br />sorted by parent ';foreach ($newset as $t => $arr){ echo '<br />'.$t.'<br />'; var_dump($arr);} ;}
	// then for each parent, if it has not appeared, we need to 'add it' before the child - will have no events

	$args = array(
			'orderby' => 'term_group', //    id,    count,    name - Default,    slug,    term_group,     none 
			//'order' => 'ASC', // DESC
			'hide_empty' => 1,
			'fields' => 'all', //default
			'hierarchical' => true // pass even if empty, if child not empty
		);
		
	// get the terms for that taxonomy ? how to sort ????	
	$terms = get_terms ($taxonomy_groupby, $args);
	foreach ($newset as $termname => $garray) {   // for our newly sorted array
		if (!empty($garray['parent'])) {
			//if (isset($_GET['debugg'])) {echo '<br />Term = '.$termname.'    Parent = '.$garray['parent'];}
			$parentterm = get_term($garray['parent'],$taxonomy_groupby );
			if (isset($_GET['debugg'])) {echo '<br />ParentTerm = '; var_dump($parentterm->name);}
			if (!isset($newset[$parentterm->name])) {// then it hasn't appeared
				//if (isset($_GET['debugg'])) {echo '<br />Title to add:'.$parentterm->name;}
				$grptitle_to_add = amr_prepare_term_title($parentterm->name, $taxonomy_groupby);
				if (isset($_GET['debugg'])) {echo '<br />Existing title ='; var_dump($newset[$termname]['title']); echo '<br />Title to add:'.$grptitle_to_add;}
				array_unshift($newset[$termname]['title'],$grptitle_to_add );
				if (isset($_GET['debugg'])) {echo '<br /> New set of titles: <br />'; var_dump($newset[$termname]['title']);}
			}
			else {if (isset($_GET['debugg'])) echo '<br />already  have in this parent '.$parentterm->name;}
		}
		
	}
	return ($newset);
}
/* --------------------------------------------------  */
function amr_assign_events_to_a_grouping ($grouping, $eventsubset, $events) {
	if (in_array($grouping, array(
			'Year', 
			'Month', 
			'Day', 
			'Week',
			'Quarter',	
			'Astronomical Season',	
			'Traditional Season',
			'Western Zodiac',
			'Solar Term' ), true)) {
			$groupsubset = amr_assign_events_to_dategrouping ($grouping, $eventsubset, $events);
			// returns array[grouping][events]
			//                                     [title]
		}
		elseif (taxonomy_exists( $grouping )) { // ie it is a taxonomy
			
			$groupsubset = amr_assign_events_to_taxogrouping ($grouping, $eventsubset, $events);
			
		}
		else { // as yet unknown - maybe grouping by location ? - depends how stored then, maybe save places as taxo terms ?
			if (WP_DEBUG) echo '<br />Unknown grouping: '.$grouping;
			$groupsubset = array();
		}
	return ($groupsubset);
}
/* --------------------------------------------------  */
function amr_assign_events_to_groupings ($groupings, $events) {
// assign events to first level of grouping, handling hierarchical
// then assign that subset to next level ?
	if (empty($groupings)) return false;
	// chcek / convert grupings
	foreach ($groupings as $i => $g) {
		if (is_bool($g)) {
			if (!$g) unset ($groupings[$i]);
			else $groupings[$i] = $i;
		}
	}
	if (isset($_GET['debugg'])) {echo '<br />Groupings requested: '; var_dump($groupings);}
	$grouping = array_shift($groupings);  // get the first
	// setup initial 'subset' which is actually all
	//if (WP_DEBUG) {echo '<br />Start with grouping '.$grouping;}

	foreach ($events as $i => $event) { 
		$eventsubset['events'][] = $i; 
	}
	$eventsubset['title'] = array(); 
	
	$groupsubset = amr_assign_events_to_a_grouping ($grouping, $eventsubset, $events);
	if (empty ($groupsubset) ) return false;
	//should now have ? array [$taxo][events]
	//                            ............................[title]
		
	//if (isset($_GET['debugg'])) { echo '<hr/>First Pass *************<br/>';
	//foreach ($groupsubset as $g => $arr ) {echo '<br />***'.$g.'<br />'; var_dump($arr); echo '<hr/>';}}
	
	if (!empty($groupings)) { // only allows two levels for now  
		
		$grouping = array_shift($groupings);  // get the  2nd grouping 
		//if (WP_DEBUG) {echo '<br />And then with grouping '.$grouping;}
		foreach ($groupsubset as $grp => $garray) {  // overwrite the curremt subset with the next level tree
			if (isset($_GET['debugg']))  echo '<hr />Doing grp = '.$grp; //.' with'; var_dump($garray);
			$groupsubset[$grp] = amr_assign_events_to_a_grouping ($grouping, $garray, $events);
				//should now have ? array [$taxo or date ][events]
				//                            ............................[title]
			//echo '<br />Then have '; 	var_dump($groupsubset[$grp]); echo '<hr/>';
			
		}
	}
	return $groupsubset;
}
/* --------------------------------------------------  */
function amr_do_grouping_html($htm, $id, $class, $titlestring) {
	
	$html =
		((!empty($htm['grow'])) ? $htm['grow'].$id.' class="trigger group '.$class.'">' : '')
		.((!empty($htm['ghcell'])) ? $htm['ghcell'].' class="group '.$class. '" >' : '')
		.$titlestring
		.$htm['ghcellc']
		.$htm['growc'];
	return $html;

}
/* --------------------------------------------------  */
function amr_prepare_term_title($term, $taxo) { // this is a term 
global 	$amr_options,$amr_listtype;

	if (is_int($term)) $termdata = get_term_by('id', $term, $taxo, ARRAY_A);   
	else $termdata = get_term_by('name', $term, $taxo, ARRAY_A);  
	$termstring = '<a class="term" href="#">'.$termdata['name'].'</a>'; // to facilitate collapsability
	
	if (true or !empty ($amr_options['listtypes'][$amr_listtype]['grouping_settings']['show_description'])) { //***
		if (!empty($termdata['description'])) 
			$termstring .=' <span class="termdesc"  >'.$termdata['description'].'</span>';
		// allow own definition of how taxonomy term should look
	}
	$termstring = apply_filters('amr_term_grouping_title',$termstring,$term);
	return $termstring;
}
/* --------------------------------------------------  */
function amr_get_groupings_requested () {
global 	$amr_options,
		$amr_listtype,
		$amr_groupings;
		
		if (isset ($amr_options['listtypes'][$amr_listtype]['grouping'])) {
				foreach (($amr_options['listtypes'][$amr_listtype]['grouping']) as $i => $level)
					{	if ($level) { $g[$level] = $i; }			}
			}

		if (!empty($g)) return ($g);
		else return false;
}
/* -------------------------------------------------------------------------------------------*/
function amr_do_a_grouping($htm, $gi, $ghtml) {
 /* we have a new group  */
	$id = amr_string($gi.$new[$gi]);
	$html =
		((!empty($htm['grow'])) ? $htm['grow'].'class="group '.$gi.'">' : '')
		.((!empty($htm['ghcell'])) ? $htm['ghcell'].' class="group '.$gi. '" >' : '')
		.$grouping
		.$htm['ghcellc']
		.$htm['growc'];
	return $html;
}
/* -------------------------------------------------------------------------------------------*/
