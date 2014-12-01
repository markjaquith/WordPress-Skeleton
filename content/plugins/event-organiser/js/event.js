var eo_include_dates;
var eo_exclude_dates;
var eo_viewing_month;
(function($) {
	
//Workaround for indexOf in IE 7&8
if (!Array.prototype.indexOf){
  Array.prototype.indexOf = function(elt /*, from*/){
    var len = this.length;
    var from = Number(arguments[1]) || 0;
    from = (from < 0)? Math.ceil(from) : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++){
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

/**
 * Returns the ordinal suffix of the date
 */
Date.prototype.eoGetOrdinal = function () {
	 var d = this.getDate();
	 switch( d ){
	    case 1:
	    case 21:
	    case 31:
	        return 'st';
	    case 2:
	    case 22:
	        return 'nd';
	    case 3:
	    case 23:
	        return 'rd';
	    default:
	        return 'th';
	 }
};

/**
 * Given a month (start & end ) and event schedule, calculates
 * the dates which occur by rule in that month.
 */
function eo_generate_dates_by_schedule_rule( rule, month_start,month_end ){
	
	//Helper array
	var ical_weekdays = new Array("SU", "MO", "TU", "WE", "TH", "FR", "SA"),
	eo_occurrences_by_rule = [],
	count_days, pointer = false;
	
    //If event starts in previous month - how many days from start to first occurrence in current month?
    // Depends on occurrence (and 'stream' for weekly events.
    switch (rule.schedule) {
    	case 'once':
    	case 'custom':
    		var formateddate = $.datepicker.formatDate('yy-mm-dd', rule.start);
    		eo_occurrences_by_rule.push(formateddate);
    		return eo_occurrences_by_rule;
    	/*break;*/
    		
    	case 'daily':
    		if ( rule.start < month_start ) {
    			count_days = Math.abs((month_start - rule.start) / (1000 * 60 * 60 * 24)) - 1;
    			count_days = count_days % rule.frequency;
    		} else {
    			count_days = parseInt( rule.start.getDate(), 10 );
    		}
    		var skip = rule.frequency;
    		var streams = [];
    		var start_stream = new Date(month_start);
    		start_stream.setDate(month_start.getDate() + (count_days - 1));
    		streams.push(start_stream);
    	break;

    	case 'weekly':
    		var month_start_day = month_start.getDay();
        
    		streams = [];
    		$.each(rule.schedule_meta, function(index, value ) {
    			index = ical_weekdays.indexOf(value);
    			start_stream = new Date(rule.start);
    			start_stream.setDate(rule.start.getDate() + (index - rule.start.getDay() + 7) % 7);
    			if (start_stream < month_start) {
    				count_days = Math.abs((month_start - rule.start) / (1000 * 60 * 60 * 24));
    				count_days = count_days - count_days % (rule.frequency * 7);
    				start_stream.setDate(start_stream.getDate() + count_days);
    			}
    			streams.push(start_stream);
    		});
    		skip = 7 * rule.frequency;
        break;

        //These are easy
        case 'monthly':
        	var month_difference = (month_start.getFullYear() - rule.start.getFullYear()) * 12 + ( month_start.getMonth() - rule.start.getMonth() );
        	if (month_difference % rule.frequency !== 0) {
        		return;
        	}
        	
    		if ( rule.schedule_meta.match(/BYMONTHDAY=(\d+)/) ) {
        		var day = rule.start.getDate();
        		var daysinmonth = month_end.getDate();
        		//Check for short months
        		if ( day <= daysinmonth) {
        			//If valid date
        			pointer = new Date( month_start.getFullYear(), month_start.getMonth(), day);
        		}
            } else {
        		//e.g. 3rd friday of month:
            	var matches = rule.schedule_meta.match(/BYDAY=(\d+)(MO|TU|WE|TH|FR|SA|SU)/);
            	var n = parseInt( matches[1], 10 ) -1;    //0=>first,1=>second,...,4=>last            	
        		var occurrence_day = rule.start.getDay(), occurence_date;
        		
        		if (n >= 4) {
        			//Last day
        			var month_end_day = month_end.getDay();
        			occurence_date = month_end.getDate() + (occurrence_day - month_end_day - 7) % 7;
        		} else {
        			//Want date of (n+1)th X of month. 
        			month_start_day = month_start.getDay();//0=sun,..
        			var offset = (occurrence_day - month_start_day + 7) % 7;//How many days till first X of the month
        			occurence_date = offset + n * 7 + 1;
        		}
        		pointer = new Date(month_start);
        		pointer.setDate(occurence_date);
            }
    		
    		if ( pointer && pointer <= rule.schedule_last ) {
    			//If before end
    			formateddate = $.datepicker.formatDate('yy-mm-dd', pointer);
    			eo_occurrences_by_rule.push(formateddate);
    		}
    		return eo_occurrences_by_rule;
        /*break;*/

        case 'yearly':
        	var year_difference = (month_start.getFullYear() - rule.start.getFullYear());
        	if (year_difference % rule.frequency !== 0) {
        		return eo_occurrences_by_rule;
        	}

        	var dateCheck = new Date( month_start.getFullYear(), rule.start.getMonth(), rule.start.getDate() );

        	if ( month_start.getMonth() == rule.start.getMonth() && dateCheck.getMonth() == rule.start.getMonth()) {
        		pointer = new Date(rule.start);
        		pointer.setYear(month_start.getFullYear());
        		if (pointer <= rule.schedule_last ) {
        			//If before end
        			formateddate = $.datepicker.formatDate('yy-mm-dd', pointer);
        			eo_occurrences_by_rule.push(formateddate);
        		}
        	}
        	return eo_occurrences_by_rule;
        /*break;*/

        default:
        	return eo_occurrences_by_rule;
        /*break;*/

    }
    //End switch
    //While in current month, and event has not finished - generate occurrences.
    for (var x in streams) {
        pointer = new Date(streams[x]);
        while (pointer <= month_end && pointer <= rule.schedule_last) {
            formateddate = $.datepicker.formatDate('yy-mm-dd', pointer);
            eo_occurrences_by_rule.push(formateddate);
            pointer.setDate(pointer.getDate() + skip);
        }
    }
    return eo_occurrences_by_rule;
}


/**
 * Schedule picker 'view'/'controller' object
 */
window.eventOrganiserSchedulePicker = {
	/**
	 * @this {Type}
	 */
	init: function( options ){
		var self = this;
	
		this.options = options;
		this.schedule = options.schedule;
		this.set_up_datepickers();
		this.set_up_timepickers();
		        
        //On input, update form
        $(".event-date :input, .eo-all-day-toggle").change(function(o) {
        	self.update_schedule();
        	self.update_form();
            if ( !$(this).hasClass('eo-all-day-toggle') ) {
            	//When rule changes, wipe include/exclude dates clean
            	//TODO
                self.update_occurrencepicker_rules();
            }
        });
        
        //Initiate form
        this.update_schedule();
        this.update_form();
        
        var now = new Date();
        eo_viewing_month = [ now.getFullYear(), now.getMonth() + 1 ];
        this.schedule.generate_dates_by_rule(now.getFullYear(), now.getMonth() + 1, {});
	},

	set_up_datepickers: function(){
		var self = this;
		
		var views = this.options.views;
		var locale = this.options.locale;
		
		//Init data
		var start =  $(views.start_date).data('eo-datepicker','start');
		var end = $(views.end_date).data('eo-datepicker','end');
		$(views.is_all_day).addClass('eo-all-day-toggle');
		
		//Date pickers
		//Schedule last
		if( $(views.schedule_last_date) ){
			var schedule_last = $(views.schedule_last_date)
					.datepicker({
						dateFormat: this.options.format,
	                	changeMonth: true,
	                	changeYear: true,
	                	monthNamesShort: locale.monthAbbrev,
	                	dayNamesMin: locale.dayAbbrev,
	                	isRTL: locale.isrtl,
	                	firstDay: parseInt( this.options.startday, 10 )
					})
					.data('eo-datepicker','schedule_last');
		}	
		
		//Start & End
        var dates = $( views.start_date + ', ' + views.end_date).datepicker({
			dateFormat: this.options.format,
            changeMonth: true,
            changeYear: true,
            monthNamesShort: locale.monthAbbrev,
            dayNamesMin: locale.dayAbbrev,
            firstDay: parseInt( this.options.startday, 10 ),
            onSelect: function(selectedDate) {
            	//Ensure that start date comes before end date
                var option = ( 'start' == $(this).data('eo-datepicker')? "minDate": "maxDate" ),
                instance = $(this).data("datepicker"),
                date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
                if (this.id == "from_date") {
                	//If updating start date, ensure recurrence end falls after this
                	schedule_last.datepicker("option", "minDate", date);
                }
                
                var startDate = $( views.start_date ).datepicker( 'getDate' );
                var endDate   = $( views.end_date ).datepicker( 'getDate' );
                if( startDate.getTime() != endDate.getTime() ){
                	var time = { hour: null, minute: null };
                	$( views.start_time + ", " + views.end_time ).eotimepicker('option', { maxTime: time, minTime: time });
                }
                
                //Replace with do_action
                self.update_schedule();
                self.update_occurrencepicker_rules();
                self.update_form();
            }
        });
        
        //Occurrence picker
        var dp = $(views.occurrence_picker);
        if (dp.length > 0) {
            dp.datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
                monthNamesShort: locale.monthAbbrev,
                dayNamesMin: locale.dayAbbrev,
                firstDay: parseInt( this.options.startday, 10 ),
                onSelect: function( date ){
                		eventOrganiserSchedule.add_or_remove_date( date );
            	        $( eventOrganiserSchedulePicker.options.views.include ).val(eventOrganiserSchedule.include.join(',\r\n'));
            	        $( eventOrganiserSchedulePicker.options.views.exclude ).val(eventOrganiserSchedule.exclude.join(',\r\n'));	
                },
                beforeShowDay: function (date) {
                    var date_str = $.datepicker.formatDate('yy-mm-dd', date);
                    var isEventful = eventOrganiserSchedule.is_date_eventful(date_str);
                     if (isEventful[0]) {
                         return [true, "ui-state-active", ""];
                     }
                     return [true, "ui-state-disabled", ''];
                 },
                onChangeMonthYear: eventOrganiserSchedule.generate_dates_by_rule
            })
            .hide().find('.ui-datepicker-inline').click(function(e) {
                    if (!e)
                        e = window.event;
                   e.cancelBubble = true;
                   if (e.stopPropagation)
                       e.stopPropagation();
               });
            
            $('html').click(function() {
                dp.hide();
                $(views.occurrence_picker_toggle).val(locale.showDates);
            });
        }

        //Show/hide calendar
        $(views.occurrence_picker_toggle).click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            dp.toggle();
            if( dp.is(":visible") ){
            	$(this).val(locale.hideDates);
            	$( views.occurrence_picker ).datepicker("refresh");
            }else{
            	$(this).val(locale.showDates);
            }

        });

        
	},
		
    //When rule changes, wipe include/exclude dates clean
	update_occurrencepicker_rules: function() {
        eo_exclude_dates = [];
        eo_include_dates = [];
        //eo_update_inc_ex_Input();
        this.schedule.generate_dates_by_rule(eo_viewing_month[0], eo_viewing_month[1], {});
        $(this.options.views.occurrence_picker).datepicker("refresh");
    },
	
	set_up_timepickers: function(){
		
		
		var options = this.options;
		var views = this.options.views;
        //Time pickers
		$( views.start_time ).data( 'eo-event-data', 'start-time' );
		$( views.end_time ).data( 'eo-event-data', 'end-time' );
        $( views.start_time + ', ' +  views.end_time).eotimepicker({
            showPeriodLabels: !options.is24hour,
            showPeriod: !this.options.is24hour,
            showLeadingZero: options.is24hour,
            periodSeparator: '',
            amPmText: options.locale.meridian,
            hourText: options.locale.hour,
            minuteText: options.locale.minute,
            isRTL: options.locale.isrtl,
    		onSelect: function( timeString, endTimePickerInst ){
    			var startDate = $( views.start_date ).datepicker( 'getDate' );
    			var endDate   = $( views.end_date ).datepicker( 'getDate' );

    			if( startDate.getTime() == endDate.getTime() ){
    	            	
    				var time = {
    					hour: endTimePickerInst.hours,
    					minute: endTimePickerInst.minutes,
    				};
    					
    				if( 'start-time' == $( endTimePickerInst.input ).data( 'eo-event-data' ) ){
    					$( views.end_time ).eotimepicker('option', { minTime: time });
    				}else{
    					$( views.start_time ).eotimepicker('option', { maxTime: time });
    				}
    	            		
    			}
    	            		
    		}
        }).addClass('eo-time-picker');
	},
	
	/**
	 * @this;
	 */
	update_schedule: function(){
	    
		var c = new Array("SU", "MO", "TU", "WE", "TH", "FR", "SA");
	    var views = this.options.views;
	    
	    var schedule ={
	    	schedule: $(views.schedule).val(),
	    	frequency: parseInt( $(views.frequency).val(), 10 ),
	    	schedule_last: $(views.schedule_last_date).datepicker("getDate"),
	    	start: $(views.start_date).datepicker("getDate"),
	    	end: $(views.end_date).datepicker("getDate"),
	    	is_all_day: $(views.is_all_day).attr("checked"),
	    	include: $(views.include).length > 0 ? $(views.include).val().split(",") : [],
	    	exclude: $(views.exclude).length > 0 ? $(views.exclude).val().split(",") : []
	    };

	    if( schedule.schedule == 'weekly' ){
	    	schedule.schedule_meta = [];
			if ( $(views.week_repeat+" :checkbox:checked").length === 0) {
				var day = schedule.start.getDay();
	        	$(views.week_repeat+" :checkbox[value='" + c[day] + "']").attr("checked", true);
	        }
			$(views.week_repeat+" :checkbox:checked").each(function() {
	    		schedule.schedule_meta.push( $(this).val() );
	        });
	   
	    }else if( schedule.schedule == 'monthly' ){
	    	if( $(views.month_repeat+" :radio:checked").val() == "BYMONTHDAY=" ){
	    		schedule.schedule_meta = "BYMONTHDAY=" + schedule.start.getDate();
	    	}else{
	        	var dayInt = schedule.start.getDay() % 7;
	        	var n = parseInt( Math.floor((schedule.start.getDate() - 1) / 7), 10 );
	        	schedule.schedule_meta = "BYDAY=" + (n+1) + c[dayInt];
	    	}
	    }
		
		this.schedule = eventOrganiserSchedule.init(schedule);
		
        //Backwards compat:
        eo_exclude_dates = schedule.exclude;
        eo_include_dates = schedule.include;

	},
	
	
	update_form: function(){
    	
    	var view = this.options.views;
    	var locale = this.options.locale;
    	var schedule = this.schedule;
        var speed = 700;
        
    	$(".event-date :input").attr("disabled", !this.options.editable).toggleClass("ui-state-disabled", !this.options.editable);
    	
    	if( this.options.editable ){
    		$(view.start_time+', '+view.end_time).attr("disabled", schedule.is_all_day ).toggleClass("ui-state-disabled", schedule.is_all_day );
    	}
    	
        if( schedule.schedule == 'once' || schedule.schedule == 'custom' ){
        	$(view.recurrence_section+" :input").attr("disabled", true );
        	$(view.recurrence_section).hide();
        }else{
        	$(view.recurrence_section+" :input").attr("disabled", false );
        	$(view.recurrence_section).fadeIn(speed);
        }
        	
        switch ( schedule.schedule ) {
        	case "once":
        	case "custom":
        		$(schedule.frequency).val("1");
        		$(view.month_repeat+', '+view.week_repeat).show();
            break;
            
        	case "weekly":
        		if ( schedule.frequency > 1) {
        			$(view.schedule_span).text(locale.weeks);
                } else {
                	$(view.schedule_span).text(locale.week);
                }
        		
        		$(view.week_repeat).fadeIn(speed);
        		$(view.week_repeat+" :input").attr("disabled", false);
        		$(view.month_repeat).hide();
        		$(view.month_repeat+" :input").attr("disabled", true );
        		
            break;
        
        	case "monthly":
        		if ( schedule.frequency > 1 ) {
        			$(view.schedule_span).text(locale.months);
                } else {
                	$(view.schedule_span).text(locale.month);
                }
        		
        		$(view.month_repeat).fadeIn(speed);
        		$(view.month_repeat+" :input").attr("disabled", false);
        		$(view.week_repeat).hide();
        		$(view.week_repeat+" :input").attr("disabled", true );
            break;
            
        	case "daily":
        		if ( schedule.frequency > 1 ) {
        			$(view.schedule_span).text(locale.days);
                } else {
                	$(view.schedule_span).text(locale.day);
                }
        		$(view.week_repeat + ', ' + view.month_repeat).hide();
        		$(view.week_repeat + ' :input, ' + view.month_repeat+" :input").attr("disabled", true );
            break;
        
        	case "yearly":
        		if ( schedule.frequency > 1 ) {
        			$(view.schedule_span).text(locale.years);
                } else {
                	$(view.schedule_span).text(locale.year);
                }
        		$(view.week_repeat + ', ' + view.month_repeat).hide();
        		$(view.week_repeat + ' :input, ' + view.month_repeat+" :input").attr("disabled", true );
            break;
        }
        
        if ($("#venue_select").val() === null) {
            $("tr.venue_row").hide();
        }
            
        //Generate summary
        $(view.summary).html( schedule.generate_summary( locale ) );
	}
			
};


