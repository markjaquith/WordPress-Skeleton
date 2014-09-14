(function (gpoll, $) {

    var strings, resultsButton, backButtonMarkup;

    gpoll.init = function (){
        strings = gpoll_strings;
        resultsButton = '<a href="javascript:void(0)" class="gpoll_button">' + strings.viewResults + '</a><div class="gpoll_summary"></div>';
        backButtonMarkup = '<a href="javascript:void(0)" class="gpoll_back_button" style="display:none;">' + strings.backToThePoll + '</a>';

        $(".gform_wrapper.gpoll_enabled form").each(function(){
            var pollVars = getPollVars(this);
            if (pollVars.showResultsLink == "1" || (pollVars.override == "0" && jQuery(this).hasClass("gpoll_show_results_link")))
                $(this).find(".gform_button").parent().append(resultsButton);
            if (pollVars.cookieDuration || $(this).hasClass('gpoll_block_repeat_voters')){
                var tz = jstz.determine();
                document.cookie = "gpoll-timezone=" + tz.name();
            }
        });

        $(".gpoll_container").each(function(){
            animateBars(this);
        });

        $(document).bind('gform_confirmation_loaded', function(event, formId){
            var gf_selector = "div#gforms_confirmation_message.gform_confirmation_message_" + formId;
            var pollsContainer = $(gf_selector);
            $(gf_selector + " div.gpoll_bar > span").hide();
            animateBars(pollsContainer);
        });

        $(".gform_wrapper form.gpoll_enabled").each(function(){
            maybeGetResultsUI(this, false);
        });

        setUpResultsButtons();
    }

    function setUpBackButtons(){
        $(".gpoll_back_button").click(function(){
            var $form = getCurrentForm(jQuery(this));
            var formId = getFormId($form);
            $form.find(".gpoll_summary").fadeOut();
            $form.find("#gform_fields_" + formId).fadeIn();
            $form.find("#gform_submit_button_" + formId).fadeIn();
            $form.find(".gpoll_button").fadeIn();
            $form.find(".gpoll_summary").remove();
            $form.find(".gform_button").parent().append(resultsButton);
            setUpResultsButtons();
        });
    }

    function setUpResultsButtons(){
        $(".gpoll_button").click(function(e){
            var form = $(this).closest(".gform_wrapper form");
            maybeGetResultsUI(form, true);
        });
    }

    function getCurrentForm(element){
        var form = $(element).closest("form");
        return form;
    }

    function getFormId(form){
        var formId = $(form).attr("id").replace("gform_", "");
        return formId;
    }

    function animateBars(root){
        var $root = $(root);
        $root.find(".gpoll_bar > span").show();
        $root.find(".gpoll_bar > span").each(function() {
            var $this = $(this);
            $this.width(0)
                .animate({
                    width: $this.data("origwidth") + '%'
                }, 1500);
        });
    }

    function getQueryVariable( query, variable) {
        var vars = query.split("&"), i;
        for (i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable) {
                return unescape(pair[1]);
            }
        }
    }

    function getPollVars(form){
        var fieldValues = $(form).find("input[name='gform_field_values']").val();
        var pollVars = new Object();
        if (fieldValues != undefined) {
            var override = getQueryVariable(fieldValues,"gpoll_enabled");
            if (override) {
                pollVars.override = 1;
                pollVars.checksum = getQueryVariable(fieldValues,"gpoll_checksum");
                pollVars.style = getQueryVariable(fieldValues,"gpoll_style");
                pollVars.percentages = getQueryVariable(fieldValues,"gpoll_percentages");
                pollVars.counts = getQueryVariable(fieldValues,"gpoll_counts");
                pollVars.showResultsLink = getQueryVariable(fieldValues,"gpoll_show_results_link");
                pollVars.displayResults = getQueryVariable(fieldValues,"gpoll_display_results");
                pollVars.cookieDuration = getQueryVariable(fieldValues,"gpoll_cookie");
                pollVars.confirmation = getQueryVariable(fieldValues,"gpoll_confirmation");
            } else {
                pollVars.override = 0;
            }
        }
        return pollVars;
    }

    function cookieExists(key){
        return document.cookie.indexOf(key) !== -1;
    }

    function maybeGetResultsUI(form, previewResults){
        var container, pollVars, formId, hasVoted,formSettingBlockRepeatVoters;

        pollVars = getPollVars(form);
        formId = getFormId(form);

        if (previewResults){
            pollVars.previewResults = 1;
        } else {
            hasVoted = cookieExists("gpoll_form_" + formId);
            if (false === hasVoted || "" === pollVars.cookieDuration)
                return;
            container = jQuery(form).closest(".gform_wrapper.gpoll_enabled");
            formSettingBlockRepeatVoters = jQuery(container).hasClass('gpoll_block_repeat_voters');
            if ( pollVars.cookieDuration == undefined && false === formSettingBlockRepeatVoters)
                return;

            $(container).hide()
        }

        pollVars.action = 'gpoll_ajax';
        pollVars.formId = formId;

        $.ajax({
            url:gpollVars.ajaxurl,
            type:'POST',
            dataType: 'json',
            data: pollVars,
            success:function(result) {
                if (result === -1){
                    //permission denied
                }
                else {
                    var $form = $(form);
                    container = $form.closest(".gform_wrapper.gpoll_enabled");
                    if (previewResults){

                        $form.find(".gpoll_summary").html(result.resultsUI);
                        $form.find("#gform_fields_" + formId).hide();
                        $form.find("#gform_submit_button_" + formId).hide();
                        $form.find(".gpoll_button").remove();
                        $form.find(".gpoll_bar > span").hide();
                        $form.find(".gpoll_summary").hide().fadeIn(function (){
                            animateBars(form);

                            $form.find(".gpoll_summary").append(backButtonMarkup)
                            $form.find(".gpoll_back_button").fadeIn('slow');

                            setUpBackButtons();
                        });
                    } else if (false === result.canVote){
                        $(container).html(result.resultsUI);
                        $(container).show();
                        animateBars(container);
                    } else {
                        $(container).show();
                    }
                }
            }
        });
    }

}(window.gpoll = window.gpoll || {}, jQuery));

