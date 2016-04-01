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
            _this.selection.get();

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
                    allowShortcuts: false,
                    useBrowserHistory: false,
                    getFileCallback: function (file, fm) {

                        $('#file_manager').remove();

                        var _html = (-1 == file.mime.indexOf('image'))
                            ? '<a href="' + file.url + '">' + file.url  + '</a>'
                            : '<img src="' + file.url + '" alt=""/>';

                        (typeof(_this.opts.replaceFull) != 'undefined' && _this.opts.replaceFull == true)
                            ? _this.insert.set(_html)
                            : _this.insert.html(_html);

                        _this.code.sync();

                        setTimeout(function () {
                            _this.core.setCallback('fileSelected', file.url);
                        }, 10);

                    }

                });

        }
    }
};
})(jQuery);