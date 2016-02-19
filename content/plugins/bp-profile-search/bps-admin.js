
function add_field(forWhat) {
	var holder = document.getElementById( 'field_box' );
	var theId = document.getElementById( 'field_next' ).value;

	var newDiv = document.createElement( 'p' );
	newDiv.setAttribute( 'id', 'field_div' + theId );
	newDiv.setAttribute( 'class', 'sortable' );

	var span = document.createElement( 'span' );
	span.appendChild( document.createTextNode( "\u00A0\u039E " ) );

	var $select = jQuery("<select>", {name: 'bps_options[field_name][' + theId + ']', id: 'field_name' + theId});
	var $option = jQuery("<option>", {text: bps_strings.field, value: 0});
	$option.appendTo($select);

	jQuery.each( bps_groups, function( i, optgroups ) {
		jQuery.each( optgroups, function( groupName, options ) {
			var $optgroup = jQuery( "<optgroup>", {label: groupName} );
			$optgroup.appendTo( $select );

			jQuery.each( options, function( j, option ) {
				var $option = jQuery( "<option>", {text: option.name, value: option.id} );
				$option.appendTo( $optgroup );
			});
		});
	});

	var fieldL = document.createElement( 'input' );
	fieldL.setAttribute( 'type', 'text' );
	fieldL.setAttribute( 'name', 'bps_options[field_label][' + theId + ']' );
	fieldL.setAttribute( 'id', 'field_label' + theId );
	fieldL.setAttribute( 'placeholder', bps_strings.label );
	fieldL.setAttribute( 'style', 'width: 16%' );

	var fieldD = document.createElement( 'input' );
	fieldD.setAttribute( 'type', 'text' );
	fieldD.setAttribute( 'name', 'bps_options[field_desc][' + theId + ']' );
	fieldD.setAttribute( 'id', 'field_desc' + theId );
	fieldD.setAttribute( 'placeholder', bps_strings.description );
	fieldD.setAttribute( 'style', 'width: 32%' );

	var range = document.createElement( 'input' );
	range.setAttribute( 'type', 'checkbox' );
	range.setAttribute( 'name', 'bps_options[field_range][' + theId + ']' );
	range.setAttribute( 'id', 'field_range' + theId );
	range.setAttribute( 'value', theId );

	var label = document.createElement( 'label' );
	label.appendChild( range );
	label.appendChild( document.createTextNode( bps_strings.range + ' ' ) );

	var toDelete = document.createElement( 'a' );
	toDelete.setAttribute( 'href', "javascript:hide('field_div" + theId + "')" );
	toDelete.setAttribute( 'class', 'delete' );
	toDelete.appendChild( document.createTextNode( '[x]' ) );

	holder.appendChild( newDiv );
	newDiv.appendChild( span );
	$select.appendTo("#field_div" + theId);
	newDiv.appendChild( document.createTextNode( " " ) );
	newDiv.appendChild( fieldL );
	newDiv.appendChild( document.createTextNode( " " ) );
	newDiv.appendChild( fieldD );
	newDiv.appendChild( document.createTextNode( " " ) );
	newDiv.appendChild( label );
	newDiv.appendChild( toDelete );

	enableSortableFieldOptions();
	document.getElementById( 'field_name' + theId ).focus();
	document.getElementById( 'field_next' ).value = ++theId;
}

function hide( id ) {
	var element = document.getElementById( id );
	element.parentNode.removeChild( element );
}

function enableSortableFieldOptions() {
	jQuery( '.field_box' ).sortable( {
		items: 'p.sortable',
		tolerance: 'pointer',
		axis: 'y',
		handle: 'span'
	});

	jQuery( '.sortable span' ).css( 'cursor', 'move' );
}

jQuery( document ).ready( function() {
	enableSortableFieldOptions();
});
