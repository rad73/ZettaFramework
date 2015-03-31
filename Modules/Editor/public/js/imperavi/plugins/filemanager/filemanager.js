if (!RedactorPlugins) var RedactorPlugins = {};

RedactorPlugins.filemanager = {
	init: function() {
				
		this.buttonRemove('image');
		this.buttonRemove('file');
		
        this.buttonAddBefore('video', 'image', 'Insert Image', $.proxy(function() {
        	
        	this.makeFilemanager();

        }, this));
                
    },
    makeFilemanager: function () {
    	
    	var _this = this;
    	this.selectionSave();
    	
    	$('body').append('<div id="file_manager" title="Выберите файл"/>');
		$('#file_manager')
			.dialog({
				resizable: false,
				width: 950,
				height: 500,
				modal: true,
				close: function( event, ui ) {
					$('#file_manager').remove();
				}
			})
			.elfinder({
				url : _baseUrl + '/mvc/editor/index/elfinderconnector/?csrf_hash=' + _csrf_hash,
				lang: 'ru',
				getFileCallback: function (file, fm) {
					
		        	fm.destroy();
		        	$('#file_manager').remove();
					
					_this.selectionRestore()
					
					if (-1 == file.mime.indexOf('image')) {
						_this.linkInsert('<a href="' + file.url + '">' + _this.getSelection() + '</a>', _this.getSelection(), file.url);
					}
					else {
		        		_this.execCommand('inserthtml', '<img src="' + file.url + '" alt=""/>');
					}
					
				}
			
			});
    	
    }

};