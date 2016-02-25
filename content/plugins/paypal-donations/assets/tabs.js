jQuery(document).ready(function($) {
	$("#paypal-donations-tabs li").each(function() {
		$(this).click(function() {
			var tabId = $(this).attr('id');
			var tabId = tabId.split('_');
			var tabContent = document.getElementById('paypal-donations-tab-content-' + tabId[1]);
			tabContent.style.display = 'block';
			$(this).addClass('nav-tab-active');
			$(this).siblings().removeClass('nav-tab-active');			
			$(tabContent).siblings().css('display','none');	
		});
	});
});
