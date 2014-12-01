var eventorganiser = eventorganiser || {};
/**
 * Simply compares two string version values.
 * 
 * Example:
 * versionCompare('1.1', '1.2') => -1
 * versionCompare('1.1', '1.1') =>  0
 * versionCompare('1.2', '1.1') =>  1
 * versionCompare('2.23.3', '2.22.3') => 1
 * 
 * Returns:
 * -1 = left is LOWER than right
 *  0 = they are equal
 *  1 = left is GREATER = right is LOWER
 *  And FALSE if one of input versions are not valid
 *
 * @function
 * @param {String} left  Version #1
 * @param {String} right Version #2
 * @return {Integer|Boolean}
 * @author Alexey Bass (albass)
 * @since 2011-07-14
 */
eventorganiser.versionCompare = function(left, right) {
	if (typeof left + typeof right != 'stringstring')
		return false;
	    
	var a = left.split('.'), b = right.split('.'), len = Math.max(a.length, b.length);
	        
	for ( var i = 0; i < len; i++) {
		if ((a[i] && !b[i] && parseInt(a[i],10) > 0) || (parseInt(a[i],10) > parseInt(b[i],10))) {
			return 1;
		} else if ((b[i] && !a[i] && parseInt(b[i],10) > 0) || (parseInt(a[i],10) < parseInt(b[i],10))) {
			return -1;
		}
	}
	    
	return 0;
};


