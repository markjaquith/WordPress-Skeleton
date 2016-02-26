jQuery(document).ready(function(jQuery) {

	jQuery(".bf-select2").select2({
		placeholder: "Select an option"
	});

	jQuery(document.body).on('change', '.bf_hidden_checkbox' ,function(){

		var ids = jQuery(this).attr('bf_hidden_checkbox');

		if(!ids)
			return;

		if(jQuery(this).is(':checked')){
			ids = ids.split(" ");
			ids.forEach(function(entry) {
				jQuery('#table_row_'+entry).removeClass('hidden');
				jQuery('#'+entry).removeClass('hidden');
			});
		} else {
			ids = ids.split(" ");
			ids.forEach(function(entry) {
				jQuery('#table_row_'+entry).addClass('hidden');
			});
		}

	});

	jQuery('.bf_tax_select').live('change', function() {

		var id 		= jQuery(this).attr('id');
		var taxonomy 	= jQuery(this).val();
		var taxonomy_default = jQuery("#taxonomy_default_"+id);


		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "buddyforms_update_taxonomy_default",
				"taxonomy": taxonomy,
			},
			success: function(data){
				if(data != false){
					taxonomy_default.val(null).trigger("change");
					taxonomy_default.select2({ placeholder: "Select default term" }).trigger("change");

					taxonomy_default.html(data);
				}

			},
			error: function() {
				alert('Something went wrong.. ;-(sorry)');
			}
		});

	});


	jQuery('#publish').click(function(){

		var create_new_form_name                    = jQuery('[name="post_title"]').val();
		var create_new_form_singular_name           = jQuery('[name="buddyforms_options[singular_name]"]').val();
		var create_new_form_post_type               = jQuery('[name="buddyforms_options[post_type]"]').val();
		var create_new_form_attached_page           = jQuery('[name="buddyforms_options[attached_page]"]').val();

		var error = false;
		if( create_new_form_name === ''){
			jQuery('[name="post_title"]').removeClass('bf-ok');
			jQuery('[name="post_title"]').addClass('bf-error');
			error = true;
		} else {
			jQuery('[name="post_title"]').removeClass('bf-error');
			jQuery('[name="post_title"]').addClass('bf-ok');
		}


		if( create_new_form_singular_name === ''){
			jQuery('[name="buddyforms_options[singular_name]"]').removeClass('bf-ok');
			jQuery('[name="buddyforms_options[singular_name]"]').addClass('bf-error');
			error = true;
		} else {
			jQuery('[name="buddyforms_options[singular_name]"]').removeClass('bf-error');
			jQuery('[name="buddyforms_options[singular_name]"]').addClass('bf-ok');
		}

		if( create_new_form_post_type === 'none'){
			jQuery('[name="buddyforms_options[post_type]"]').removeClass('bf-ok');
			jQuery('[name="buddyforms_options[post_type]"]').addClass('bf-error');
		} else {
			jQuery('[name="buddyforms_options[post_type]"]').removeClass('bf-error');
			jQuery('[name="buddyforms_options[post_type]"]').addClass('bf-ok');
		}

		if( create_new_form_attached_page === 'none'){
			jQuery('[name="buddyforms_options[attached_page]"]').removeClass('bf-ok');
			jQuery('[name="buddyforms_options[attached_page]"]').addClass('bf-error');
			error = true;
		} else {
			jQuery('[name="buddyforms_options[attached_page]"]').removeClass('bf-error');
			jQuery('[name="buddyforms_options[attached_page]"]').addClass('bf-ok');
		}


		// traverse all the required elements looking for an empty one
		jQuery("#post input[required]").each(function() {

			// if the value is empty, that means that is invalid
			if (jQuery(this).val() == "") {

				// hide the currently open accordion and open the one with the required field
				jQuery(".accordion-body.collapse.in").removeClass("in");
				jQuery(this).closest(".accordion-body.collapse").addClass("in").css("height","auto");
				jQuery('#buddyforms_form_setup').removeClass('closed');
				jQuery('#buddyforms_form_elements').removeClass('closed');

				jQuery("html, body").animate({ scrollTop: jQuery(this).offset().top - 250 }, 1000);

				// stop scrolling through the required elements
				return false;
			}
		});


		if(error === true){
			return false;
		}

	});

	var bf_getUrlParameter = function bf_getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};

	jQuery('.bf_add_element_action').click(function(){

		var action = jQuery(this);
		var post_id = bf_getUrlParameter('post');

		if(post_id == undefined)
			post_id = 0;

		var fieldtype	= jQuery(this).data("fieldtype");
		var unique		= jQuery(this).data("unique");
		var fieldtype	= jQuery(this).data("fieldtype");

		var exist = jQuery("#sortable_buddyforms_elements ." + fieldtype);

		if(unique == 'unique'){
			if (exist.length){
				alert('This element can only be added once into each form');
				return false;
		    }
		}

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "buddyforms_display_form_element", "fieldtype": fieldtype, "unique": unique, "post_id": post_id },
			success: function(data){
				if(data == 'unique'){
					alert('This element can only be added once into each form');
					return false;
				}

				data = data.replace('accordion-body collapse','accordion-body in collapse');

				var myvar = action.attr('href');
				var arr = myvar.split('/');
				jQuery('#sortable_buddyforms_elements').append(data);

				bf_update_list_item_number();

				jQuery('#buddyforms_form_elements').removeClass('closed');
				jQuery("html, body").animate({ scrollTop: jQuery('#buddyforms_form_elements ul li:last').offset().top - 200 }, 1000);

			},
			error: function() {
				alert('Something went wrong ;-(sorry)');
			}
		});
		return false;
	});

	jQuery(document).on('click','.bf_delete_field',function() {

		var del_id = jQuery(this).attr('id');

		if (confirm('Delete Permanently'))
			jQuery("#field_" + del_id).remove();

		return false;
	});
	jQuery(document).on('click','.bf_delete_trigger',function() {

		var del_id = jQuery(this).attr('id');

		if (confirm('Delete Permanently'))
			jQuery("#trigger" + del_id).remove();

		return false;
	});

	jQuery(document).on('click','.bf_add_input',function() {

		var action = jQuery(this);
		var args = action.attr('href').split("/");
	 	var	numItems = jQuery('#table_row_' + args[0] + '_select_options ul li').size();

	 	numItems = numItems + 1;
	 	jQuery('#table_row_' + args[0] + '_select_options ul').append(
			'<li class="field_item field_item_'+args[0]+'_'+numItems+'">' +
			'<table class="wp-list-table widefat fixed posts"><tbody><tr><td>' +
			'<input class="field-sortable" type="text" name="buddyforms_options[form_fields]['+args[0]+'][options]['+numItems+'][label]">' +
			'</td><td>' +
			'<input class="field-sortable" type="text" name="buddyforms_options[form_fields]['+args[0]+'][options]['+numItems+'][value]">' +
			'</td><td class="manage-column column-author">' +
			'<a href="#" id="'+args[0]+'_'+numItems+'" class="bf_delete_input">Delete</a>' +
			'</td></tr></li></tbody></table>');
    	return false;

	});

	jQuery(document).on('click','.bf_delete_input',function() {
		var del_id = jQuery(this).attr('id');
		if (confirm('Delete Permanently'))
			jQuery(".field_item_" + del_id).remove();
		return false;
	});

	jQuery(document).on('mousedown','.bf_list_item',function() {
		jQuery(".element_field_sortable").sortable({
			update: function(event, ui) {
				var testst = jQuery(".element_field_sortable").sortable('toArray');
				for (var key in testst){
				//	alert(key); this needs to be rethinked ;-)
				}
			}
		});
	});

	function bf_update_list_item_number() {
		jQuery(".buddyforms_forms_builder ul").each(function() {
			jQuery(this).children("li").each(function(t) {
				jQuery(this).find("td.field_order .circle").first().html(t + 1)
			})
		})
	}
	bf_update_list_item_number();

	jQuery(document).on('mousedown','.bf_list_item',function() {
		itemList = jQuery(this).closest('.sortable').sortable({
	    	update: function(event, ui) {
				bf_update_list_item_number();
		       }
	       });
	   });

	function bf_update_list_item_number_mail() {
		jQuery(".panel-mail-notifications .wp-list-table").each(function(t) {
			jQuery(this).find("td.field_order .circle").first().html(t + 1)
		})
	}
	bf_update_list_item_number_mail();

    jQuery('#mail_notification_add_new').click(function (e) {
		var error = false;
        var trigger = jQuery('.buddyforms_notification_trigger').val();

        if(trigger == 'none'){
            alert('You have to select a trigger first.');
            return false;
        }

		// traverse all the required elements looking for an empty one
		jQuery("#buddyforms_form_mail li.bf_trigger_list_item").each(function() {
			if(jQuery(this).attr('id') == 'trigger'+trigger){
				alert('Trigger already exists');
				error = true;
			}
		})

		if(error == true)
			return false;

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_new_mail_notification", "trigger": trigger},
            success: function(data){

                if(data == 0){
                    alert('trigger already exists');
                    return false;
                }

				jQuery('#mailcontainer').append(data);
            }
        });
        return false;
    });

    jQuery(".bf_check_all").click(function(e){

        if (jQuery("#buddyforms_form_roles input[type='checkbox']").prop("checked")) {
            jQuery('#buddyforms_form_roles :checkbox').prop('checked', false);
            jQuery(this).text( admin_text.check);
        } else {
            jQuery('#buddyforms_form_roles :checkbox').prop('checked', true);
            jQuery(this).text(admin_text.uncheck);
        }
		e.preventDefault();
    });

	jQuery('.buddyforms_forms_builder').on('blur', '.use_as_slug', function() {

		var field_name = jQuery(this).val();
		if( field_name === '')
			return;

		var field_id = jQuery(this).attr('data');
		if( field_id === '')
			return;

		var field_slug_val = jQuery('tr .slug'+field_id).val();


		if( field_slug_val === ''){
			jQuery('tr .slug'+field_id).val(slug(field_name, {lower: true}));
		}
		jQuery(this).unbind('blur');
	});

});

