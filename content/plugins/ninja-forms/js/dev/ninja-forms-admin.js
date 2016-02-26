/*!
 * jQuery serializeFullArray - v0.1 - 28/06/2010
 * http://github.com/jhogendorn/jQuery-serializeFullArray/
 *
 * Copyright (c) 2010 Joshua Hogendorn
 *
 *
 * Whereas .serializeArray() serializes a form into a key:pair array, .serializeFullArray()
 * builds it into a n-tier object, respecting form input arrays.
 *
 */

(function($){
	'$:nomunge'; // Used by YUI compressor.

	$.fn.serializeFullArray = function () {
		// Grab a set of name:value pairs from the form dom.
		var set = $(this).serializeArray();
		var output = {};

		for (var field in set)
		{
			if(!set.hasOwnProperty(field)) continue;

			// Split up the field names into array tiers
			var parts = set[field].name
				.split(/\]|\[/);

			// We need to remove any blank parts returned by the regex.
			parts = $.grep(parts, function(n) { return n != ''; });

			// Start ref out at the root of the output object
			var ref = output;

			for (var segment in parts)
			{
				if(!parts.hasOwnProperty(segment)) continue;

				// set key for ease of use.
				var key = parts[segment];
				var value = {};

				// If we're at the last part, the value comes from the original array.
				if (segment == parts.length - 1)
				{
					var value = set[field].value;
				}

				// Create a throwaway object to merge into output.
				var objNew = {};
				objNew[key] = value;

				// Extend output with our temp object at the depth specified by ref.
				$.extend(true, ref, objNew);

				// Reassign ref to point to this tier, so the next loop can extend it.
				ref = ref[key];
			}
		}

		return output;
	};
})(jQuery);

jQuery.fn.tinymce_textareas = function(){
  tinyMCE.init({
    skin : "wp_theme"
    // other options here
  });
};

jQuery.fn.nextElementInDom = function(selector, options) {
	var defaults = { stopAt : 'body' };
	options = jQuery.extend(defaults, options);

	var parent = jQuery(this).parent();
	var found = parent.find(selector);

	switch(true){
		case (found.length > 0):
			return found;
		case (parent.length === 0 || parent.is(options.stopAt)):
			return jQuery([]);
		default:
			return parent.nextElementInDom(selector);
	}
};

