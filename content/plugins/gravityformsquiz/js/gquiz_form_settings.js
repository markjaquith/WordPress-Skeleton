(function (gquiz, $) {
    var grades = {};

    gquiz.init = function (){

        var gradesJSON = $("#grades").val();
        grades = $.parseJSON(gradesJSON);

        $(".gquiz-grading").click(function(){
            toggleGradingOptions(this.value);
        });

        var grading = $("input[name='_gaddon_setting_grading']:checked").val();
        toggleGradingOptions(grading);

        $("#passfaildisplayconfirmation, #letterdisplayconfirmation").click(function(){
            $(this).parent().siblings(".gquiz-quiz-confirmation").toggle();
        });

        var passfailDisplayConfirmation, letterDisplayConfirmation;
        passfailDisplayConfirmation = $("#passfaildisplayconfirmation").prop("checked");
        letterDisplayConfirmation = $("#letterdisplayconfirmation").prop("checked");

        if(passfaildisplayconfirmation)
            $("#gquiz-grading-pass-fail-container .gquiz-quiz-confirmation").show();
        else
            $("#gquiz-grading-pass-fail-container .gquiz-quiz-confirmation").hide();

        if(letterDisplayConfirmation)
            $("#gquiz-grading-letter-container .gquiz-quiz-confirmation").show();
        else
            $("#gquiz-grading-letter-container .gquiz-quiz-confirmation").hide();

        toggleGradingOptions();

        $('#gquiz-grades').html(getGrades());

        $(document).on("blur", 'input.gquiz-grade-value', (function () {
            var $this = $(this);
            var percent = $this.val();
            if (percent < 0 || isNaN(percent)) {
                $this.val(0);
            } else if (percent > 100) {
                $this.val(100);
            }
        }));

        $(document).on("keypress", 'input.gquiz-grade-value', (function (event) {
            if (event.which == 27) {
                this.blur();
                return false;
            }
            if (event.which === 0 || event.which === 8)
                return true;
            if (event.which < 48 || event.which > 57) {
                event.preventDefault();
            }

        }));



        //enble sorting on the grades table
        $('#gquiz-grades').sortable({
            axis  : 'y',
            handle: '.gquiz-grade-handle',
            update: function (event, ui) {
                var fromIndex = ui.item.data("index");
                var toIndex = ui.item.index();
                moveGrade(fromIndex, toIndex);
            }
        });


        $("#gform-settings").submit(function () {
            updateGradesObject();
            $("#grades").val($.toJSON(grades));
        })
    }

    function toggleGradingOptions(gradeOption) {

        switch (gradeOption) {
            case "none" :
                $('#gquiz-grading-pass-fail-container').fadeOut('fast');
                $('#gquiz-grading-letter-container').fadeOut('fast');
                break;
            case "passfail" :
                $('#gquiz-grading-letter-container').fadeOut('fast');
                $('#gquiz-grading-pass-fail-container').fadeIn('fast');
                break;
            case "letter" :
                $('#gquiz-grading-pass-fail-container').fadeOut('fast');
                $('#gquiz-grading-letter-container').fadeIn('fast');
                break;
        }
    }


    function Grade(text, value) {
        this.text = text;
        this.value = value;
    }

    gquiz.insertGrade = function(index) {
        updateGradesObject();
        var gradeBelowVal;
        var gradeAbove = grades[index - 1];
        var gradeBelow = grades[index];
        if (typeof gradeBelow == 'undefined')
            gradeBelowVal = 0;
        else
            gradeBelowVal = gradeBelow.value;
        var newValue = parseInt(gradeBelowVal) + parseInt(( gradeAbove.value - gradeBelowVal ) / 2);
        var g = new Grade("", newValue);
        grades.splice(index, 0, g);
        $('div#gquiz-settings-grades-container ul#gquiz-grades').html(getGrades());
    }

    gquiz.deleteGrade = function (index) {
        updateGradesObject();
        grades.splice(index, 1);
        $('div#gquiz-settings-grades-container ul#gquiz-grades').html(getGrades());
    }

    function moveGrade(fromIndex, toIndex) {
        updateGradesObject();
        var grade = grades[fromIndex];

        //deleting from old position
        grades.splice(fromIndex, 1);

        //inserting into new position
        grades.splice(toIndex, 0, grade);

        $('div#gquiz-settings-grades-container ul#gquiz-grades').html(getGrades());
    }

    function getGrades() {

        var imagesUrl = gquizVars.imagesUrl;
        var str = "";
        for (var i = 0; i < grades.length; i++) {

            str += "<li data-index='" + i + "'>";
            str += "<i class='fa fa-sort field-choice-handle gquiz-grade-handle' title='" + gquiz_strings.dragToReOrder + "'></i>";
            //str += "<img src='" + imagesUrl + "/arrow-handle.png' class='gquiz-grade-handle' alt='" + gquiz_strings.dragToReOrder + "' /> ";
            str += "<input type='text' id='gquiz-grade-text-" + i + "' value=\"" + grades[i].text.replace(/"/g, "&quot;") + "\"  class='gquiz-grade-input gquiz-grade-text' />";
            str += " <span class='gquiz-greater-than-or-equal'>&gt;=</span> ";
            str += "<input type='text' id='gquiz-grade-value-" + i + "' value=\"" + grades[i].value + "\" class='gquiz-grade-input gquiz-grade-value' >";
            str += " <span class='gquiz-percentage'>&#37;</span> ";
            str += "<img src='" + imagesUrl + "/add.png' class='gquiz-add-grade' title='" + gquiz_strings.addAnotherGrade + "' alt='" + gquiz_strings.addAnotherGrade + "' style='cursor:pointer; margin:0 3px;' onclick=\"gquiz.insertGrade(" + (i + 1) + ");\" />";

            if (grades.length > 1)
                str += "<img src='" + imagesUrl + "/remove.png' title='" + gquiz_strings.removeThisGrade + "' alt='" + gquiz_strings.removeThisGrade + "' class='gquiz-delete-grade' style='cursor:pointer;' onclick=\"gquiz.deleteGrade(" + i + ");\" />";

            str += "</li>";

        }

        return str;
    }

    function updateGradesObject() {

        $('ul#gquiz-grades li').each(function (index) {
            var $this = $(this);
            var gquizText = $this.children('input.gquiz-grade-text').val();
            var gquizValue = $this.children('input.gquiz-grade-value').val();
            var i = $this.data("index");
            var g = new Grade(gquizText, parseInt(gquizValue));
            grades[parseInt(i)] = g;
        });

    }


    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };

}(window.gquiz = window.gquiz || {}, jQuery));

