var eo_venue = eo_venue || { marker: false };

jQuery(document).ready(function ($) {
	
	postboxes.add_postbox_toggles( pagenow );
				
	var eo_venue_Lat = $("#eo_venue_Lat").val();
	var eo_venue_Lng = $("#eo_venue_Lng").val();
	var zoom = 15;
        
	if( eo_venue_Lat === 0 && eo_venue_Lng === 0 ){
		var address = [];
		$(".eo_addressInput").each(function (){ address.push($(this).val());});
		if( !address.join('') ){
			zoom = 1;
		}
	}

	eovenue.init_map( 'venuemap', {
		lat: eo_venue_Lat,
        lng: eo_venue_Lng,
        zoom: zoom,
        draggable: true,
        onDrag: function( evt ) {
        	this.dragging = true;
        	var latlng = evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6);
        	$("#eo-venue-latllng-text").text( latlng );
        },
        onDragend: function( evt ) {
        	this.dragging = false;
        	this.setPosition( this.position );
        },
        onPositionchanged: function (){
        	if( !this.dragging ){
        		var latLng    = this.getPosition();
        		var latlngStr = latLng.lat().toFixed(6) + ',' + latLng.lng().toFixed(6);
        		
        		$("#eo_venue_Lat").val( latLng.lat().toFixed(6) );
        		$("#eo_venue_Lng").val( latLng.lng().toFixed(6) );
        		$("#eo-venue-latllng-text").text( latlngStr );
        		                
        		this.getMap().setCenter( latLng );
        		this.getMap().setZoom( 15 );
        	}
        },
	});
        
	$(".eo_addressInput").change(function () {
		var address = [];
		$(".eo_addressInput").each(function () {
			address.push($(this).val());
		});
            
		eovenue.geocode( address.join(', '), function( latlng ){
			if( latlng ){
				eovenue.get_map( 'venuemap' ).marker[0].setPosition( latlng );
			}
		});
	});
	
	$('#eo-venue-latllng-text').blur(function() {
		var text    = $(this).text().trim().replace(/ /g,'');
		var match   = text.match(/^(-?[0-9]{1,3}\.[0-9]+),(-?[0-9]{1,3}\.[0-9]+)$/);
		var old_lat = $(this).data('eo-lat');
		var old_lng = $(this).data('eo-lng');
		
		if( match ){
			var lat = match[1];
			var lng = match[2];
			
			if( lat != old_lat || lng != old_lng ){
				$(this).data( 'eo-lat', lat );
				$(this).data( 'eo-lng', lng );
				var latlng = new google.maps.LatLng( lat, lng );
					eovenue.get_map( 'venuemap' ).marker[0].setPosition( latlng );
				}
		}else{
			//Not valid...
			$(this).text( old_lat + "," + old_lng );
		}
	});
	
	$('#eo-venue-latllng-text').keydown( function( evt ){
		//On enter leave the latitude/longtitude
		if( 13 === evt.which ){
			$(this).blur();	
		}
	});
			
});