// https://github.com/dodo/node-slug
(function (root) {
// lazy require symbols table
	var _symbols, removelist;
	function symbols(code) {
		if (_symbols) return _symbols[code];
		_symbols = require('unicode/category/So');
		removelist = ['sign','cross','of','symbol','staff','hand','black','white']
			.map(function (word) {return new RegExp(word, 'gi')});
		return _symbols[code];
	}

	function slug(string, opts) {
		string = string.toString();
		if ('string' === typeof opts)
			opts = {replacement:opts};
		opts = opts || {};
		opts.mode = opts.mode || slug.defaults.mode;
		var defaults = slug.defaults.modes[opts.mode];
		var keys = ['replacement','multicharmap','charmap','remove','lower'];
		for (var key, i = 0, l = keys.length; i < l; i++) { key = keys[i];
			opts[key] = (key in opts) ? opts[key] : defaults[key];
		}
		if ('undefined' === typeof opts.symbols)
			opts.symbols = defaults.symbols;

		var lengths = [];
		for (var key in opts.multicharmap) {
			if (!opts.multicharmap.hasOwnProperty(key))
				continue;

			var len = key.length;
			if (lengths.indexOf(len) === -1)
				lengths.push(len);
		}

		var code, unicode, result = "";
		for (var char, i = 0, l = string.length; i < l; i++) { char = string[i];
			if (!lengths.some(function (len) {
					var str = string.substr(i, len);
					if (opts.multicharmap[str]) {
						i += len - 1;
						char = opts.multicharmap[str];
						return true;
					} else return false;
				})) {
				if (opts.charmap[char]) {
					char = opts.charmap[char];
					code = char.charCodeAt(0);
				} else {
					code = string.charCodeAt(i);
				}
				if (opts.symbols && (unicode = symbols(code))) {
					char = unicode.name.toLowerCase();
					for(var j = 0, rl = removelist.length; j < rl; j++) {
						char = char.replace(removelist[j], '');
					}
					char = char.replace(/^\s+|\s+$/g, '');
				}
			}
			char = char.replace(/[^\w\s\-\.\_~]/g, ''); // allowed
			if (opts.remove) char = char.replace(opts.remove, ''); // add flavour
			result += char;
		}
		result = result.replace(/^\s+|\s+$/g, ''); // trim leading/trailing spaces
		result = result.replace(/[-\s]+/g, opts.replacement); // convert spaces
		result = result.replace(opts.replacement+"$",''); // remove trailing separator
		if (opts.lower)
			result = result.toLowerCase();
		return result;
	};

	slug.defaults = {
		mode: 'pretty',
	};

	slug.multicharmap = slug.defaults.multicharmap = {
		'<3': 'love', '&&': 'and', '||': 'or', 'w/': 'with',
	};

// https://code.djangoproject.com/browser/django/trunk/django/contrib/admin/media/js/urlify.js
	slug.charmap  = slug.defaults.charmap = {
		// latin
		'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE',
		'Ç': 'C', 'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I',
		'Î': 'I', 'Ï': 'I', 'Ð': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O',
		'Õ': 'O', 'Ö': 'O', 'Ő': 'O', 'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U',
		'Ü': 'U', 'Ű': 'U', 'Ý': 'Y', 'Þ': 'TH', 'ß': 'ss', 'à':'a', 'á':'a',
		'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'æ': 'ae', 'ç': 'c', 'è': 'e',
		'é': 'e', 'ê': 'e', 'ë': 'e', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
		'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o',
		'ő': 'o', 'ø': 'o', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ű': 'u',
		'ý': 'y', 'þ': 'th', 'ÿ': 'y', 'ẞ': 'SS',
		// greek
		'α':'a', 'β':'b', 'γ':'g', 'δ':'d', 'ε':'e', 'ζ':'z', 'η':'h', 'θ':'8',
		'ι':'i', 'κ':'k', 'λ':'l', 'μ':'m', 'ν':'n', 'ξ':'3', 'ο':'o', 'π':'p',
		'ρ':'r', 'σ':'s', 'τ':'t', 'υ':'y', 'φ':'f', 'χ':'x', 'ψ':'ps', 'ω':'w',
		'ά':'a', 'έ':'e', 'ί':'i', 'ό':'o', 'ύ':'y', 'ή':'h', 'ώ':'w', 'ς':'s',
		'ϊ':'i', 'ΰ':'y', 'ϋ':'y', 'ΐ':'i',
		'Α':'A', 'Β':'B', 'Γ':'G', 'Δ':'D', 'Ε':'E', 'Ζ':'Z', 'Η':'H', 'Θ':'8',
		'Ι':'I', 'Κ':'K', 'Λ':'L', 'Μ':'M', 'Ν':'N', 'Ξ':'3', 'Ο':'O', 'Π':'P',
		'Ρ':'R', 'Σ':'S', 'Τ':'T', 'Υ':'Y', 'Φ':'F', 'Χ':'X', 'Ψ':'PS', 'Ω':'W',
		'Ά':'A', 'Έ':'E', 'Ί':'I', 'Ό':'O', 'Ύ':'Y', 'Ή':'H', 'Ώ':'W', 'Ϊ':'I',
		'Ϋ':'Y',
		// turkish
		'ş':'s', 'Ş':'S', 'ı':'i', 'İ':'I',
		'ğ':'g', 'Ğ':'G',
		// russian
		'а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ё':'yo', 'ж':'zh',
		'з':'z', 'и':'i', 'й':'j', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o',
		'п':'p', 'р':'r', 'с':'s', 'т':'t', 'у':'u', 'ф':'f', 'х':'h', 'ц':'c',
		'ч':'ch', 'ш':'sh', 'щ':'sh', 'ъ':'u', 'ы':'y', 'ь':'', 'э':'e', 'ю':'yu',
		'я':'ya',
		'А':'A', 'Б':'B', 'В':'V', 'Г':'G', 'Д':'D', 'Е':'E', 'Ё':'Yo', 'Ж':'Zh',
		'З':'Z', 'И':'I', 'Й':'J', 'К':'K', 'Л':'L', 'М':'M', 'Н':'N', 'О':'O',
		'П':'P', 'Р':'R', 'С':'S', 'Т':'T', 'У':'U', 'Ф':'F', 'Х':'H', 'Ц':'C',
		'Ч':'Ch', 'Ш':'Sh', 'Щ':'Sh', 'Ъ':'U', 'Ы':'Y', 'Ь':'', 'Э':'E', 'Ю':'Yu',
		'Я':'Ya',
		// ukranian
		'Є':'Ye', 'І':'I', 'Ї':'Yi', 'Ґ':'G', 'є':'ye', 'і':'i', 'ї':'yi', 'ґ':'g',
		// czech
		'č':'c', 'ď':'d', 'ě':'e', 'ň': 'n', 'ř':'r', 'š':'s', 'ť':'t', 'ů':'u',
		'ž':'z', 'Č':'C', 'Ď':'D', 'Ě':'E', 'Ň': 'N', 'Ř':'R', 'Š':'S', 'Ť':'T',
		'Ů':'U', 'Ž':'Z',
		// polish
		'ą':'a', 'ć':'c', 'ę':'e', 'ł':'l', 'ń':'n', 'ś':'s', 'ź':'z',
		'ż':'z', 'Ą':'A', 'Ć':'C', 'Ę':'E', 'Ł':'L', 'Ń':'N', 'Ś':'S',
		'Ź':'Z', 'Ż':'Z',
		// latvian
		'ā':'a', 'ē':'e', 'ģ':'g', 'ī':'i', 'ķ':'k', 'ļ':'l', 'ņ':'n',
		'ū':'u', 'Ā':'A', 'Ē':'E', 'Ģ':'G', 'Ī':'I',
		'Ķ':'K', 'Ļ':'L', 'Ņ':'N', 'Ū':'U',
		// lithuanian
		'ė':'e', 'į':'i', 'ų':'u', 'Ė': 'E', 'Į': 'I', 'Ų':'U',
		// romanian
		'ț':'t', 'Ț':'T', 'ţ':'t', 'Ţ':'T', 'ș':'s', 'Ș':'S', 'ă':'a', 'Ă':'A',
		// vietnamese
		'Ạ': 'A', 'Ả': 'A', 'Ầ': 'A', 'Ấ': 'A', 'Ậ': 'A', 'Ẩ': 'A', 'Ẫ': 'A',
		'Ằ': 'A', 'Ắ': 'A', 'Ặ': 'A', 'Ẳ': 'A', 'Ẵ': 'A', 'Ẹ': 'E', 'Ẻ': 'E',
		'Ẽ': 'E', 'Ề': 'E', 'Ế': 'E', 'Ệ': 'E', 'Ể': 'E', 'Ễ': 'E', 'Ị': 'I',
		'Ỉ': 'I', 'Ĩ': 'I', 'Ọ': 'O', 'Ỏ': 'O', 'Ồ': 'O', 'Ố': 'O', 'Ộ': 'O',
		'Ổ': 'O', 'Ỗ': 'O', 'Ơ': 'O', 'Ờ': 'O', 'Ớ': 'O', 'Ợ': 'O', 'Ở': 'O',
		'Ỡ': 'O', 'Ụ': 'U', 'Ủ': 'U', 'Ũ': 'U', 'Ư': 'U', 'Ừ': 'U', 'Ứ': 'U',
		'Ự': 'U', 'Ử': 'U', 'Ữ': 'U', 'Ỳ': 'Y', 'Ỵ': 'Y', 'Ỷ': 'Y', 'Ỹ': 'Y',
		'Đ': 'D', 'ạ': 'a', 'ả': 'a', 'ầ': 'a', 'ấ': 'a', 'ậ': 'a', 'ẩ': 'a',
		'ẫ': 'a', 'ằ': 'a', 'ắ': 'a', 'ặ': 'a', 'ẳ': 'a', 'ẵ': 'a', 'ẹ': 'e',
		'ẻ': 'e', 'ẽ': 'e', 'ề': 'e', 'ế': 'e', 'ệ': 'e', 'ể': 'e', 'ễ': 'e',
		'ị': 'i', 'ỉ': 'i', 'ĩ': 'i', 'ọ': 'o', 'ỏ': 'o', 'ồ': 'o', 'ố': 'o',
		'ộ': 'o', 'ổ': 'o', 'ỗ': 'o', 'ơ': 'o', 'ờ': 'o', 'ớ': 'o', 'ợ': 'o',
		'ở': 'o', 'ỡ': 'o', 'ụ': 'u', 'ủ': 'u', 'ũ': 'u', 'ư': 'u', 'ừ': 'u',
		'ứ': 'u', 'ự': 'u', 'ử': 'u', 'ữ': 'u', 'ỳ': 'y', 'ỵ': 'y', 'ỷ': 'y',
		'ỹ': 'y', 'đ': 'd',
		// currency
		'€': 'euro', '₢': 'cruzeiro', '₣': 'french franc', '£': 'pound',
		'₤': 'lira', '₥': 'mill', '₦': 'naira', '₧': 'peseta', '₨': 'rupee',
		'₩': 'won', '₪': 'new shequel', '₫': 'dong', '₭': 'kip', '₮': 'tugrik',
		'₯': 'drachma', '₰': 'penny', '₱': 'peso', '₲': 'guarani', '₳': 'austral',
		'₴': 'hryvnia', '₵': 'cedi', '¢': 'cent', '¥': 'yen', '元': 'yuan',
		'円': 'yen', '﷼': 'rial', '₠': 'ecu', '¤': 'currency', '฿': 'baht',
		"$": 'dollar', '₹': 'indian rupee',
		// symbols
		'©':'(c)', 'œ': 'oe', 'Œ': 'OE', '∑': 'sum', '®': '(r)', '†': '+',
		'“': '"', '”': '"', '‘': "'", '’': "'", '∂': 'd', 'ƒ': 'f', '™': 'tm',
		'℠': 'sm', '…': '...', '˚': 'o', 'º': 'o', 'ª': 'a', '•': '*',
		'∆': 'delta', '∞': 'infinity', '♥': 'love', '&': 'and', '|': 'or',
		'<': 'less', '>': 'greater',
	};

	slug.defaults.modes = {
		rfc3986: {
			replacement: '-',
			symbols: true,
			remove: null,
			lower: true,
			charmap: slug.defaults.charmap,
			multicharmap: slug.defaults.multicharmap,
		},
		pretty: {
			replacement: '-',
			symbols: true,
			remove: /[.]/g,
			lower: false,
			charmap: slug.defaults.charmap,
			multicharmap: slug.defaults.multicharmap,
		},
	};

// Be compatible with different module systems

	if (typeof define !== 'undefined' && define.amd) { // AMD
		// dont load symbols table in the browser
		for (var key in slug.defaults.modes) {
			if (!slug.defaults.modes.hasOwnProperty(key))
				continue;

			slug.defaults.modes[key].symbols = false;
		}
		define([], function () {return slug});
	} else if (typeof module !== 'undefined' && module.exports) { // CommonJS
		symbols(); // preload symbols table
		module.exports = slug;
	} else { // Script tag
		// dont load symbols table in the browser
		for (var key in slug.defaults.modes) {
			if (!slug.defaults.modes.hasOwnProperty(key))
				continue;

			slug.defaults.modes[key].symbols = false;
		}
		root.slug = slug;
	}

}(this));