eventOrganiserSchedule = {
		self: this,
		/**
		 * Schedule picker 'model' object
		 * @this element
		 */
	    init: function ( schedule ) {
	    	
	    	var self = this;
	    	
		    var defaults = {
		    	schedule: 'once',
		    	frequency: 1,
		    	schedule_last: new Date(),
		    	start: new Date(),
		    	end: new Date(),
		    	is_all_day: false,
		    	dates_by_rule: []
		    };
		    schedule = $.extend({}, defaults, schedule);
		    
		    for(var prop in schedule) {
		        this[prop] = schedule[prop];
		    }
		    return this;
	    },

	    /**
	     * @this element
	     */
		generate_dates_by_rule: function(year,month,inst){
		    
	    	//month is 1-12.
	        var eo_occurrences_by_rule = [], eo_viewing_month = [year, month];
	        
	        //Get month start/end dates. Date expects month 0-11.
	        var month_start = new Date(year, month-1, 1);
	        var nxt_mon = new Date(year, month, 1);
	        var month_end = new Date(nxt_mon - 1);
	       
	        if ( eventOrganiserSchedule.schedule_last < month_start || eventOrganiserSchedule.start > month_end) {
	        	return;
	        }
	        
	        eventOrganiserSchedule.dates_by_rule = eo_generate_dates_by_schedule_rule( eventOrganiserSchedule, month_start,month_end );
		},
		
		/**
		 * Given an event schedule returns a string summary describing it.
		 * Schedule is an object with similar properties as those accepted by
		 * eo_insert_event() (in php). 
		 * 
		 * @this element
		 */
	    generate_summary: function( locale ){
	    	//Locale
	    	
	        var b = locale.weekDay;
	        var summary = locale.summary + " ";
	        var options = {
	            monthNamesShort: locale.monthAbbrev,
	            dayNamesMin: locale.dayAbbrev,
	            monthNames: locale.monthNames
	        };
	        var schedule = this.schedule;
	        	
	        //Helper array
	        var c = new Array("SU", "MO", "TU", "WE", "TH", "FR", "SA");
	        
	        switch ( schedule ) {
	        
	        	case "once":
	        		return "This event will be a one-time event";
	        	/*break;*/
	        	
	        	case "custom":
	        	case "daily":
	        		if ( this.frequency > 1) {
	        			summary += sprintf(locale.dayPlural, this.frequency);
	        		} else {
	        			summary += locale.daySingle;
	        		}
	            break;
	            
	        	case "weekly":
	        		if ( this.frequency > 1 ) {
	        			summary += sprintf( locale.weekPlural, this.frequency );
	                } else {
	                	summary += locale.weekSingle;
	                }
	        		var days = $.map( this.schedule_meta, function(value, index){ return b[c.indexOf(value)]; } );
	        		summary += " " + days.join(', ');
	            break;
	            
	        	case "monthly":
	        		if ( this.frequency > 1 ) {
	        			summary += sprintf(locale.monthPlural, this.frequency);
	        		} else {
	        			summary += locale.monthSingle;
	        		}
	        		if ( this.schedule_meta.match(/BYMONTHDAY=(\d+)/) ) {
	        			summary = summary + " " + this.start.getDate() + this.start.eoGetOrdinal();
	                } else {
	                	var matches = this.schedule_meta.match(/BYDAY=(\d+)(MO|TU|WE|TH|FR|SA|SU)/);
	                	var n = parseInt( matches[1], 10 ) -1;
	                	summary = summary + " " + locale.occurrence[n] + " " + b[c.indexOf(matches[2])];
	                }
	            break;
	            
	        	case "yearly":
	        		if (this.frequency > 1) {
	        			summary += sprintf(locale.yearPlural, this.frequency);
	                } else {
	                	summary += locale.yearSingle;
	                }
	        		summary = summary + " " + $.datepicker.formatDate("MM d", this.start, options) + this.start.eoGetOrdinal();
	        	break;
	        }
	        
	        if ( this.schedule_last !== null ) {
	            summary = summary + " " + locale.until + " " + $.datepicker.formatDate("MM d'" + this.schedule_last.eoGetOrdinal() + "' yy", this.schedule_last, options);
	        }
	        
	        return summary;
	    },
	    
	    
	    //Is given date an occurrence of the event?
	    is_date_eventful: function(date) {
	        var index = $.inArray(date, eventOrganiserSchedule.dates_by_rule);

	        if (index > -1) {
	            //Occurs by rule - is it excluded manually?
	            var excluded = $.inArray(date, eventOrganiserSchedule.exclude);
	            if (excluded > -1) {
	                return [false, excluded];
	            } else {
	                return [true, -1];
	            }
	        } else {
	            //Doesn't occurs by rule - is it included manually?
	            var included = $.inArray(date, eventOrganiserSchedule.include);
	            if (included > -1) {
	                return [true, included];
	            } else {
	                return [false, -1];
	            }
	        }
	    },

	    //When a date is selected, add or remove it based on current state
	    add_or_remove_date: function (date, inst) {
	        var isEventful = eventOrganiserSchedule.is_date_eventful(date),index;
	        if ( isEventful[0] ) {
	            //Date is eventful. Remove date
	            index = isEventful[1];
	            if ( index > -1 ) {
	                //Date was manually included
	            	eventOrganiserSchedule.include.splice(index, 1);
	   	    	 
	            } else {
	                //Date was eventful by rule
	    	        if ( $.inArray( date, eventOrganiserSchedule.exclude ) < 0)
	    	        	eventOrganiserSchedule.exclude.push(date);
	            }
	            
	        } else {
	            //Date is not eventful. Add date
	            index = isEventful[1];
	            if (index > -1) {
	                //Date was manually excluded
	            	eventOrganiserSchedule.exclude.splice(index, 1);
	            } else {
	                //Date was not an event by rule
	    	        if ( $.inArray( date, eventOrganiserSchedule.include ) < 0)
	    	        	eventOrganiserSchedule.include.push(date);
	            }
	        }
	        //Backwards compat:
	        eo_exclude_dates = eventOrganiserSchedule.exclude;
	        eo_include_dates = eventOrganiserSchedule.include;
	        
	        
	    }
};

})(jQuery);



