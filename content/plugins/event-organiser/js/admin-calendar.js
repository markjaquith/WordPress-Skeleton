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
    
    var a = left.split('.'), b = right.split('.'), i = 0, len = Math.max(a.length, b.length);
        
    for (; i < len; i++) {
        if ((a[i] && !b[i] && parseInt(a[i],10) > 0) || (parseInt(a[i],10) > parseInt(b[i],10))) {
            return 1;
        } else if ((b[i] && !a[i] && parseInt(b[i],10) > 0) || (parseInt(a[i],10) < parseInt(b[i],10))) {
            return -1;
        }
    }
    
    return 0;
};


(function ($) {
	 /**Parses string formatted as YYYY-MM-DD to a Date object.
	  * If the supplied string does not match the format, an 
	  * invalid Date (value NaN) is returned.
	  * Used as a workaround for IE7/8 difficulities.
	  * @link https://stackoverflow.com/questions/2182246/javascript-dates-in-ie-nan-firefox-chrome-ok
	  * @param {string} dateStringInRange format YYYY-MM-DD, with year in
	  * range of 0000-9999, inclusive.
	  * @return {Date} Date object representing the string.
	  */
	  function eventorganiser_parseISO8601( dateStringInRange ) {
	    var isoExp = /^\s*(\d{4})-(\d\d)-(\d\d)\s*$/,
	        date = new Date(NaN), month,
	        parts = isoExp.exec(dateStringInRange);

	    if( parts ) {
	      month = +parts[2];
	      date.setFullYear( parts[1], month - 1, parts[3] );
	      if( month != date.getMonth() + 1 ) {
	        date.setTime( NaN );
	      }
	    }
	    return date;
	  }
	  
    $(document).ready(function () {
	
	/* Calendar Dialogs */
	$('#eo-dialog-tabs').tabs();
	$('.eo-dialog').dialog({ 
		autoOpen: false,
		dialogClass: 'eo-admin-calendar-dialog',
		width: 527,
		modal:true
	});
	//Add eo-ui-button to jQuery UI button
	$('.eo-dialog').parent().find('.ui-dialog-titlebar-close').addClass('eo-ui-button');
	$('#events-meta').parent().find('.ui-dialog-titlebar-close').appendTo('.ui-tabs-nav').closest('.ui-dialog').children('.ui-dialog-titlebar').remove();

	/* Time Format from screen option */
	var format = ($('#eofc_time_format').is(":checked") ? 'HH:mm' : 'h:mmtt');
    
	var initial_date = eventorganiser_parseISO8601( jQuery.cookie('eo_admin_cal_last_viewed_date') );
	
    //Invalid dates cause trouble in IE7&8 https://github.com/stephenharris/Event-Organiser/issues/96
    //Check date is valid: https://stackoverflow.com/questions/1353684/
	if ( Object.prototype.toString.call( initial_date  ) === "[object Date]" ) {
		if ( isNaN( initial_date.getTime() ) ) {
				//not valid
				initial_date = new Date();
		} else {
				//Is valid date
		}
	} else {
		//not valid;
		initial_date = new Date();
	}

	/* Calendar */
        var calendar = jQuery('#eo_admin_calendar').fullCalendar({
		firstDay: parseInt(EO_Ajax.startday,10),
		date: initial_date.getDate(),
		month: initial_date.getMonth(),
		year: initial_date.getFullYear(),
		defaultView: ($.cookie('eo_admin_cal_last_view') ? $.cookie('eo_admin_cal_last_view') : 'month'),
		editable: false,
		lazyFetching: 'true',
		eventColor: '#21759B',
		theme: true,
		customButtons:{
			category:  eventorganiser_cat_dropdown,
			venue:  ( EO_Ajax.venues !== '' ? eventorganiser_venue_dropdown : null ),
			'goto': eventorganiser_mini_calendar
		},
		buttonText: {
			today: EO_Ajax.locale.today,
			 month: EO_Ajax.locale.month,
			week: EO_Ajax.locale.week,
			day: EO_Ajax.locale.day,
			cat: EO_Ajax.locale.cat,
			venue: EO_Ajax.locale.venue
		},
		monthNames: EO_Ajax.locale.monthNames,
		monthNamesShort: EO_Ajax.locale.monthAbbrev,
		dayNames: EO_Ajax.locale.dayNames,
		dayNamesShort: EO_Ajax.locale.dayAbbrev,
		isRTL: EO_Ajax.locale.isrtl,
		header: {
			left: 'title',
                	center: 'category venue',
                	right: 'prev goto today next'
		},
		buttonIcons: false,
		buttonui: true,
		events: function (start, end, callback) {
                	jQuery.ajax({
				url: EO_Ajax.ajaxurl + "?action=event-admin-cal",
                    		dataType: 'JSON',
				data: {
					start: jQuery.fullCalendar.formatDate(start, 'yyyy-MM-dd'),
					end: jQuery.fullCalendar.formatDate(end, 'yyyy-MM-dd')
                    		},
                    		success: function (data) {
                        		callback(data);
                    		}
                	});
		},
		categories: EO_Ajax.categories,
		venues: EO_Ajax.venues,
		selectable: true,
		selectHelper: true,
		eventRender: function (event, element) {
			var cat = jQuery(".filter-category .eo-cal-filter").val();
                	var venue = jQuery(".filter-venue .eo-cal-filter").val();
                	if ( typeof cat !== "undefined" && cat !== '' && (jQuery.inArray(cat, event.category) < 0)) {
                    		return '<div></div>';
                	}
                	if ( typeof venue !== "undefined" && venue !== '' && venue != event.venue) {
                    		return '<div></div>';
                	}
		},
		viewDisplay: function (element) {
			var date = jQuery.fullCalendar.formatDate( element.start,'yyyy-MM-dd');
			var view = element.name;

			//Expire cooke after 10 minutes
			var expires_date = new Date();
			expires_date = new Date(expires_date.getTime() + (10 * 60 * 1000));			
			$.cookie('eo_admin_cal_last_viewed_date', date,{ expires: expires_date });
			$.cookie('eo_admin_cal_last_view', view,{ expires: expires_date });
    		},
		weekMode: 'variable',
		aspectRatio: 1.50,
		loading: function (bool) {
			if (bool) jQuery('#loading').show();
			else jQuery('#loading').hide();
		},
		timeFormat:format,
		axisFormat: format,
		eventClick: function (event, jsevent, view) {
                	jsevent.preventDefault();
			jQuery("#eo-dialog-tabs ul li").each(function(){
				var id = $(this).attr('id').substring(14);
	                	jQuery("#eo-dialog-tabs #"+$(this).attr('id')+'-content').html(event[id]);
			});

			$('#events-meta').dialog('open');
		},
		select: function (startDate, endDate, allDay, jsEvent, view) {
                	if (EO_Ajax.perm_edit) {
				jsEvent.preventDefault();
				var fc_format = 'yyyy-MM-dd';
				var options = jQuery(this)[0].calendar.options;
				var start_date = jQuery.fullCalendar.formatDate(startDate, fc_format);
				var start_time = jQuery.fullCalendar.formatDate(startDate, 'HH:mm');
				var end_date = jQuery.fullCalendar.formatDate(endDate, fc_format);
				var end_time = jQuery.fullCalendar.formatDate(endDate, 'HH:mm');
				var the_date;
				if ( allDay ) {
					format = 'ddd, dS MMMM';
					allDay = 1;
				} else {
					format = 'ddd, dS MMMM h(:mm)tt';
					allDay = 0;
				}
                    		
				if (start_date == end_date) {
					the_date = jQuery.fullCalendar.formatDate(startDate, format, options);
					if (!allDay) {
						the_date = the_date + ' &mdash; ' + jQuery.fullCalendar.formatDate(endDate, 'h(:mm)tt', options );
					}
				} else {
					the_date = jQuery.fullCalendar.formatDate(startDate, format, options) + ' &mdash; ' + jQuery.fullCalendar.formatDate( endDate, format, options );
				}
				
				$("#eo_event_create_cal input[name='eo_event[event_title]']").val('');
				$("#eo_event_create_cal input.ui-autocomplete-input").val('');
				$("#eo_event_create_cal textarea[name='eo_event[event_content]']").val('');
    			$("#eo_event_create_cal input[name='eo_event[StartDate]']").val(start_date);
    			$("#eo_event_create_cal input[name='eo_event[StartTime]']").val(start_time);
    			$("#eo_event_create_cal input[name='eo_event[EndDate]']").val(end_date);
				$("#eo_event_create_cal input[name='eo_event[FinishTime]']").val(end_time);
    			$("#eo_event_create_cal input[name='eo_event[allday]']").val(allDay);
    			$("#eo_event_create_cal td#date").html(the_date);
    			$('#eo_event_create_cal').dialog('open');
    			$("form.eo_cal input[type='submit']").removeAttr('disabled');
    			$("form.eo_cal input#reset").click(function (event) {
        			$('#eo_event_create_cal').dialog('close');
        		});
    		}
		}
        });

	/* Update time format screen option */
        $('#eofc_time_format').change(function () {
            format = ($('#eofc_time_format').is(":checked") ? 'HH:mm' : 'h:mmtt');
            calendar.fullCalendar('option', 'timeFormat', format);
            $.post(ajaxurl, {
                action: 'eofc-format-time',
                is24: $('#eofc_time_format').is(":checked")
            });
        });


	/* View tabs */
        $('.view-button').click(function (event) {
        	event.preventDefault();
        	$('.view-button').removeClass('nav-tab-active');
        	calendar.fullCalendar('changeView', $(this).attr('id'));
        	$(this).addClass('nav-tab-active');
        });

	/* GoTo 'mini calendar' */
	function eventorganiser_mini_calendar(){
		var element = $("<span class='fc-header-goto'><input type='hidden' id='miniCalendar'/></span>");
		return element;
	}
        $('#miniCalendar').datepicker({
            dateFormat: 'DD, d MM, yy',
            firstDay: parseInt( EO_Ajax.startday, 10 ),
            changeMonth: true,
            monthNamesShort: EO_Ajax.locale.monthAbbrev,
            dayNamesMin: EO_Ajax.locale.dayAbbrev,
            changeYear: true,
            showOn: 'button',
            buttonText: EO_Ajax.locale.gotodate,
            onSelect: function (dateText, dp) {
                calendar.fullCalendar('gotoDate', new Date(Date.parse(dateText)));
            }
        });
        $('button.ui-datepicker-trigger').button();
        
    /* Venue drop-down in modal */
        
      //The venue combobox
        $.widget("ui.combobox", {
        	_create: function () {
        	var c = this.element.hide(),d = c.children(":selected"),e = d.val() ? d.text() : "";
        	var wrapper  = $("<span>").addClass("ui-combobox eo-venue-input").insertAfter(c);
        	var options = {
        			delay: 0,
        			minLength: 0,
        			source: function (a, callback) {
        				$.getJSON(EO_Ajax.ajaxurl + "?callback=?&action=eo-search-venue", a, function (a) {
        					var venues = $.map(a, function (a) {a.label = a.name;return a;});
        					callback(venues);
        				});
        			},
        			select: function (a, b) {
        				$("#venue_select").removeAttr("selected");
        				$("#venue_select").val(b.item.term_id);
        			}
        		};
        		var input = $("<input>").appendTo(wrapper).val(e).addClass("ui-combobox-input").autocomplete(options).addClass("ui-widget-content ui-corner-left");
                     
        		/* Backwards compat with WP 3.3-3.5 (UI 1.8.16-1.9.2)*/
        		var jquery_ui_version = $.ui ? $.ui.version || 0 : -1;
        		var ac_namespace = ( eventorganiser.versionCompare( jquery_ui_version, '1.10' ) >= 0 ? 'ui-autocomplete' : 'autocomplete' );
        		
        		
        		//Apend venue address to drop-down
        		input.data( ac_namespace )._renderItem = function (a, b) {
        			if (b.term_id === 0 ) {
        				return $("<li></li>").data( ac_namespace + "-item", b).append("<a>" + b.label + "</a>").appendTo(a);
        			}
        			//Clean address
        			var address_array = [b.venue_address, b.venue_city, b.venue_state,b.venue_postcode,b.venue_country];
        			var address = $.grep(address_array,function(n){return(n);}).join(', ');
        		
        			/* Backwards compat with WP 3.3-3.5 (UI 1.8.16-1.9.2)*/
        			var li_ac_namespace = ( eventorganiser.versionCompare( jquery_ui_version, '1.10' ) >= 0 ? 'ui-autocomplete-item' : 'item.autocomplete' );

        			return $("<li></li>").data( li_ac_namespace, b)
        				.append("<a>" + b.label + "</br> <span style='font-size: 0.8em'><em>" +address+ "</span></em></a>").appendTo(a);
        		};

        		//Add new / selec buttons
    			var button_height = eventorganiser.is_mp6 ? '25px' : '21px';
    			var button_wrappers = $("<span>").addClass("eo-venue-combobox-buttons").appendTo(wrapper);
    			$("<a style='vertical-align: top;margin: 0px -1px;padding: 0px;height:"+button_height+";'>").attr("title", "Show All Items").appendTo(button_wrappers).button({
    				icons: { primary: "ui-icon-triangle-1-s"},
    				text: false
    			}).removeClass("ui-corner-all").addClass("eo-ui-button ui-corner-right ui-combobox-toggle ui-combobox-button").click(function () {
    				if (input.autocomplete("widget").is(":visible")) {input.autocomplete("close");return;}
    				$(this).blur();
    				input.autocomplete("search", "").focus();
    			});
    			
        	}
        });
        $("#venue_select").combobox();

    

	/* Venue & Category Filters */
	function eventorganiser_cat_dropdown(options){

		var terms = options.categories;
		
		if( !terms ){
			return;
		}

		var html="<select class='eo-cal-filter' id='eo-event-cat'>";
		html+="<option value=''>"+options.buttonText.cat+"</option>";
		for (var i=0;i<terms.length; i++) {
			html+= "<option class='cat-slug-"+terms[i].slug+" cat' value='"+terms[i].slug+"'>"+terms[i].name+"</option>";
		}
		html+="</select>";

		return $("<span class='fc-header-dropdown filter-category'></span>").append(html);
	}

	function eventorganiser_venue_dropdown(options){

		var venues = options.venues;
		
		if( !venues ){
			return;
		}

		var html="<select class='eo-cal-filter' id='eo-event-venue'>";
		html+="<option value=''>"+options.buttonText.venue+"</option>";

		for (var i=0; i<venues.length; i++){
			html+= "<option value='"+venues[i].term_id+"'>"+venues[i].name+"</option>";
		}
		html+="</select>";

		return $("<span class='fc-header-dropdown filter-venue'></span>").append(html);
	}
        $(".eo-cal-filter").change(function () {
            calendar.fullCalendar('rerenderEvents');
        });
        $('.filter-venue .eo-cal-filter').selectmenu({
            wrapperElement: "<span class='fc-header-filter'></span>"
        });
        $('.filter-category .eo-cal-filter').selectmenu({
            wrapperElement: "<span class='fc-header-filter'></span>",
            icons: [{find: '.cat'} ]
        });
        var w = $('#eo-event-venue-button').width() + 30;
        $('#eo-event-venue-button').width(w + 'px');
        $('#eo-event-venue-menu').width(w + 'px');
        var w2 = $('#eo-event-cat-button').width() + 30;
        $('#eo-event-cat-button').width(w2 + 'px');
        $('#eo-event-cat-menu').width(w2 + 'px');
    });
})(jQuery);
/*
 * jQuery UI Selectmenu version 1.4.0pre
 *
 * Copyright (c) 2009-2010 filament group, http://filamentgroup.com
 * Copyright (c) 2010-2012 Felix Nagel, http://www.felixnagel.com
 * Licensed under the MIT (MIT-LICENSE.txt)
 *
 * https://github.com/fnagel/jquery-ui/wiki/Selectmenu
 */

