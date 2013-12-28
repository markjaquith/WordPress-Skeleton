jQuery(document).ready(function($) {

	//Native upload window

	var target, target_id, set_interval,fileurl = "";
	window.ci_opener = { trigger : '' };

	$('.ci-upload').click(function() {
		
		var trigger = $(this).attr('id');
		trigger == "ci-upload-background" ? window.ci_opener = { trigger : 'ci-upload-background' } : window.ci_opener = { trigger : '' };  
		
		target = $(this).siblings('.uploaded');
		target_id = $(this).siblings('.uploaded-id');

		set_interval = setInterval( function() {
			jQuery('#TB_iframeContent').contents().find('.savesend .button').val('Use this file');
		}, 2000 );

		postID = 0;
		tb_show('', 'media-upload.php?post_id='+postID+'&amp;type=image&amp;TB_iframe=true');
		return false;
	});


	window.original_send_to_editor = window.send_to_editor;

	window.send_to_editor = function(html){
		if (target) {
			clearInterval(set_interval);
			if($('img',html).length > 0)
			{
				fileurl = $('img',html).attr('src');
				var imgstr = $('<div>').append($('img',html).clone()).html();
				var regex = /(?:class=".*wp-image-)(\d*)(?:")/;
				var result = imgstr.match(regex);
				if(result != null)
				{
					target_id.val(result[1]);
	
					if (window.ci_opener.trigger == 'ci-upload-background') {
						$('#default_header_bg_hidden').val(result[1]);
					}
				}
			}
			else
			{
				fileurl = $(html).attr('href');
			}

			target.val(fileurl);
			tb_remove();
		} else {
			window.original_send_to_editor(html);
		}
	};

});
