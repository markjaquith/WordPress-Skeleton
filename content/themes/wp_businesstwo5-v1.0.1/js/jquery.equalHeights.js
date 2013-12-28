jQuery.fn.equalHeights = function(px) {
	jQuery(this).each(function(){
		var currentTallest = 0;
		jQuery(this).children().each(function(i){
			if (jQuery(this).height() > currentTallest) { currentTallest = jQuery(this).height(); }
		});
		jQuery(this).children().css({'min-height': currentTallest+1});
	});
	return this;
};
