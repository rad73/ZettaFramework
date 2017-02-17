(function($)
{
    $.Redactor.prototype.undoredo = function ()
    {
        return {
            init: function () {
                var button = this.button.addBefore ('formatting', 'undo', this.lang.get('undo'));

                this.button.addCallback(button, $.proxy(function () {
                    this.undoredo.undo();
                }, this));
				this.button.setIcon(button, '<i class="re-icon-undo"></i>');

                var button = this.button.addAfter ('undo', 'redo', this.lang.get('redo'));

                this.button.addCallback(button, $.proxy(function () {
                    this.undoredo.redo();
                }, this));
				this.button.setIcon(button, '<i class="re-icon-redo"></i>');

            },
            undo: function () {
                this.buffer.undo();
            },
            redo: function () {
                this.buffer.redo();
            },
        }
    }
})(jQuery);
