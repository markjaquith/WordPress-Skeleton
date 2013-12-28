jQuery(document).ready(function($) {

	// Portfolio project info
	if($('#project_info_fields .inside').length > 0){
		$('#project_info_fields .inside').sortable();
	}

	$('#pi-add-field').click( function() {
		$('#project_info_fields .inside').append('<p class="pi-field"><input type="text" name="ci_cpt_project_info_fields[]" /><input type="text" name="ci_cpt_project_info_fields[]" /> <a href="#" class="pi-remove">Remove me</a></p>');
		return false;		
	});
	
	$('#project_info_fields').on('click', '.pi-remove', function() {
		$(this).parent('p').remove();
		return false;
	});

}); 
