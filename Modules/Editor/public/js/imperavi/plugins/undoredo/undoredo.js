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

                var button = this.button.addAfter ('undo', 'redo', this.lang.get('redo'));

                this.button.addCallback(button, $.proxy(function () {
                    this.undoredo.redo();
                }, this));

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