jQuery(document).ready(function () {

    gquiz.init();
});

//------------------ Grades -----------------



jQuery(document).ready(function () {
    if (typeof form == 'undefined')
        return;

    //defaults added in php
    /*
    if (typeof form.gquizGrading == 'undefined')
        form.gquizGrading = 'none';
    if (typeof form.gquizConfirmationFail == 'undefined')
        form.gquizConfirmationFail = gquiz_strings.gquizConfirmationFail;
    if (typeof form.gquizConfirmationPass == 'undefined')
        form.gquizConfirmationPass = gquiz_strings.gquizConfirmationPass;
    if (typeof form.gquizConfirmationLetter == 'undefined')
        form.gquizConfirmationLetter = gquiz_strings.gquizConfirmationLetter;
    if (typeof form.gquizConfirmationPassAutoformatDisabled == 'undefined')
        form.gquizConfirmationPassAutoformatDisabled = false;
    if (typeof form.gquizConfirmationFailAutoformatDisabled == 'undefined')
        form.gquizConfirmationFailAutoformatDisabled = false;
    if (typeof form.gquizConfirmationLetterAutoformatDisabled == 'undefined')
        form.gquizConfirmationLetterAutoformatDisabled = false;

    if (typeof form.gquizGrades == 'undefined' || form.gquizGrades.length == 0)
        form.gquizGrades = new Array(
            new gquiz_Grade(gquiz_strings.gradeA, 90),
            new gquiz_Grade(gquiz_strings.gradeB, 80),
            new gquiz_Grade(gquiz_strings.gradeC, 70),
            new gquiz_Grade(gquiz_strings.gradeD, 60),
            new gquiz_Grade(gquiz_strings.gradeE, 0)
        );


    if (typeof form.gquizPassMark == 'undefined')
        form.gquizPassMark = "50";

    if (typeof form.gquizShuffleFields == 'undefined')
        form.gquizShuffleFields = false;
    if (typeof form.gquizInstantFeedback == 'undefined')
        form.gquizInstantFeedback = false;

    if (typeof form.gquizConfirmationTypePassFail == 'undefined')
        form.gquizConfirmationTypePassFail = "quiz";

    if (typeof form.gquizConfirmationTypeLetter == 'undefined')
        form.gquizConfirmationTypeLetter = "quiz";


    if(typeof form.gquizDisplayConfirmationPassFail == 'undefined')
        form.gquizDisplayConfirmationPassFail = true;
    if(typeof form.gquizDisplayConfirmationLetter== 'undefined')
        form.gquizDisplayConfirmationLetter = true;
        */



});

