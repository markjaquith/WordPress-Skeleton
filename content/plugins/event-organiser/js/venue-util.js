(function($) {
eovenue = {
		
	maps: {},

	/**
	 * Options
	 *  - lat
	 *  - lng
	 *  - zoom
	 *  - draggable
	 *  - onDrag
	 */
	init_map: function( id, options ){
		
	    if (typeof google === "undefined") {
	    	return;
	    }
	
	    var fieldID   = ( options.hasOwnProperty( 'fieldID' ) ? options.fieldID : id );
	    var draggable  = ( options.hasOwnProperty( 'draggable' ) ? options.draggable : false );
	    var markerIcon = ( options.hasOwnProperty( 'markerIcon' ) ? options.markerIcon : null );
	    	
	    var lat = ( options.hasOwnProperty( 'lat' ) ? options.lat : 0 );
	    var lng = ( options.hasOwnProperty( 'lng' ) ? options.lng : 0 );
	    var latlng = new google.maps.LatLng( lat, lng );

	    var map_options = {
	    	zoom: ( options.hasOwnProperty( 'zoom' ) ? options.zoom : 15 ),
	    	center: latlng,
	    	mapTypeId: google.maps.MapTypeId.ROADMAP
	    };

	    var map    = new google.maps.Map( document.getElementById( fieldID ), map_options );
	    var marker = new google.maps.Marker({
            position:  latlng,
            map:       map,
            draggable: draggable,
    		icon:      markerIcon
        });
	   
	    this.maps[id] = {
	    	map:    map,
	    	marker: [ marker ]
	    } ;
	    
	    
	    if( options.hasOwnProperty( 'onDrag' ) && options.onDrag ){
	    	google.maps.event.addListener( marker, 'drag', options.onDrag );
	    }
	    
	    if( options.hasOwnProperty( 'onDragend' ) && options.onDragend ){
	    	google.maps.event.addListener( marker, 'dragend', options.onDragend );
	    }
	    
	    if( options.hasOwnProperty( 'onPositionchanged' ) && options.onPositionchanged ){
	    	google.maps.event.addListener( marker, 'position_changed', options.onPositionchanged );
	    }
	    
	},
	
	geocode: function( address, callback ){
	    
		if (typeof google === "undefined") {
	    	return;
	    }		
		
		var geocoder = new google.maps.Geocoder();
		
		geocoder.geocode(
			{ 'address': address}, 
			function (results, status) {
				if ( status == google.maps.GeocoderStatus.OK ){
					callback.call( this, results[0].geometry.location );
				}else{
					return callback.call( this, false );
				}
		});
	},
		
	get_map: function( id ){
		return this.maps[id];
	}
				
};
})(jQuery);