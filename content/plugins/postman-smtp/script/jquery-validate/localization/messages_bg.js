/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: BG (Bulgarian; български език)
 */
jQuery.extend( jQuery.validator.messages, {
	required: "Полето е задължително.",
	remote: "Моля, въведете правилната стойност.",
	email: "Моля, въведете валиден email.",
	url: "Моля, въведете валидно URL.",
	date: "Моля, въведете валидна дата.",
	dateISO: "Моля, въведете валидна дата (ISO).",
	number: "Моля, въведете валиден номер.",
	digits: "Моля, въведете само цифри.",
	creditcard: "Моля, въведете валиден номер на кредитна карта.",
	equalTo: "Моля, въведете същата стойност отново.",
	extension: "Моля, въведете стойност с валидно разширение.",
	maxlength: jQuery.validator.format( "Моля, въведете повече от {0} символа." ),
	minlength: jQuery.validator.format( "Моля, въведете поне {0} символа." ),
	rangelength: jQuery.validator.format( "Моля, въведете стойност с дължина между {0} и {1} символа." ),
	range: jQuery.validator.format( "Моля, въведете стойност между {0} и {1}." ),
	max: jQuery.validator.format( "Моля, въведете стойност по-малка или равна на {0}." ),
	min: jQuery.validator.format( "Моля, въведете стойност по-голяма или равна на {0}." )
} );