jQuery(document).ready(function($) {
var eo_venue_obj;
//Date fields must be wrapped inside event-date
eventOrganiserSchedulePicker.init({
	views: {
		start_date: '#eventorganiser_detail #from_date',
		start_time: '#HWSEvent_time',
		end_date: '#eventorganiser_detail #to_date',
		end_time: '#HWSEvent_time2',
		occurrence_picker: '#eo_occurrence_datepicker',
		occurrence_picker_toggle: '.eo_occurrence_toggle',
		schedule_last_date: '#recend',
    	schedule: "#HWSEventInput_Req",
    	is_all_day: "#eo_allday",
		frequency: '#HWSEvent_freq',
		week_repeat: '#dayofweekrepeat',
		month_repeat: '#dayofmonthrepeat',
		recurrence_section: '.reocurrence_row',
    	include: '#eo_occurrence_includes',
    	exclude: '#eo_occurrence_excludes',
    	schedule_span: '#recpan',//day(s)|week(s)|month(s)|year(s) - human readable span
    	summary: "#event_summary"
	},
	format: EO_Ajax_Event.format,
	is24hour: Boolean(EO_Ajax_Event.is24hour),
	startday: EO_Ajax_Event.startday,
	schedule: window.eventOrganiserSchedule,
	locale: EO_Ajax_Event.locale,
	editable: ( $("#HWSEventInput_Req").val() == 'once' ||  $("#HWSEventInput_Req").val() == 'custom' )//if recurring set to false
});

//Edit recurrinng dates
$("#HWSEvent_rec").click(function() {
	//eventOrganiserSchedulePicker.update_schedule();
	window.eventOrganiserSchedulePicker.options.editable = $("#HWSEvent_rec").is(':checked');
	window.eventOrganiserSchedulePicker.update_form();
});

//Map
eovenue.init_map( 'venuemap', {
	lat: $("#eo_venue_Lat").val(),
	lng: $("#eo_venue_Lng").val(),
	draggable: false,
	onPositionchanged: function (){
		var latLng = this.getPosition();
		
		jQuery("#eo_venue_Lat").val( latLng.lat().toFixed(6) );
		jQuery("#eo_venue_Lng").val( latLng.lng().toFixed(6) );
        
		this.getMap().setCenter( latLng );
		this.getMap().setZoom( 15 );
	},
});

//The venue combobox
$.widget("ui.combobox", {
	_create: function () {
	var c = this.element.hide(),d = c.children(":selected"),e = d.val() ? d.text() : "";
	var wrapper  = $("<span>").addClass("ui-combobox eo-venue-input").insertAfter(c);
	var options = {
			delay: 0,
			minLength: 0,
			source: function (a, callback) {
				$.getJSON(EO_Ajax_Event.ajaxurl + "?callback=?&action=eo-search-venue", a, function (a) {
					var venues = $.map(a, function (a) {a.label = a.name;return a;});
					callback(venues);
				});
			},
			select: function (a, b) {
				if ($("tr.venue_row").length > 0) {
					if ( parseInt( b.item.term_id, 10 ) === 0) {
						$("tr.venue_row").hide();
						$("#eventorganiser_event_detail tr.eo-add-new-venue").hide();
					} else {
						$("tr.venue_row").show();
						$("#eventorganiser_event_detail tr.eo-add-new-venue").hide();
					}
					
					var latlng = new google.maps.LatLng( b.item.venue_lat, b.item.venue_lng );
					eovenue.get_map( 'venuemap' ).marker[0].setPosition( latlng );
				}
				$("#venue_select").removeAttr( "selected" );
				$("#venue_select").val( b.item.term_id );
			}
		};
		var input = $("<input>").appendTo(wrapper).val(e).addClass("ui-combobox-input").autocomplete(options).addClass("ui-widget-content ui-corner-left");
       
		/* Backwards compat with WP 3.3-3.5 (UI 1.8.16-1.9.2)*/
		var jquery_ui_version = $.ui ? $.ui.version || 0 : -1;
		var ac_namespace = ( eventorganiser.versionCompare( jquery_ui_version, '1.10' ) >= 0 ? 'ui-autocomplete' : 'autocomplete' );
		
		//Apend venue address to drop-down
		input.data( ac_namespace )._renderItem = function (a, venue ) {
			if ( parseInt( venue.term_id, 10 ) === 0 ) {
				return $("<li></li>").data( ac_namespace + "-item", venue ).append("<a>" + venue.label + "</a>").appendTo(a);
			}
			//Clean address
			var address_array = [venue.venue_address, venue.venue_city, venue.venue_state,venue.venue_postcode,venue.venue_country];
			var address = $.grep(address_array,function(n){return(n);}).join(', ');
			
			/* Backwards compat with WP 3.3-3.5 (UI 1.8.16-1.9.2)*/
			var li_ac_namespace = ( eventorganiser.versionCompare( jquery_ui_version, '1.10' ) >= 0 ? 'ui-autocomplete-item' : 'item.autocomplete' );

			return $("<li></li>").data( li_ac_namespace, venue)
				.append("<a>" + venue.label + "</br> <span style='font-size: 0.8em'><em>" +address+ "</span></em></a>").appendTo(a);
		};

		//Add new / selec buttons
		var button_height = eventorganiser.is_mp6 ? '27px' : '21px';
		var button_wrappers = $("<span>").addClass("eo-venue-combobox-buttons").appendTo(wrapper);
		$("<a style='vertical-align: top;margin: 0px -1px;padding: 0px;height:"+button_height+";'>").attr("title", "Show All Items").appendTo(button_wrappers).button({
			icons: { primary: "ui-icon-triangle-1-s"},
			text: false
		}).removeClass("ui-corner-all").addClass("eo-ui-button ui-corner-right ui-combobox-toggle ui-combobox-button").click(function () {
			if (input.autocomplete("widget").is(":visible")) {input.autocomplete("close");return;}
			$(this).blur();
			input.autocomplete("search", "").focus();
		});

		if( EO_Ajax_Event.current_user_can.manage_venues ){
			//Only add this on event edit page - i.e. not on calendar page.
			$("<a style='vertical-align: top;margin: 0px -1px;padding: 0px;height:"+button_height+";'>").attr("title", "Create New Venue").appendTo(button_wrappers).button({
				icons: {primary: "ui-icon-plus"},
				text: false
			}).removeClass("ui-corner-all").addClass("eo-ui-button ui-corner-right add-new-venue ui-combobox-button").click(function () {
				$("#eventorganiser_event_detail tr.eo-add-new-venue").show();			
				$("tr.venue_row").show();
				
				//Store existing venue details in case the user cancels creating a new one
				eo_venue_obj={
						id: $("#venue_select").val(),
						label: $(".eo-venue-input input").val(),
						lat: $("#eo_venue_Lat").val(),
						lng: $("#eo_venue_Lng").val()
				};
				$("#venue_select").removeAttr("selected").val(0);
				$('.eo-venue-combobox-select').hide();
				$('.eo-venue-input input').val('');

				//Use selected timezone to 'guess' a new address, so we don't get a blank map instead.
				var address = EO_Ajax_Event.location;
				if( address ){
					address = address.split("/");					
		            eovenue.geocode( address[address.length-1], function( latlng ){
		            	if( latlng ){
		                	eovenue.get_map( 'venuemap' ).marker[0].setPosition( latlng );
		                }
		            });
				}else{
					var latlng = new google.maps.LatLng( 0, 0 );
					eovenue.get_map( 'venuemap' ).marker[0].setPosition( latlng );
					eovenue.get_map( 'venuemap' ).map.setZoom( 1 );
				}
				$(this).blur();
			});
		}	
	}
});

$("#venue_select").combobox();

$(".eo_addressInput").change(function () {
    var address = [];
    $(".eo_addressInput").each(function () {
    	address.push(jQuery(this).val());
    });
    
    eovenue.geocode( address.join(', '), function( latlng ){
    	if( latlng ){
        	eovenue.get_map( 'venuemap' ).marker[0].setPosition( latlng );
        }
    });
});

//When cancelling venue input, restore defaults
$('.eo-add-new-venue-cancel').click(function(e){
	e.preventDefault();
	$('.eo-venue-combobox-select').show();
	$('.eo-add-new-venue input').val('');

	//Restore old venue details
	var latlng = new google.maps.LatLng( eo_venue_obj.lat, eo_venue_obj.lng );
	eovenue.get_map( 'venuemap' ).marker[0].setPosition( latlng );
	$("#venue_select").val( eo_venue_obj.id );
	$(".eo-venue-input input").val( eo_venue_obj.label );
	$("#eventorganiser_event_detail tr.eo-add-new-venue").hide();	
});
});