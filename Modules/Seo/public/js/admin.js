$(function () {

	$('.z_form_seo input, .z_form_seo textarea').each(function () {
		
		if ($(this).val() == '') {
			$(this).val('По умолчанию');
			$(this).defaultValue();
		}
		
	});
	
	$('.z_form_seo form').submit(function () {
		
		$('input, textarea', this).each(function () {
			
			if ($(this).val() == $(this).data('initValue')) {
				$(this).val('');
			}
			
		});
		
	});

})