/*! 
 * sprintf.js | Copyright (c) 2007-2013 Alexandru Marasteanu <hello at alexei dot ro> | 3 clause BSD license 
 * https://github.com/alexei/sprintf.js
 * */
(function(ctx) {
	var sprintf = function() {
		if (!sprintf.cache.hasOwnProperty(arguments[0])) {
			sprintf.cache[arguments[0]] = sprintf.parse(arguments[0]);
		}
		return sprintf.format.call(null, sprintf.cache[arguments[0]], arguments);
	};

	sprintf.format = function(parse_tree, argv) {
		var cursor = 1, tree_length = parse_tree.length, node_type = '', arg, output = [], i, k, match, pad, pad_character, pad_length;
		for (i = 0; i < tree_length; i++) {
			node_type = get_type(parse_tree[i]);
			if (node_type === 'string') {
				output.push(parse_tree[i]);
			}
			else if (node_type === 'array') {
				match = parse_tree[i]; // convenience purposes only
				if (match[2]) { // keyword argument
					arg = argv[cursor];
					for (k = 0; k < match[2].length; k++) {
						if (!arg.hasOwnProperty(match[2][k])) {
							throw(sprintf('[sprintf] property "%s" does not exist', match[2][k]));
						}
						arg = arg[match[2][k]];
					}
				}
				else if (match[1]) { // positional argument (explicit)
					arg = argv[match[1]];
				}
				else { // positional argument (implicit)
					arg = argv[cursor++];
				}

				if (/[^s]/.test(match[8]) && (get_type(arg) != 'number')) {
					throw(sprintf('[sprintf] expecting number but found %s', get_type(arg)));
				}
				switch (match[8]) {
					case 'b': arg = arg.toString(2); break;
					case 'c': arg = String.fromCharCode(arg); break;
					case 'd': arg = parseInt(arg, 10); break;
					case 'e': arg = match[7] ? arg.toExponential(match[7]) : arg.toExponential(); break;
					case 'f': arg = match[7] ? parseFloat(arg).toFixed(match[7]) : parseFloat(arg); break;
					case 'o': arg = arg.toString(8); break;
					case 's': arg = ((arg = String(arg)) && match[7] ? arg.substring(0, match[7]) : arg); break;
					case 'u': arg = arg >>> 0; break;
					case 'x': arg = arg.toString(16); break;
					case 'X': arg = arg.toString(16).toUpperCase(); break;
				}
				arg = (/[def]/.test(match[8]) && match[3] && arg >= 0 ? '+'+ arg : arg);
				pad_character = match[4] ? match[4] == '0' ? '0' : match[4].charAt(1) : ' ';
				pad_length = match[6] - String(arg).length;
				pad = match[6] ? str_repeat(pad_character, pad_length) : '';
				output.push(match[5] ? arg + pad : pad + arg);
			}
		}
		return output.join('');
	};

	sprintf.cache = {};

	sprintf.parse = function(fmt) {
		var _fmt = fmt, match = [], parse_tree = [], arg_names = 0;
		while (_fmt) {
			if ((match = /^[^\x25]+/.exec(_fmt)) !== null) {
				parse_tree.push(match[0]);
			}
			else if ((match = /^\x25{2}/.exec(_fmt)) !== null) {
				parse_tree.push('%');
			}
			else if ((match = /^\x25(?:([1-9]\d*)\$|\(([^\)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(_fmt)) !== null) {
				if (match[2]) {
					arg_names |= 1;
					var field_list = [], replacement_field = match[2], field_match = [];
					if ((field_match = /^([a-z_][a-z_\d]*)/i.exec(replacement_field)) !== null) {
						field_list.push(field_match[1]);
						while ((replacement_field = replacement_field.substring(field_match[0].length)) !== '') {
							if ((field_match = /^\.([a-z_][a-z_\d]*)/i.exec(replacement_field)) !== null) {
								field_list.push(field_match[1]);
							}
							else if ((field_match = /^\[(\d+)\]/.exec(replacement_field)) !== null) {
								field_list.push(field_match[1]);
							}
							else {
								throw('[sprintf] huh?');
							}
						}
					}
					else {
						throw('[sprintf] huh?');
					}
					match[2] = field_list;
				}
				else {
					arg_names |= 2;
				}
				if (arg_names === 3) {
					throw('[sprintf] mixing positional and named placeholders is not (yet) supported');
				}
				parse_tree.push(match);
			}
			else {
				throw('[sprintf] huh?');
			}
			_fmt = _fmt.substring(match[0].length);
		}
		return parse_tree;
	};

	var vsprintf = function(fmt, argv, _argv) {
		_argv = argv.slice(0);
		_argv.splice(0, 0, fmt);
		return sprintf.apply(null, _argv);
	};

	/**
	 * helpers
	 */
	function get_type(variable) {
		return Object.prototype.toString.call(variable).slice(8, -1).toLowerCase();
	}

	function str_repeat(input, multiplier) {
		for (var output = []; multiplier > 0; output[--multiplier] = input) {/* do nothing */}
		return output.join('');
	}

	/**
	 * export to either browser or node.js
	 */
	ctx.sprintf = sprintf;
	ctx.vsprintf = vsprintf;
})(typeof exports != "undefined" ? exports : window);
