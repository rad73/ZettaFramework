(function($)
{
    $.Redactor.prototype.filemanager = function()
    {
    return {
        init: function () {

            if (this.button.get('image').length) this.button.remove('image');
            if (this.button.get('file').length) this.button.remove('file');

            var button = this.button.addBefore ('link', 'image', this.lang.get('image'));
            this.button.addCallback(button, $.proxy(function () {
                this.filemanager.makeFilemanager();
            }, this));
        },
        makeFilemanager: function () {
            var _this = this;
            this.selection.save();

            $('body').append('<div id="file_manager" title="Выберите файл"/>');
            $('#file_manager')
                .dialog({
                    resizable: false,
                    width: 950,
                    height: 500,
                    modal: true,
                    close: function (event, ui) {
                        $('#file_manager').remove();
                    }
                })
                .elfinder({
                    url: _baseUrl + '/mvc/editor/index/elfinderconnector/?csrf_hash=' + _csrf_hash,
                    lang: 'ru',
                    getFileCallback: function (file, fm) {

                        $('#file_manager').remove();

                        _this.selection.restore();

                        if (-1 == file.mime.indexOf('image')) {
                            _this.insert.set('<a href="' + file.url + '">' + file.url  + '</a>');
                        }
                        else {
                            _this.insert.set('<img src="' + file.url + '" alt=""/>');
                        }

                        setTimeout(function () {
                            _this.core.setCallback('fileSelected', file.url);
                        }, 10);

                    }

                });

        }
    }
};
})(jQuery);