jQuery(document).ready(function() {

    gpoll.init();

});

/*! jsTimezoneDetect - v1.0.5 - 2013-04-01 */
(function(e){var t=function(){"use strict";var e="s",n=2011,r=function(e){var t=-e.getTimezoneOffset();return t!==null?t:0},i=function(e,t,n){var r=new Date;return e!==undefined&&r.setFullYear(e),r.setDate(n),r.setMonth(t),r},s=function(e){return r(i(e,0,2))},o=function(e){return r(i(e,5,2))},u=function(e){var t=e.getMonth()>7?o(e.getFullYear()):s(e.getFullYear()),n=r(e);return t-n!==0},a=function(){var t=s(n),r=o(n),i=t-r;return i<0?t+",1":i>0?r+",1,"+e:t+",0"},f=function(){var e=a();return new t.TimeZone(t.olson.timezones[e])},l=function(e){var t=new Date(2010,6,15,1,0,0,0),n={"America/Denver":new Date(2011,2,13,3,0,0,0),"America/Mazatlan":new Date(2011,3,3,3,0,0,0),"America/Chicago":new Date(2011,2,13,3,0,0,0),"America/Mexico_City":new Date(2011,3,3,3,0,0,0),"America/Asuncion":new Date(2012,9,7,3,0,0,0),"America/Santiago":new Date(2012,9,3,3,0,0,0),"America/Campo_Grande":new Date(2012,9,21,5,0,0,0),"America/Montevideo":new Date(2011,9,2,3,0,0,0),"America/Sao_Paulo":new Date(2011,9,16,5,0,0,0),"America/Los_Angeles":new Date(2011,2,13,8,0,0,0),"America/Santa_Isabel":new Date(2011,3,5,8,0,0,0),"America/Havana":new Date(2012,2,10,2,0,0,0),"America/New_York":new Date(2012,2,10,7,0,0,0),"Asia/Beirut":new Date(2011,2,27,1,0,0,0),"Europe/Helsinki":new Date(2011,2,27,4,0,0,0),"Europe/Istanbul":new Date(2011,2,28,5,0,0,0),"Asia/Damascus":new Date(2011,3,1,2,0,0,0),"Asia/Jerusalem":new Date(2011,3,1,6,0,0,0),"Asia/Gaza":new Date(2009,2,28,0,30,0,0),"Africa/Cairo":new Date(2009,3,25,0,30,0,0),"Pacific/Auckland":new Date(2011,8,26,7,0,0,0),"Pacific/Fiji":new Date(2010,10,29,23,0,0,0),"America/Halifax":new Date(2011,2,13,6,0,0,0),"America/Goose_Bay":new Date(2011,2,13,2,1,0,0),"America/Miquelon":new Date(2011,2,13,5,0,0,0),"America/Godthab":new Date(2011,2,27,1,0,0,0),"Europe/Moscow":t,"Asia/Yekaterinburg":t,"Asia/Omsk":t,"Asia/Krasnoyarsk":t,"Asia/Irkutsk":t,"Asia/Yakutsk":t,"Asia/Vladivostok":t,"Asia/Kamchatka":t,"Europe/Minsk":t,"Pacific/Apia":new Date(2010,10,1,1,0,0,0),"Australia/Perth":new Date(2008,10,1,1,0,0,0)};return n[e]};return{determine:f,date_is_dst:u,dst_start_for:l}}();t.TimeZone=function(e){"use strict";var n={"America/Denver":["America/Denver","America/Mazatlan"],"America/Chicago":["America/Chicago","America/Mexico_City"],"America/Santiago":["America/Santiago","America/Asuncion","America/Campo_Grande"],"America/Montevideo":["America/Montevideo","America/Sao_Paulo"],"Asia/Beirut":["Asia/Beirut","Europe/Helsinki","Europe/Istanbul","Asia/Damascus","Asia/Jerusalem","Asia/Gaza"],"Pacific/Auckland":["Pacific/Auckland","Pacific/Fiji"],"America/Los_Angeles":["America/Los_Angeles","America/Santa_Isabel"],"America/New_York":["America/Havana","America/New_York"],"America/Halifax":["America/Goose_Bay","America/Halifax"],"America/Godthab":["America/Miquelon","America/Godthab"],"Asia/Dubai":["Europe/Moscow"],"Asia/Dhaka":["Asia/Yekaterinburg"],"Asia/Jakarta":["Asia/Omsk"],"Asia/Shanghai":["Asia/Krasnoyarsk","Australia/Perth"],"Asia/Tokyo":["Asia/Irkutsk"],"Australia/Brisbane":["Asia/Yakutsk"],"Pacific/Noumea":["Asia/Vladivostok"],"Pacific/Tarawa":["Asia/Kamchatka"],"Pacific/Tongatapu":["Pacific/Apia"],"Africa/Johannesburg":["Asia/Gaza","Africa/Cairo"],"Asia/Baghdad":["Europe/Minsk"]},r=e,i=function(){var e=n[r],i=e.length,s=0,o=e[0];for(;s<i;s+=1){o=e[s];if(t.date_is_dst(t.dst_start_for(o))){r=o;return}}},s=function(){return typeof n[r]!="undefined"};return s()&&i(),{name:function(){return r}}},t.olson={},t.olson.timezones={"-720,0":"Pacific/Majuro","-660,0":"Pacific/Pago_Pago","-600,1":"America/Adak","-600,0":"Pacific/Honolulu","-570,0":"Pacific/Marquesas","-540,0":"Pacific/Gambier","-540,1":"America/Anchorage","-480,1":"America/Los_Angeles","-480,0":"Pacific/Pitcairn","-420,0":"America/Phoenix","-420,1":"America/Denver","-360,0":"America/Guatemala","-360,1":"America/Chicago","-360,1,s":"Pacific/Easter","-300,0":"America/Bogota","-300,1":"America/New_York","-270,0":"America/Caracas","-240,1":"America/Halifax","-240,0":"America/Santo_Domingo","-240,1,s":"America/Santiago","-210,1":"America/St_Johns","-180,1":"America/Godthab","-180,0":"America/Argentina/Buenos_Aires","-180,1,s":"America/Montevideo","-120,0":"America/Noronha","-120,1":"America/Noronha","-60,1":"Atlantic/Azores","-60,0":"Atlantic/Cape_Verde","0,0":"UTC","0,1":"Europe/London","60,1":"Europe/Berlin","60,0":"Africa/Lagos","60,1,s":"Africa/Windhoek","120,1":"Asia/Beirut","120,0":"Africa/Johannesburg","180,0":"Asia/Baghdad","180,1":"Europe/Moscow","210,1":"Asia/Tehran","240,0":"Asia/Dubai","240,1":"Asia/Baku","270,0":"Asia/Kabul","300,1":"Asia/Yekaterinburg","300,0":"Asia/Karachi","330,0":"Asia/Kolkata","345,0":"Asia/Kathmandu","360,0":"Asia/Dhaka","360,1":"Asia/Omsk","390,0":"Asia/Rangoon","420,1":"Asia/Krasnoyarsk","420,0":"Asia/Jakarta","480,0":"Asia/Shanghai","480,1":"Asia/Irkutsk","525,0":"Australia/Eucla","525,1,s":"Australia/Eucla","540,1":"Asia/Yakutsk","540,0":"Asia/Tokyo","570,0":"Australia/Darwin","570,1,s":"Australia/Adelaide","600,0":"Australia/Brisbane","600,1":"Asia/Vladivostok","600,1,s":"Australia/Sydney","630,1,s":"Australia/Lord_Howe","660,1":"Asia/Kamchatka","660,0":"Pacific/Noumea","690,0":"Pacific/Norfolk","720,1,s":"Pacific/Auckland","720,0":"Pacific/Tarawa","765,1,s":"Pacific/Chatham","780,0":"Pacific/Tongatapu","780,1,s":"Pacific/Apia","840,0":"Pacific/Kiritimati"},typeof exports!="undefined"?exports.jstz=t:e.jstz=t})(this);


