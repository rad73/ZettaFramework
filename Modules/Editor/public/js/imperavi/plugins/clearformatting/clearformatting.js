(function($)
{
    $.Redactor.prototype.clearformatting = function ()
    {
        return {
            init: function () {
                var button = this.button.addBefore('formatting', 'clearformatting', this.lang.get('clear_format'));

                this.button.addCallback(button, $.proxy(function () {
                    this.clearformatting.callback();
                }, this));
            },
            callback: function () {
                var html = this.selection.html();
                var text = this.clean.getPlainText(html);
				console.log(text);
                this.insert.raw(text);
            },
        }
    }
})(jQuery);
