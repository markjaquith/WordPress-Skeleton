function StartChangePollType(type) {
    field = GetSelectedField();

    field["poll_field_type"] = type;

    return StartChangeInputType(type, field);
}

function SetDefaultValues_poll(field) {
    var strings = gpoll_form_editor_js_strings;
    field.poll_field_type = "radio";
    field.label = "Untitled Poll Field";
    field.inputType = "radio";
    field.inputs = null;
    field.enableChoiceValue = true;
    field.enablePrice = false;
    field.enableRandomizeChoices = false;
    if (!field.choices) {
        field.choices = new Array(new Choice(strings.firstChoice, GeneratePollChoiceValue(field)), new Choice(strings.secondChoice, GeneratePollChoiceValue(field)), new Choice(strings.thirdChoice, GeneratePollChoiceValue(field)));
    }
    return field;
}

function GeneratePollChoiceValue(field) {
    return 'gpoll' + field.id + 'xxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
        return v.toString(16);
    });

}

function gform_new_choice_poll(field, choice) {
    if (field.type == "poll")
        choice["value"] = GeneratePollChoiceValue(field);

    return choice;
}

//binding to the load field settings event to initialize
jQuery(document).bind("gform_load_field_settings", function (event, field, form) {
    jQuery('#field_randomize_choices').prop('checked', field.enableRandomizeChoices ? true : false);
    jQuery("#poll_field_type").val(field["poll_field_type"]);
    jQuery("#poll_question").val(field["label"]);

    if (field.type == 'poll') {

        jQuery('li.label_setting').hide();

        if (has_entry(field.id)) {
            jQuery("#poll_field_type").attr("disabled", true);
        } else {
            jQuery("#poll_field_type").removeAttr("disabled");
        }

    }
});




jQuery(document).ready(function () {
    if(typeof fieldSettings == 'undefined')
        return;

    fieldSettings["poll"] = ".poll_field_type_setting, .poll_question_setting, .randomize_choices_setting";
});

/* deprecated functions

 function gpollFormContainsPollField() {
 for (var i = 0; i < form.fields.length; i++) {
 if (form.fields[i].type == "poll")
 return true;
 }
 return false;
 }

jQuery(document).bind("gform_field_deleted", function (event, form, fieldId) {
    //if there are no poll fields left on the page then hide the poll forms settings
    if (gpollFormContainsPollField())
        return;
    jQuery("#gpoll-form-settings").hide();
});
jQuery(document).bind("gform_field_added", function (event, form, field) {
    if (field.type == 'poll')
        jQuery("#gpoll-form-settings").show();
});

jQuery(document).bind("gform_load_form_settings", function (event, form) {

    //defaults
    if (form.gpollDisplayResults == undefined)
        form.gpollDisplayResults = true;
    jQuery("#gpoll-form-setting-display-results").prop("checked", form.gpollDisplayResults);

    if (form.gpollShowResultsLink == undefined)
        form.gpollShowResultsLink = true;
    jQuery("#gpoll-form-setting-show-results-link").prop("checked", form.gpollShowResultsLink);

    if (form.gpollShowPercentages == undefined)
        form.gpollShowPercentages = true;
    jQuery("#gpoll-form-setting-show-percentages").prop("checked", form.gpollShowPercentages);

    if (form.gpollShowCounts == undefined)
        form.gpollShowCounts = true;
    jQuery("#gpoll-form-setting-show-counts").prop("checked", form.gpollShowCounts);

    if (form.gpollStyle == undefined)
        form.gpollStyle = "green";
    jQuery("#gpoll-form-setting-style").val(form.gpollStyle);

    if (form.gpollBlockRepeatVoters == undefined)
        form.gpollBlockRepeatVoters = false;

    if (form.gpollBlockRepeatVoters) {
        jQuery("#gpoll-form-settings input#gpoll-form-setting-block-repeat-voters-1").prop("checked", true);
        jQuery('#gpoll-form-setting-cookie-options').show();
    } else {
        jQuery("#gpoll-form-settings input#gpoll-form-setting-block-repeat-voters-0").prop("checked", true);
        jQuery('#gpoll-form-setting-cookie-options').hide();
    }

    if (form.gpollCookie == undefined)
        form.gpollCookie = "1 month";
    jQuery("#gpoll-form-setting-cookie").val(form.gpollCookie);


});

function gpollSetFormProperty(property, value) {
    form[property] = value;
}

 */