(function( $ ) {

$.widget("ui.selectmenu", {
	options: {
		appendTo: "body",
		typeAhead: 1000,
		style: 'dropdown',
		positionOptions: null,
		width: null,
		menuWidth: null,
		handleWidth: 26,
		maxHeight: null,
		icons: null,
		format: null,
		escapeHtml: false,
		bgImage: function() {}
	},

	_create: function() {
		var self = this, o = this.options;
		
		// make / set unique id
		/* Backwards compat with WP 3.3-3.4 (jQuery UI 1.8.16-1.8.2)*/
		var jquery_ui_version = $.ui ? $.ui.version || 0 : -1;
		var selectmenuId = ( eventorganiser.versionCompare( jquery_ui_version, '1.9' ) >= 0 ) ? this.element.uniqueId().attr( "id" ) : this.element.attr( 'id' ) || 'ui-selectmenu-' + Math.random().toString( 16 ).slice( 2, 10 );
		
		// quick array of button and menu id's
		this.ids = [ selectmenuId, selectmenuId + '-button', selectmenuId + '-menu' ];
		
		// define safe mouseup for future toggling
		this._safemouseup = true;
		this.isOpen = false;

		// create menu button wrapper
		this.newelement = $( '<a />', {
			'class': 'ui-selectmenu ui-widget ui-state-default ui-corner-all',
			'id' : this.ids[ 1 ],
			'role': 'button',
			'href': '#nogo',
			'tabindex': this.element.attr( 'disabled' ) ? 1 : 0,
			'aria-haspopup': true,
			'aria-owns': this.ids[ 2 ]
		});
		this.newelementWrap = $( "<span />" )
			.append( this.newelement )
			.insertAfter( this.element );

		// transfer tabindex
		var tabindex = this.element.attr( 'tabindex' );
		if ( tabindex ) {
			this.newelement.attr( 'tabindex', tabindex );
		}

		// save reference to select in data for ease in calling methods
		this.newelement.data( 'selectelement', this.element );

		// menu icon
		this.selectmenuIcon = $( '<span class="ui-selectmenu-icon ui-icon"></span>' )
			.prependTo( this.newelement );

		// append status span to button
		this.newelement.prepend( '<span class="ui-selectmenu-status" />' );

		// make associated form label trigger focus
		this.element.bind({
			'click.selectmenu':  function( event ) {
				self.newelement.focus();
				event.preventDefault();
			}
		});

		// click toggle for menu visibility
		this.newelement
			.bind( 'mousedown.selectmenu', function( event ) {
				self._toggle( event, true );
				// make sure a click won't open/close instantly
				if ( o.style == "popup" ) {
					self._safemouseup = false;
					setTimeout( function() { self._safemouseup = true; }, 300 );
				}

				event.preventDefault();
			})
			.bind( 'click.selectmenu', function( event ) {
				event.preventDefault();
			})
			.bind( "keydown.selectmenu", function( event ) {
				var ret = false;
				switch ( event.keyCode ) {
					case $.ui.keyCode.ENTER:
						ret = true;
						break;
					case $.ui.keyCode.SPACE:
						self._toggle( event );
						break;
					case $.ui.keyCode.UP:
						if ( event.altKey ) {
							self.open( event );
						} else {
							self._moveSelection( -1 );
						}
						break;
					case $.ui.keyCode.DOWN:
						if ( event.altKey ) {
							self.open( event );
						} else {
							self._moveSelection( 1 );
						}
						break;
					case $.ui.keyCode.LEFT:
						self._moveSelection( -1 );
						break;
					case $.ui.keyCode.RIGHT:
						self._moveSelection( 1 );
						break;
					case $.ui.keyCode.TAB:
						ret = true;
						break;
					case $.ui.keyCode.PAGE_UP:
					case $.ui.keyCode.HOME:
						self.index( 0 );
						break;
					case $.ui.keyCode.PAGE_DOWN:
					case $.ui.keyCode.END:
						self.index( self._optionLis.length );
						break;
					default:
						ret = true;
				}
				return ret;
			})
			.bind( 'keypress.selectmenu', function( event ) {
				if ( event.which > 0 ) {
					self._typeAhead( event.which, 'mouseup' );
				}
				return true;
			})
			.bind( 'mouseover.selectmenu', function() {
				if ( !o.disabled ) $( this ).addClass( 'ui-state-hover' );
			})
			.bind( 'mouseout.selectmenu', function() {
				if ( !o.disabled ) $( this ).removeClass( 'ui-state-hover' );
			})
			.bind( 'focus.selectmenu', function() {
				if ( !o.disabled ) $( this ).addClass( 'ui-state-focus' );
			})
			.bind( 'blur.selectmenu', function() {
				if (!o.disabled) $( this ).removeClass( 'ui-state-focus' );
			});

		// document click closes menu
		$( document ).bind( "mousedown.selectmenu-" + this.ids[ 0 ], function( event ) {
			//check if open and if the clicket targes parent is the same
			if ( self.isOpen && !$( event.target ).closest( "#" + self.ids[ 1 ] ).length ) {
				self.close( event );
			}
		});

		// change event on original selectmenu
		this.element
			.bind( "click.selectmenu", function() {
				self._refreshValue();
			})
			// FIXME: newelement can be null under unclear circumstances in IE8
			// TODO not sure if this is still a problem (fnagel 20.03.11)
			.bind( "focus.selectmenu", function() {
				if ( self.newelement ) {
					self.newelement[ 0 ].focus();
				}
			});

		// set width when not set via options
		if ( !o.width ) {
			o.width = this.element.outerWidth();
		}
		// set menu button width
		this.newelement.width( o.width );

		// hide original selectmenu element
		this.element.hide();

		// create menu portion, append to body
		this.list = $( '<ul />', {
			'class': 'ui-widget ui-widget-content',
			'aria-hidden': true,
			'role': 'listbox',
			'aria-labelledby': this.ids[ 1 ],
			'id': this.ids[ 2 ]
		});
		this.listWrap = $( "<div />", {
			'class': 'ui-selectmenu-menu'
		}).append( this.list ).appendTo( o.appendTo );

		// transfer menu click to menu button
		this.list
			.bind("keydown.selectmenu", function(event) {
				var ret = false;
				switch ( event.keyCode ) {
					case $.ui.keyCode.UP:
						if ( event.altKey ) {
							self.close( event, true );
						} else {
							self._moveFocus( -1 );
						}
						break;
					case $.ui.keyCode.DOWN:
						if ( event.altKey ) {
							self.close( event, true );
						} else {
							self._moveFocus( 1 );
						}
						break;
					case $.ui.keyCode.LEFT:
						self._moveFocus( -1 );
						break;
					case $.ui.keyCode.RIGHT:
						self._moveFocus( 1 );
						break;
					case $.ui.keyCode.HOME:
						self._moveFocus( ':first' );
						break;
					case $.ui.keyCode.PAGE_UP:
						self._scrollPage( 'up' );
						break;
					case $.ui.keyCode.PAGE_DOWN:
						self._scrollPage( 'down' );
						break;
					case $.ui.keyCode.END:
						self._moveFocus( ':last' );
						break;
					case $.ui.keyCode.ENTER:
					case $.ui.keyCode.SPACE:
						self.close( event, true);
						$( event.target ).parents( 'li:eq(0)' ).trigger( 'mouseup' );
						break;
					case $.ui.keyCode.TAB:
						ret = true;
						self.close( event, true );
						$( event.target ).parents( 'li:eq(0)' ).trigger( 'mouseup' );
						break;
					case $.ui.keyCode.ESCAPE:
						self.close( event, true );
						break;
					default:
						ret = true;
				}
				return ret;
			})
			.bind( 'keypress.selectmenu', function( event ) {
				if ( event.which > 0 ) {
					self._typeAhead( event.which, 'focus' );
				}
				return true;
			})
			// this allows for using the scrollbar in an overflowed list
			.bind( 'mousedown.selectmenu mouseup.selectmenu', function() { return false; });

		// needed when window is resized
		$( window ).bind( "resize.selectmenu-" + this.ids[ 0 ], $.proxy( self.close, this ) );
	},

	_init: function() {
		var self = this, o = this.options;

		// serialize selectmenu element options
		var selectOptionData = [];
		this.element.find( 'option' ).each( function() {
			var opt = $( this );
			selectOptionData.push({
				value: opt.attr( 'value' ),
				text: self._formatText( opt.text(), opt ),
				selected: opt.attr( 'selected' ),
				disabled: opt.attr( 'disabled' ),
				classes: opt.attr( 'class' ),
				typeahead: opt.attr( 'typeahead'),
				parentOptGroup: opt.parent( 'optgroup' ),
				bgImage: o.bgImage.call( opt )
			});
		});

		// active state class is only used in popup style
		var activeClass = ( self.options.style == "popup" ) ? " ui-state-active" : "";

		// empty list so we can refresh the selectmenu via selectmenu()
		this.list.html( "" );

		// write li's
		if ( selectOptionData.length ) {
			for ( var i = 0; i < selectOptionData.length; i++ ) {
				var thisLiAttr = { role : 'presentation' };
				if ( selectOptionData[ i ].disabled ) {
					thisLiAttr[ 'class' ] = 'ui-state-disabled';
				}
				var thisAAttr = {
					html: selectOptionData[ i ].text || '&nbsp;',
					href: '#nogo',
					tabindex : -1,
					role: 'option',
					'aria-selected' : false
				};
				if ( selectOptionData[ i ].disabled ) {
					thisAAttr[ 'aria-disabled' ] = selectOptionData[ i ].disabled;
				}
				if ( selectOptionData[ i ].typeahead ) {
					thisAAttr.typeahead = selectOptionData[ i ].typeahead;
				}
				var thisA = $( '<a/>', thisAAttr )
					.bind( 'focus.selectmenu', function() {
						$( this ).parent().mouseover();
					})
					.bind( 'blur.selectmenu', function() {
						$( this ).parent().mouseout();
					});
				var thisLi = $( '<li/>', thisLiAttr )
					.append( thisA )
					.data( 'index', i )
					.addClass( selectOptionData[ i ].classes )
					.data( 'optionClasses', selectOptionData[ i ].classes || '' )
					.bind( "mouseup.selectmenu", function( event ) {
						if ( self._safemouseup && !self._disabled( event.currentTarget ) && !self._disabled( $( event.currentTarget ).parents( "ul > li.ui-selectmenu-group " ) ) ) {
							self.index( $( this ).data( 'index' ) );
							self.select( event );
							self.close( event, true );
						}
						return false;
					})
					.bind( "click.selectmenu", function() {
						return false;
					})
					.bind('mouseover.selectmenu', function( e ) {
						// no hover if diabled
						if ( !$( this ).hasClass( 'ui-state-disabled' ) && !$( this ).parent( "ul" ).parent( "li" ).hasClass( 'ui-state-disabled' ) ) {
							e.optionValue = self.element[ 0 ].options[ $( this ).data( 'index' ) ].value;
							self._trigger( "hover", e, self._uiHash() );
							self._selectedOptionLi().addClass( activeClass );
							self._focusedOptionLi().removeClass( 'ui-selectmenu-item-focus ui-state-hover' );
							$( this ).removeClass( 'ui-state-active' ).addClass( 'ui-selectmenu-item-focus ui-state-hover' );
						}
					})
					.bind( 'mouseout.selectmenu', function( e ) {
						if ( $( this ).is( self._selectedOptionLi() ) ) {
							$( this ).addClass( activeClass );
						}
						e.optionValue = self.element[ 0 ].options[ $( this ).data( 'index' ) ].value;
						self._trigger( "blur", e, self._uiHash() );
						$( this ).removeClass( 'ui-selectmenu-item-focus ui-state-hover' );
					});

				// optgroup or not...
				if ( selectOptionData[ i ].parentOptGroup.length ) {
					var optGroupName = 'ui-selectmenu-group-' + this.element.find( 'optgroup' ).index( selectOptionData[ i ].parentOptGroup );
					if ( this.list.find( 'li.' + optGroupName ).length ) {
						this.list.find( 'li.' + optGroupName + ':last ul' ).append( thisLi );
					} else {
						$( '<li role="presentation" class="ui-selectmenu-group ' + optGroupName + ( selectOptionData[ i ].parentOptGroup.attr( "disabled" ) ? ' ' + 'ui-state-disabled" aria-disabled="true"' : '"' ) + '><span class="ui-selectmenu-group-label">' + selectOptionData[ i ].parentOptGroup.attr( 'label' ) + '</span><ul></ul></li>' )
							.appendTo( this.list )
							.find( 'ul' )
							.append( thisLi );
					}
				} else {
					thisLi.appendTo( this.list );
				}

				// append icon if option is specified
				if ( o.icons ) {
					for ( var j in o.icons ) {
						if (thisLi.is(o.icons[ j ].find)) {
							thisLi
								.data( 'optionClasses', selectOptionData[ i ].classes + ' ui-selectmenu-hasIcon' )
								.addClass( 'ui-selectmenu-hasIcon' );
							var iconClass = o.icons[ j ].icon || "";
							thisLi
								.find( 'a:eq(0)' )
								.prepend( '<span class="ui-selectmenu-item-icon ui-icon ' + iconClass + '"></span>' );
							if ( selectOptionData[ i ].bgImage ) {
								thisLi.find( 'span' ).css( 'background-image', selectOptionData[ i ].bgImage );
							}
						}
					}
				}
			}
		} else {
			$(' <li role="presentation"><a href="#nogo" tabindex="-1" role="option"></a></li>' ).appendTo( this.list );
		}
		// we need to set and unset the CSS classes for dropdown and popup style
		var isDropDown = ( o.style == 'dropdown' );
		this.newelement
			.toggleClass( 'ui-selectmenu-dropdown', isDropDown )
			.toggleClass( 'ui-selectmenu-popup', !isDropDown );
		this.list
			.toggleClass( 'ui-selectmenu-menu-dropdown ui-corner-bottom', isDropDown )
			.toggleClass( 'ui-selectmenu-menu-popup ui-corner-all', !isDropDown )
			// add corners to top and bottom menu items
			.find( 'li:first' )
			.toggleClass( 'ui-corner-top', !isDropDown )
			.end().find( 'li:last' )
			.addClass( 'ui-corner-bottom' );
		this.selectmenuIcon
			.toggleClass( 'ui-icon-triangle-1-s', isDropDown )
			.toggleClass( 'ui-icon-triangle-2-n-s', !isDropDown );

		// set menu width to either menuWidth option value, width option value, or select width
		if ( o.style == 'dropdown' ) {
			this.list.width( o.menuWidth ? o.menuWidth : o.width );
		} else {
			this.list.width( o.menuWidth ? o.menuWidth : o.width - o.handleWidth );
		}

		// reset height to auto
		this.list.css( 'height', 'auto' );
		var listH = this.listWrap.height();
		var winH = $( window ).height();
		// calculate default max height
		var maxH = o.maxHeight ? Math.min( o.maxHeight, winH ) : winH / 3;
		if ( listH > maxH ) this.list.height( maxH );

		// save reference to actionable li's (not group label li's)
		this._optionLis = this.list.find( 'li:not(.ui-selectmenu-group)' );

		// transfer disabled state
		if ( this.element.attr( 'disabled' ) ) {
			this.disable();
		} else {
			this.enable();
		}

		// update value
		this._refreshValue();

		// set selected item so movefocus has intial state
		this._selectedOptionLi().addClass( 'ui-selectmenu-item-focus' );

		// needed when selectmenu is placed at the very bottom / top of the page
		clearTimeout( this.refreshTimeout );
		this.refreshTimeout = window.setTimeout( function () {
			self._refreshPosition();
		}, 200 );
	},

	destroy: function() {
		this.element.removeData( this.widgetName )
			.removeClass( 'ui-selectmenu-disabled' + ' ' + 'ui-state-disabled' )
			.removeAttr( 'aria-disabled' )
			.unbind( ".selectmenu" );

		$( window ).unbind( ".selectmenu-" + this.ids[ 0 ] );
		$( document ).unbind( ".selectmenu-" + this.ids[ 0 ] );

		this.newelementWrap.remove();
		this.listWrap.remove();

		// unbind click event and show original select
		this.element
			.unbind( ".selectmenu" )
			.show();

		// call widget destroy function
		$.Widget.prototype.destroy.apply( this, arguments );
	},

	_typeAhead: function( code, eventType ) {
		var self = this,
			c = String.fromCharCode( code ).toLowerCase(),
			matchee = null,
			nextIndex = null;

		// Clear any previous timer if present
		if ( self._typeAhead_timer ) {
			window.clearTimeout( self._typeAhead_timer );
			self._typeAhead_timer = undefined;
		}
		// Store the character typed
		self._typeAhead_chars = ( self._typeAhead_chars === undefined ? "" : self._typeAhead_chars ).concat( c );
		// Detect if we are in cyciling mode or direct selection mode
		if ( self._typeAhead_chars.length < 2 || ( self._typeAhead_chars.substr( -2, 1 ) === c && self._typeAhead_cycling ) ) {
			self._typeAhead_cycling = true;
			// Match only the first character and loop
			matchee = c;
		} else {
			// We won't be cycling anymore until the timer expires
			self._typeAhead_cycling = false;
			// Match all the characters typed
			matchee = self._typeAhead_chars;
		}

		// We need to determine the currently active index, but it depends on
		// the used context: if it's in the element, we want the actual
		// selected index, if it's in the menu, just the focused one
		var selectedIndex = ( eventType !== 'focus' ? this._selectedOptionLi().data( 'index' ) : this._focusedOptionLi().data( 'index' )) || 0;
		for ( var i = 0; i < this._optionLis.length; i++ ) {
			var thisText = this._optionLis.eq( i ).text().substr( 0, matchee.length ).toLowerCase();
			if ( thisText === matchee ) {
				if ( self._typeAhead_cycling ) {
					if ( nextIndex === null )
						nextIndex = i;
					if ( i > selectedIndex ) {
						nextIndex = i;
						break;
					}
				} else {
					nextIndex = i;
				}
			}
		}

		if ( nextIndex !== null ) {
			// Why using trigger() instead of a direct method to select the index? Because we don't what is the exact action to do,
			// it depends if the user is typing on the element or on the popped up menu
			this._optionLis.eq( nextIndex ).find( "a" ).trigger( eventType );
		}

		self._typeAhead_timer = window.setTimeout( function() {
			self._typeAhead_timer = undefined;
			self._typeAhead_chars = undefined;
			self._typeAhead_cycling = undefined;
		}, self.options.typeAhead );
	},

	// returns some usefull information, called by callbacks only
	_uiHash: function() {
		var index = this.index();
		return {
			index: index,
			option: $( "option", this.element ).get( index ),
			value: this.element[ 0 ].value
		};
	},

	open: function( event ) {
		if ( this.newelement.attr( "aria-disabled" ) != 'true' ) {
			var self = this,
				o = this.options,
				selected = this._selectedOptionLi(),
				link = selected.find("a");

			self._closeOthers( event );
			self.newelement.addClass( 'ui-state-active' );
			self.list.attr( 'aria-hidden', false );
			self.listWrap.addClass( 'ui-selectmenu-open' );

			if ( o.style == "dropdown" ) {
				self.newelement.removeClass( 'ui-corner-all' ).addClass( 'ui-corner-top' );
			} else {
				// center overflow and avoid flickering
				this.list
					.css( "left", -5000 )
					.scrollTop( this.list.scrollTop() + selected.position().top - this.list.outerHeight() / 2 + selected.outerHeight() / 2 )
					.css( "left", "auto" );
			}

			self._refreshPosition();

			if ( link.length ) {
				link[ 0 ].focus();
			}

			self.isOpen = true;
			self._trigger( "open", event, self._uiHash() );
		}
	},

	close: function( event, retainFocus ) {
		if ( this.newelement.is( '.ui-state-active') ) {
			this.newelement.removeClass( 'ui-state-active' );
			this.listWrap.removeClass( 'ui-selectmenu-open' );
			this.list.attr( 'aria-hidden', true );
			if ( this.options.style == "dropdown" ) {
				this.newelement.removeClass( 'ui-corner-top' ).addClass( 'ui-corner-all' );
			}
			if ( retainFocus ) {
				this.newelement.focus();
			}
			this.isOpen = false;
			this._trigger( "close", event, this._uiHash() );
		}
	},

	change: function( event ) {
		this.element.trigger( "change" );
		this._trigger( "change", event, this._uiHash() );
	},

	select: function( event ) {
		if ( this._disabled( event.currentTarget ) ) { return false; }
		this._trigger( "select", event, this._uiHash() );
	},

	widget: function() {
		return this.listWrap.add( this.newelementWrap );
	},

	_closeOthers: function( event ) {
		$( '.ui-selectmenu.ui-state-active' ).not( this.newelement ).each( function() {
			$( this ).data( 'selectelement' ).selectmenu( 'close', event );
		});
		$( '.ui-selectmenu.ui-state-hover' ).trigger( 'mouseout' );
	},

	_toggle: function( event, retainFocus ) {
		if ( this.isOpen ) {
			this.close( event, retainFocus );
		} else {
			this.open( event );
		}
	},

	_formatText: function( text, opt ) {
		if ( this.options.format ) {
			text = this.options.format( text, opt );
		} else if ( this.options.escapeHtml ) {
			text = $( '<div />' ).text( text ).html();
		}
		return text;
	},

	_selectedIndex: function() {
		return this.element[ 0 ].selectedIndex;
	},

	_selectedOptionLi: function() {
		return this._optionLis.eq( this._selectedIndex() );
	},

	_focusedOptionLi: function() {
		return this.list.find( '.ui-selectmenu-item-focus' );
	},

	_moveSelection: function( amt, recIndex ) {
		// do nothing if disabled
		if ( !this.options.disabled ) {
			var currIndex = parseInt( this._selectedOptionLi().data( 'index' ) || 0, 10 );
			var newIndex = currIndex + amt;
			// do not loop when using up key
			if ( newIndex < 0 ) {
				newIndex = 0;
			}
			if ( newIndex > this._optionLis.size() - 1 ) {
				newIndex = this._optionLis.size() - 1;
			}
			// Occurs when a full loop has been made
			if ( newIndex === recIndex ) {
				return false;
			}

			if ( this._optionLis.eq( newIndex ).hasClass( 'ui-state-disabled' ) ) {
				// if option at newIndex is disabled, call _moveFocus, incrementing amt by one
				if( amt > 0 ){ 
					++amt;
				}else{
					--amt;
				}
				this._moveSelection( amt, newIndex );
			} else {
				this._optionLis.eq( newIndex ).trigger( 'mouseover' ).trigger( 'mouseup' );
			}
		}
	},

	_moveFocus: function( amt, recIndex ) {
		var newIndex;
		if ( !isNaN( amt ) ) {
			var currIndex = parseInt( this._focusedOptionLi().data( 'index' ) || 0, 10 );
			newIndex = currIndex + amt;
		} else {
			newIndex = parseInt( this._optionLis.filter( amt ).data( 'index' ), 10 );
		}

		if ( newIndex < 0 ) {
			newIndex = 0;
		}
		if ( newIndex > this._optionLis.size() - 1 ) {
			newIndex = this._optionLis.size() - 1;
		}

		//Occurs when a full loop has been made
		if ( newIndex === recIndex ) {
			return false;
		}

		var activeID = 'ui-selectmenu-item-' + Math.round( Math.random() * 1000 );

		this._focusedOptionLi().find( 'a:eq(0)' ).attr( 'id', '' );

		if ( this._optionLis.eq( newIndex ).hasClass( 'ui-state-disabled' ) ) {
			// if option at newIndex is disabled, call _moveFocus, incrementing amt by one
			if( amt > 0 ){ 
				++amt;
			}else{
				--amt;
			}
			this._moveFocus( amt, newIndex );
		} else {
			this._optionLis.eq( newIndex ).find( 'a:eq(0)' ).attr( 'id',activeID ).focus();
		}

		this.list.attr( 'aria-activedescendant', activeID );
	},

	_scrollPage: function( direction ) {
		var numPerPage = Math.floor( this.list.outerHeight() / this._optionLis.first().outerHeight() );
		numPerPage = ( direction == 'up' ? -numPerPage : numPerPage );
		this._moveFocus( numPerPage );
	},

	_setOption: function( key, value ) {
		this.options[ key ] = value;
		// set
		if ( key == 'disabled' ) {
			if ( value ) this.close();
			this.element
				.add( this.newelement )
				.add( this.list )[ value ? 'addClass' : 'removeClass' ]( 'ui-selectmenu-disabled ' + 'ui-state-disabled' )
				.attr( "aria-disabled" , value );
		}
	},

	disable: function( index, type ){
			// if options is not provided, call the parents disable function
			if ( typeof( index ) == 'undefined' ) {
				this._setOption( 'disabled', true );
			} else {
				if ( type == "optgroup" ) {
					this._toggleOptgroup( index, false );
				} else {
					this._toggleOption( index, false );
				}
			}
	},

	enable: function( index, type ) {
			// if options is not provided, call the parents enable function
			if ( typeof( index ) == 'undefined' ) {
				this._setOption( 'disabled', false );
			} else {
				if ( type == "optgroup" ) {
					this._toggleOptgroup( index, true );
				} else {
					this._toggleOption( index, true );
				}
			}
	},

	_disabled: function( elem ) {
			return $( elem ).hasClass( 'ui-state-disabled' );
	},

	_toggleOption: function( index, flag ) {
		var optionElem = this._optionLis.eq( index );
		if ( optionElem ) {
				optionElem
					.toggleClass( 'ui-state-disabled', flag )
					.find( "a" ).attr( "aria-disabled", !flag );
			if ( flag ) {
				this.element.find( "option" ).eq( index ).attr( "disabled", "disabled" );
			} else {
				this.element.find( "option" ).eq( index ).removeAttr( "disabled" );
			}
		}
	},

	// true = enabled, false = disabled
	_toggleOptgroup: function( index, flag ) {
			var optGroupElem = this.list.find( 'li.ui-selectmenu-group-' + index );
			if ( optGroupElem ) {
				optGroupElem
					.toggleClass( 'ui-state-disabled', flag )
					.attr( "aria-disabled", !flag );
				if ( flag ) {
					this.element.find( "optgroup" ).eq( index ).attr( "disabled", "disabled" );
				} else {
					this.element.find( "optgroup" ).eq( index ).removeAttr( "disabled" );
				}
			}
	},

	index: function( newIndex ) {
		if ( arguments.length ) {
			if ( !this._disabled( $( this._optionLis[ newIndex ] ) ) && newIndex != this._selectedIndex() ) {
				this.element[ 0 ].selectedIndex = newIndex;
				this._refreshValue();
				this.change();
			} else {
				return false;
			}
		} else {
			return this._selectedIndex();
		}
	},

	value: function( newValue ) {
		if ( arguments.length && newValue != this.element[ 0 ].value ) {
			this.element[ 0 ].value = newValue;
			this._refreshValue();
			this.change();
		} else {
			return this.element[ 0 ].value;
		}
	},

	_refreshValue: function() {
		var activeClass = ( this.options.style == "popup" ) ? " ui-state-active" : "";
		var activeID = 'ui-selectmenu-item-' + Math.round( Math.random() * 1000 );
		// deselect previous
		this.list
			.find( '.ui-selectmenu-item-selected' )
			.removeClass( "ui-selectmenu-item-selected" + activeClass )
			.find('a')
			.attr( 'aria-selected', 'false' )
			.attr( 'id', '' );
		// select new
		this._selectedOptionLi()
			.addClass( "ui-selectmenu-item-selected" + activeClass )
			.find( 'a' )
			.attr( 'aria-selected', 'true' )
			.attr( 'id', activeID );

		// toggle any class brought in from option
		var currentOptionClasses = ( this.newelement.data( 'optionClasses' ) ? this.newelement.data( 'optionClasses' ) : "" );
		var newOptionClasses = ( this._selectedOptionLi().data( 'optionClasses' ) ? this._selectedOptionLi().data( 'optionClasses' ) : "" );
		this.newelement
			.removeClass( currentOptionClasses )
			.data( 'optionClasses', newOptionClasses )
			.addClass( newOptionClasses )
			.find( '.ui-selectmenu-status' )
			.html( this._selectedOptionLi().find( 'a:eq(0)' ).html() );

		this.list.attr( 'aria-activedescendant', activeID );
	},

	_refreshPosition: function() {
		var o = this.options,
			positionDefault = {
				of: this.newelement,
				my: "left top",
				at: "left bottom",
				collision: 'flip'
			};

		// if its a pop-up we need to calculate the position of the selected li
		if ( o.style == "popup" ) {
			var selected = this._selectedOptionLi();
			positionDefault.my = "left top" + ( this.list.offset().top - selected.offset().top - ( this.newelement.outerHeight() + selected.outerHeight() ) / 2 );
			positionDefault.collision = "fit";
		}

		this.listWrap
			.removeAttr( 'style' )
			.zIndex( this.element.zIndex() + 2 )
			.position( $.extend( positionDefault, o.positionOptions ) );
	}
});

})( jQuery );
/*!
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
	var define;
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals.
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	function converted(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		try {
			return config.json ? JSON.parse(s) : s;
		} catch(er) {}
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			document.cookie = [
			   				config.raw ? key : encodeURIComponent(key),
			   				'=',
			   				config.raw ? value : encodeURIComponent(value),
			   				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
			   				options.path    ? '; path=' + options.path : '',
			   				options.domain  ? '; domain=' + options.domain : '',
			   				options.secure  ? '; secure' : ''
			   			].join('');
			return document;
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		var result = key ? undefined : {};
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = decode(parts.join('='));

			if (key && key === name) {
				result = converted(cookie);
				break;
			}

			if (!key) {
				result[name] = converted(cookie);
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== undefined) {
			// Must not alter options, thus extending a fresh object...
			$.cookie(key, '', $.extend({}, options, { expires: -1 }));
			return true;
		}
		return false;
	};

}));