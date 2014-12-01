//TODO Needs compressing
jQuery(document).ready(function($) {
	$('.eo-inline-help').each(function() {
		var id = $(this).attr('id').substr(15);
		$(this).click(function(e){e.preventDefault();});
		$(this).qtip({
			content: {
				text: eoHelp[id].content,
				title: {
					text: eoHelp[id].title
				}
			},
			show: {
				solo: true 
			},
			hide: 'unfocus',
			style: {
				classes: 'qtip-wiki qtip-light qtip-shadow'
			},
			position : {
				 viewport: $(window)
			}
		});
	});
});