jQuery(document).ready(function($) {
	/* * * General JS * * */

	$(".ninja-forms-admin-date").datepicker( ninja_forms_settings.datepicker_args );

	//Select All Checkbox
	$(".ninja-forms-select-all").click(function(){
		var tmp_class = this.title;
		var checked = this.checked;
		$("." + tmp_class).prop("checked", checked);
	});

	//Make the Sidebar Sortable.
	$("#side-sortables").sortable({
		placeholder: "ui-state-highlight",
		helper: 'clone',
		handle: '.hndl',
		stop: function(e,ui) {
			var order = $("#side-sortables").sortable("toArray");
			var page = $("#_page").val();
			var tab = $("#_tab").val();
			$.post( ajaxurl, { order: order, page: page, tab: tab, action:"ninja_forms_side_sortable", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){

			});
		},
	});

	//Disble the enter key so that it doesn't submit our form if pressed.
	$(document).on( 'keydown', 'form input', function( event ){
		if(event.keyCode == 13) {
			var type = $(this).attr("type");
			if( type != "textarea" ){
				return false;
			}
		}
	});

	$( document ).on( 'click', '.metabox-item-edit', function( e ) {
		e.preventDefault();
		$( this ).parent().parent().find('.inside' ).slideToggle( 'fast' );
	} );

	$(".hndle").dblclick(function(event){
		$(this).prevAll(".item-controls:first").find("a").click();
	});

	//Listen to the Field Label and change the LI title and update select lists on KeyUp
	$(document).on( 'keyup', '.ninja-forms-field-label', function(){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_label", "");

		var label = this.value;
		label = ninja_forms_escape_html( label );
		if ( $.trim( label ) == '' ){
			label = $(this).parent().parent().parent().prev().find('.item-type:first').prop("innerHTML");
		}

		$("#ninja_forms_field_" + field_id + "_title").prop("innerHTML", label);
		if ( label.length > 15 ) {
			label = label.substring(0, 15);
			label += '...';
		}
		$(".ninja-forms-field-conditional-cr-field option[value='" + field_id + "']").each(function(){
			$(this).text(field_id + ' - ' + label);
		});
		$(".ninja-forms-calc-select option[value='" + field_id +"']").each(function(){
			$(this).text(field_id + ' - ' + label);
		});
	});

	//Show / Hide Help Textarea
	$(document).on( 'change', '.ninja-forms-show-help', function( event ){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_show_help", "");
		if(this.checked){
			$("#ninja_forms_field_" + field_id + "_help_span").show();
		}else{
			$("#ninja_forms_field_" + field_id + "_help_span").hide();
		}
	});

	//Show / Hide Description Textarea
	$(document).on( 'change', '.ninja-forms-show-desc', function( event ){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_show_desc", "");
		if(this.checked){
			$("#ninja_forms_field_" + field_id + "_desc_span").show();
		}else{
			$("#ninja_forms_field_" + field_id + "_desc_span").hide();
		}
	});

	// Delete Form JS
	$(".ninja-forms-delete-form").click(function(event){
		event.preventDefault();
		var form_id = this.id.replace('ninja_forms_delete_form_', '');
		var answer = confirm('Really delete this form? (Irreversible)');
		if(answer){
			$.post(ajaxurl, { form_id: form_id, action:"ninja_forms_delete_form", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){
				$("#ninja_forms_form_" + form_id + "_tr").css("background-color", "#FF0000").fadeOut('slow', function(){
					$(this).remove();
				});
			});
		}
	});

	//Delete individual submissions
	$(".ninja-forms-delete-sub").click(function(event){
		event.preventDefault();
		var sub_id = this.id.replace("ninja_forms_sub_", "");
		var answer = confirm("Permenantly delete this item?");
		if(answer){
			$.post(ajaxurl, { sub_id: sub_id, action:"ninja_forms_delete_sub", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){
				$("#ninja_forms_sub_" + sub_id + "_tr").css("background-color", "#FF0000").fadeOut('slow', function(){
					$(this).remove();
				});
			});
		}
	});

	/* * * End General JS * * */

	/* * * Field Specific JS  * * * /

	/* Textbox Field JS */

	// Default Value
	$(document).on( 'change', '.ninja-forms-_text-default-value', function(){
		var id = this.id.replace('default_value_', '');
		if( this.value == '_custom' || this.value == 'querystring' ){
			$("#ninja_forms_field_" + id + "_default_value").val('');
			$("#default_value_label_" + id).show();
			$("#ninja_forms_field_" + id + "_default_value").focus();
		}else{
			$( '.querystring-error' ).hide();
			$( '#ninja_forms_field_' + id + '_default_value' ).removeClass( 'error' );
			$( '#default_value_label_' + id ).hide();
			$( '#ninja_forms_field_' + id + '_default_value' ).val(this.value);
		}

		if ( 'querystring' != this.value ) {
			$( '.querystring-error' ).hide();
			$( '#ninja_forms_field_' + id + '_default_value' ).removeClass( 'error' );
			$( '.nf-save-admin-fields' ).attr( 'disabled', false );
		}

		if(this.value != '' && this.value != 'today' ){
			$("#ninja_forms_field_" + id + "_datepicker").prop('checked', false);
		}
	});

	// Throw an error if we set a querystring that is reserved
	$( document ).on( 'keyup', '.nf-default-value-text', function() {
		var field_id = $( this ).data( 'field-id' );
		if ( -1 != wp_reserved_terms.indexOf( this.value ) && 'querystring' == $( '#default_value_' + field_id ).val() ) {
			$( '.querystring-error' ).show();
			$( this ).addClass( 'error' );
			$( '.nf-save-admin-fields' ).attr( 'disabled', true );
		} else {
			$( '.querystring-error' ).hide();
			$( this ).removeClass( 'error' );
			$( '.nf-save-admin-fields' ).attr( 'disabled', false );
		}
	} );

	// Input Mask
	$(document).on( 'change', '.ninja-forms-_text-mask', function(){
		var id = this.id.replace('mask_', '');
		if(this.value == '_custom'){
			$("#ninja_forms_field_" + id + "_mask").val('');
			$("#mask_label_" + id).show();
			$("#ninja_forms_field_" + id + "_mask").focus();
		}else{
			$("#mask_label_" + id).hide();
			$("#ninja_forms_field_" + id + "_mask").val(this.value);
		}

		if(this.value != ''){
			$("#ninja_forms_field_" + id + "_datepicker").prop('checked', false);
		}
	});

	//Input Mask Help
	$(document).on( 'click', '.ninja-forms-mask-help', function(event){
		event.preventDefault();
		if( !$("#tab-panel-mask_help").is(":visible") ){
			$("#tab-link-mask_help").find("a").click();
			$("#contextual-help-link").click().focus();
		}
	});

	// Datepicker
	$(document).on( 'change', '.ninja-forms-_text-datepicker', function(){
		var id = this.id.replace("ninja_forms_field_", "");
		id = id.replace("_datepicker", "");
		if(this.checked == true){
			//$("#ninja_forms_field_" + id + "_default_value").val("");
			if ( $("#ninja_forms_field_" + id + "_mask").val() != 'today' ) {
				$("#ninja_forms_field_" + id + "_mask").val("");
			}
			$("#default_value_" + id).val("");
			$("#default_value_label_" + id).hide();
			$("#mask_" + id).val("");
			$("#mask_label_" + id).hide();
		}
	});

	/* List Field JS */

	//Collapse List Options.
	$(document).on( 'click', '.ninja-forms-field-collapse-options', function(e){
		e.preventDefault();
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_collapse_options", "");
		$("#ninja_forms_field_" + field_id + "_list_span").slideToggle(function(){
			/*
			if($("#ninja_forms_field_" + field_id + "_list_span").css("display") != 'none'){
				var label = "Collapse Options";
			}else{
				var label = "Expand Options";
			}
			$("#ninja_forms_field_" + field_id + "_collapse_options").prop("innerHTML", label);
			*/
		});
	});

	//Listen to the "List Type" Select box and show the multi-size input box if "Multi-Select" is selected.
	$(document).on( 'change', '.ninja-forms-_list-list_type', function(){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_list_type", "");
		if(this.value == 'multi'){
			$("#ninja_forms_field_" + field_id+ "_multi_size_p").show();
		}else{
			$("#ninja_forms_field_" + field_id+ "_multi_size_p").hide();
		}
		if(this.value == 'radio' || this.value == 'checkbox'){
			$("#ninja_forms_field_" + field_id + "_label_pos").val('left');
			$("#ninja_forms_field_" + field_id + "_label_pos option[value='inside']").attr('disabled', true);
		}else{
			$("#ninja_forms_field_" + field_id + "_label_pos option[value='inside']").attr('disabled', false);
		}
	});

	//Make List Options sortable

	$(".ninja-forms-field-list-options").sortable({
		helper: 'clone',
		handle: '.ninja-forms-drag',
		items: 'div',
		placeholder: "ui-state-highlight",
	});

	// Listen to the Show list values checkboxes and show or hide those if necessary.
	$(document).on( 'change', '.ninja-forms-field-list-show-value', function(e){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_list_show_value", "");
		if(this.checked){
			$(".ninja-forms-field-" + field_id + "-list-option-value").show();
		}else{
			$(".ninja-forms-field-" + field_id + "-list-option-value").hide();
		}
	});

	//Add New List Option
	$(document).on( 'click', '.ninja-forms-field-add-list-option', function(event){
		event.preventDefault();
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_list_add_option", "");
		var x = $(".ninja-forms-field-" + field_id + "-list-option").length;
		var hidden_value = $("#ninja_forms_field_" + field_id + "_list_show_value").prop("checked");

		if(hidden_value){
			hidden_value = 1;
		}else{
			hidden_value = 0;
		}

		$.post(ajaxurl, { field_id: field_id, x: x, hidden_value: hidden_value, action:"ninja_forms_add_list_option", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){
			$("#ninja_forms_field_" + field_id + "_list_options").append(response);
			$("#ninja_forms_field_" + field_id + "_list_option_" + x).fadeIn();
			$(".ninja-forms-field-conditional-value-list").each(function(){
				$(this).append("<option value='' title='" + x + "'></option>");
			});
			$("[name='ninja_forms_field_" + field_id + "\\[list\\]\\[options\\]\\[" + x + "\\]\\[label\\]']").focus();
		});
	});

	//Remove List Option
	$(document).on( 'click', '.nf-remove-list-option', function(event){
		event.preventDefault();
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_list_remove_option", "");
		var x = $(this).parent().prop("id");
		x = x.replace("ninja_forms_field_" + field_id + "_list_option_", "");

		$(this).parent().parent().parent().parent().parent().fadeOut(300, function(){
			$(this).remove();
		});

		$(".ninja-forms-field-conditional-value-list").each(function(){
			$(this).children('option').each(function(){
				if(this.title == x){
					$(this).remove();
				}
			});
		});
	});

	//Listen to the option labels and values; if the enter key is pressed, add a new option.
	$(document).on( 'keydown', '.ninja-forms-field-list-option-label', function(event){
		if( event.keyCode == 13 ){
			var add_id = this.id.replace("option_label", "add_option");
			$("#" + add_id).click();
			return false;
		}
	});
	$(document).on( 'keydown', '.ninja-forms-field-list-option-value', function(event){
		if( event.keyCode == 13 ){
			var add_id = this.id.replace("option_value", "add_option");
			$("#" + add_id).click();
			return false;
		}
	});
	$(document).on( 'keydown', '.ninja-forms-field-list-option-calc', function(event){
		if( event.keyCode == 13 ){
			var add_id = this.id.replace("option_calc", "add_option");
			$("#" + add_id).click();
			return false;
		}
	});

	//Listen to List Option Labels and Values and change existing criteron option selects
	$(document).on( 'keyup', '.ninja-forms-field-list-option-label', function(){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_list_option_label", "");
		var label = this.value;
		var x = $(this).parent().prop("id").replace("ninja_forms_field_" + field_id + "_list_option_", "");
		var list_show_value = $("#ninja_forms_field_" + field_id + "_list_show_value").prop("checked");

		$(".ninja-forms-field-conditional-cr-field").each(function(){
			if(this.value == field_id){
				$(this).nextElementInDom('.ninja-forms-field-conditional-cr-value-list').each(function(){
					$(this).children('option').each(function(){
						if(this.title == x){
							this.text = label;
							if(!list_show_value){
								this.value = label;
							}
						}
					});
				});
			}
		});

		$(".ninja-forms-field-" + field_id + "-conditional-value").children('option').each(function(){
			if(this.title == x){
				this.text = label;
				if(!list_show_value){
					this.value = label;
				}
			}
		});
	});

	$(document).on( 'keyup', '.ninja-forms-field-list-option-value', function(){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_list_option_value", "");
		var value = this.value;
		var x = $(this).parent().parent().prop("id").replace("ninja_forms_field_" + field_id + "_list_option_", "");

		$(".ninja-forms-field-conditional-cr-field").each(function(){
			if(this.value == field_id){
				$(this).nextElementInDom('.ninja-forms-field-conditional-cr-value-list').each(function(){
					$(this).children('option').each(function(){
						if(this.title == x){
							this.value = value;
						}
					});
				});
			}
		});

		$(".ninja-forms-field-" + field_id + "-conditional-value").children('option').each(function(){
			if(this.title == x){
				this.value = value;
			}
		});

	});

	$(document).on( 'change', '.ninja-forms-hidden-default-value', function(){
		var field_id = $(this).attr("rel");
		if( this.value == 'custom' ){
			$("#ninja_forms_field_" + field_id + "_default_value").val("");
			$("#default_value_label_" + field_id).show();
			$("#ninja_forms_field_" + field_id + "_default_value").focus();
		}else{
			$("#ninja_forms_field_" + field_id + "_default_value").val(this.value);
			$("#default_value_label_" + field_id).hide();
		}
	});

	// Close the import popup when "cancel" is clicked.

	$(".cancel-list-import").click(function(e){
		e.preventDefault();
		tb_remove();
	});

	// Handle the importing of textarea data when the user clicks: "done"

	$(document).on( 'click', '.save-list-import', function(e){
		e.preventDefault();
		var options = $(this).parent().find("textarea").val();
		var field_id = $(this).attr("rel");
		$.post(ajaxurl, { options: options, field_id: field_id, action:"ninja_forms_import_list_options", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce}, function(response){
			$("#ninja_forms_field_" + field_id + "_list_options").append( response );
			tb_remove();
		});
	});

	// Fetch the HTML for exluding terms when the user decides to attach this list element to a term.

	$(document).on( 'change', '.ninja-forms-list-populate-term', function(e, data ){
		var field_id = $(this).attr("rel");

		if ( this.value != '' ) {
			$.post(ajaxurl, { field_id: field_id, tax_name: this.value, from_ajax: 1, action:"ninja_forms_list_terms_checkboxes", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce}, function(response){
				$("#ninja_forms_field_" + field_id + "_exclude_terms").html(response);
				$("#ninja_forms_field_" + field_id + "_exclude_terms").show();
			});
			
		} else {
			$("#ninja_forms_field_" + field_id + "_exclude_terms").hide();
		}
	});

	/* Password Field JS */

	$(document).on( 'change', '.ninja-forms-_profile_pass-reg_password', function(){
		if( this.checked ){
			$(".reg-password").parent().parent().show();
		}else{
			$(".reg-password").parent().parent().hide();
		}
	});

	/* Calculation Field JS */

	$(document).on( 'click', '.ninja-forms-field-add-calc', function(e){
		e.preventDefault();
		var field_id = $(this).attr("rel");
		var spinner = $(this).next(".spinner");
		$(spinner).show();
		var x = $("#ninja_forms_field_" + field_id + "_calc").find(".ninja-forms-calc-row:last").attr("rel");
		if ( isNaN( x ) ) {
			x = 0;
		} else {
			x = parseInt(x);
			x = x + 1;
		}

		$.post(ajaxurl, { field_id: field_id, x: x, action:"ninja_forms_add_calc_row", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){
			$("#ninja_forms_field_" + field_id + "_calc").append(response);
			$(spinner).hide();
		});
	});

	$(document).on( 'click', '.ninja-forms-field-remove-calc', function(e){
		e.preventDefault();
		var field_id = $(this).attr("rel");
		var x = $(this).attr("name");

		$("#ninja_forms_field_" + field_id + "_calc_row_" + x).hide( function(){
			$("#ninja_forms_field_" + field_id + "_calc_row_" + x).remove();
		});
	});

	// Listen to the calculation field "name" element and update the calculation select lists with the new values.
	$(document).on( 'keyup', '.ninja-forms-calc-name', function(){
		var field_id = $(this).prop("id");
		field_id = field_id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_calc_name", "");
		var label = this.value;
		label = ninja_forms_escape_html( label );
		if ( $.trim( label ) == '' ){
			label = 'calc_name';
		}

		// Set the LI label to this new text.
		$(this).parent().parent().parent().prev().find('.ninja-forms-field-title').html(label);
	});

	// Register a function to be called whenever a new field is added to the form.
	$(document).bind('addField', function(event, response){
		// Add this new calc field to all of our calculation selects
		$(".ninja-forms-calc-select").each(function(){
			$(this).append('<option value="' + response.new_id + '">' + response.new_id + ' - ' + response.new_type + '</option>');
		});
	});

	// Register a function to be called whenever a field is removed from the form.
	$(document).bind('removeField', function(event, field_id){
		$(".ninja-forms-calc-select option[value='" + field_id + "']").remove();
	});

	// Listen to the calculation display type select and show or hide the appropriate options.
	$(document).on( 'change', '.ninja-forms-calc-display', function(e){
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_calc_display_type", "");
		// Show the extra settings if the "none" option isn't selected.
		if(this.value == 'html'){
			$("#ninja_forms_field_" + field_id + "_calc_text_display").hide();
			$("#ninja_forms_field_" + field_id + "_calc_html_display").show();
			$("#ninja_forms_field_" + field_id + "_calc_extra_display").show();
			$("#ninja_forms_field_" + field_id + "_label").val('');
		}else if(this.value == 'text'){
			$("#ninja_forms_field_" + field_id + "_calc_text_display").show();
			$("#ninja_forms_field_" + field_id + "_calc_html_display").hide();
			$("#ninja_forms_field_" + field_id + "_calc_extra_display").show();
		}else{
			$("#ninja_forms_field_" + field_id + "_calc_text_display").hide();
			$("#ninja_forms_field_" + field_id + "_calc_html_display").hide();
			$("#ninja_forms_field_" + field_id + "_calc_extra_display").hide();
			$("#ninja_forms_field_" + field_id + "_label").val('');
		}
	});

	// Listen to the calculation advanced equations checkbox and show the advanced calculations box if it is checked.
	$(document).on( 'change', '.ninja-forms-calc-method', function(e){
		if(this.value == 'auto'){
			// Hide both advanced options
			$(this).parent().parent().parent().find(".ninja-forms-calculations").hide();
			$(this).parent().parent().parent().find(".ninja-forms-eq").hide();
		}else if(this.value == 'fields'){
			$(this).parent().parent().parent().find(".ninja-forms-calculations").show();
			$(this).parent().parent().parent().find(".ninja-forms-eq").hide();
		}else{
			$(this).parent().parent().parent().find(".ninja-forms-calculations").hide();
			$(this).parent().parent().parent().find(".ninja-forms-eq").show();
		}
	});

	/* User Info Fields JS */

	// Listen to the user info field group name select list and show the custom name box if custom is selected.
	$(document).on( 'change', '.user-info-group-name', function(e){
		if ( this.value == 'custom' ) {
			$(this).parent().parent().next().find('.user-info-custom-group').show();
			$(this).parent().parent().next().find('label').show();
		} else {
			$(this).parent().parent().next().find('label').hide();
			$(this).parent().parent().next().find('.user-info-custom-group').hide();
		}
	});

	/* * * End Field Specific JS * * */

	/* * * Favorite Fields JS * * */

	//Add Field to the User's Favorites List
	$(document).on( 'click', '.ninja-forms-field-add-fav', function(event){
		event.preventDefault();
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_fav", "");
		var field_data = new Object();
		var this_id = this.id;
		$("[name*='ninja_forms_field_" + field_id + "']").each(function(){
			tmp = this.name.replace("ninja_forms_field_" + field_id + "[", "");
			tmp = tmp.replace("]", "");
			if(this.type == 'checkbox'){
				if(this.checked){
					field_data['"' + tmp + '"']= this.value;
				}
			}else{
				field_data['"' + tmp + '"']= this.value;
			}
		})

		var fav_name = prompt( ninja_forms_settings.add_fav_prompt, '' );
		if(fav_name.length >= 1){
			$.post(ajaxurl, { fav_name: fav_name, field_data: field_data, field_id: field_id, action:"ninja_forms_add_fav", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){
				//document.write(response);
				$("#ninja_forms_field_" + field_id + "_fav").removeClass("ninja-forms-field-add-fav");
				$("#ninja_forms_field_" + field_id + "_fav").addClass("ninja-forms-field-remove-fav");
				$("#ninja_forms_sidebar_fav_fields").append(response.link_html);
				$("#ninja_forms_field_" + field_id + "_title").nextElementInDom('.item-type-name:first').prop("innerHTML", response.fav_name);
				$("#ninja_forms_field_" + field_id + "_fav_id").val(response.fav_id);

			});
		}else{
			var answer = confirm( ninja_forms_settings.add_fav_error );
			if(answer){
				$("#" + this_id).click();
			}
		}
	});

	//Remove a field from the user's favorites list
	$(document).on( 'click', '.ninja-forms-field-remove-fav', function(event){
		event.preventDefault();
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_fav", "");
		$.post(ajaxurl, { field_id: field_id, action:"ninja_forms_remove_fav", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){
			$("#ninja_forms_insert_fav_field_" + response.fav_id + "_p").remove();
			$(".ninja-forms-field-fav-id").each(function(){
				if(this.value == response.fav_id){
					var remove_id = this.id.replace("ninja_forms_field_", "");
					remove_id = remove_id.replace("_fav_id", "");
					$("#ninja_forms_field_" + remove_id + "_fav").removeClass("ninja-forms-field-remove-fav");
					$("#ninja_forms_field_" + remove_id + "_fav").addClass("ninja-forms-field-add-fav");
					$("#ninja_forms_field_" + remove_id + "_title").nextElementInDom('.item-type-name:first').prop("innerHTML", response.type_name);
				}
			});
		});
	});

	/* * * End Favorite Fields JS * * */

	/* * * Defined Fields JS * * */

	//Add Field to the Defined Fields List
	$(document).on( 'click', '.ninja-forms-field-add-def', function(event){
		event.preventDefault();
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_def", "");
		var field_data = new Object();
		var this_id = this.id;
		$("[name*='ninja_forms_field_" + field_id + "']").each(function(){
			tmp = this.name.replace("ninja_forms_field_" + field_id + "[", "");
			tmp = tmp.replace("]", "");
			if(this.type == 'checkbox'){
				if(this.checked){
					field_data['"' + tmp + '"']= this.value;
				}
			}else{
				field_data['"' + tmp + '"']= this.value;
			}
		})

		var def_name = prompt("What would you like to name this Defined Field?", "");
		if(def_name.length >= 1){
			$.post(ajaxurl, { def_name: def_name, field_data: field_data, field_id: field_id, action:"ninja_forms_add_def", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function(response){
				$("#ninja_forms_sidebar_def_fields").append(response.link_html);
				$("#ninja_forms_field_" + field_id + "_title").nextElementInDom('.item-type:first').prop("innerHTML", response.def_name);
				$("#ninja_forms_field_" + field_id + "_def_id").val(response.def_id);
			});
		}else{
			var answer = confirm('You must supply a name for this Defined Field.');
			if(answer){
				$("#" + this_id).click();
			}
		}
	});

	//Remove a field from the defined fields list
	$(document).on( 'click', '.ninja-forms-field-remove-def', function(event){
		event.preventDefault();
		var field_id = this.id.replace("ninja_forms_field_", "");
		field_id = field_id.replace("_def", "");
		$.post(ajaxurl, { field_id: field_id, action:"ninja_forms_remove_def", nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce}, function(response){
			$("#ninja_forms_insert_def_field_" + response.def_id + "_p").remove();
		});
	});

	/* * * End Defined Fields JS * * */

	$( document ).on( 'click', '#nf_deactivate_all_licenses', function( e ) {
		var answer = confirm( ninja_forms_settings.deactivate_all_licenses_confirm );
		if ( ! answer ) {
			return false;
		}
	} );

	$( '.nf-delete-on-uninstall-prompt' ).nfAdminModal( { title: nf_nuke_title, buttons: '.nf-delete-on-uninstall-prompt-buttons' } );

	$( document ).on( 'click', '#delete_on_uninstall', function( e ) {
		if ( this.checked ) {
			this.checked = false;
			$( '.nf-delete-on-uninstall-prompt' ).nfAdminModal( 'open' );
		}
	} );

	$( document ).on( 'click', '.nf-delete-on-uninstall-yes', function( e ) {
		e.preventDefault();
		$( "#delete_on_uninstall" ).attr( 'checked', true );
		$( '.nf-delete-on-uninstall-prompt' ).nfAdminModal( 'close' );
	} );

	// JS for resetting form conversion
	$( '#nf-conversion-reset' ).nfAdminModal( { title: nf_conversion_title, buttons: '#nf-conversion-reset-buttons' } );

	$( document ).on( 'click', '.nf-reset-form-conversion', function( e ) {
		e.preventDefault();
		$( '#nf-conversion-reset' ).nfAdminModal( 'open' );
	} );

}); //Document.ready();

function ninja_forms_escape_html(html) {
	var escape = document.createElement('textarea');
    escape.innerHTML = html;
    return escape.innerHTML;
}
