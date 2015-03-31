$(function () {

	$('#file_manager')
		.elfinder({
			url : _baseUrl + '/mvc/editor/index/elfinderconnector/?csrf_hash=' + _csrf_hash,
			lang: 'ru',
			resizable: false,
	});
	
});