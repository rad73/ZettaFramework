$(function () {
	
	$('#z_zfdebug_widget').one('click', function () {
		
		if ($.cookie('z_zfdebuginit_enabled')) {
			$.removeCookie('z_zfdebuginit_enabled', {path: _baseUrl + '/'});
		}
		else {
			$.cookie('z_zfdebuginit_enabled', 1, {path: _baseUrl + '/'});
		}

		document.location.reload();

	});
	
})
