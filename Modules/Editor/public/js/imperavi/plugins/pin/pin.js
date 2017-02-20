(function($)
{
    $.Redactor.prototype.pin = function ()
    {
        return {
            init: function () {
				
				if (false == this.opts.toolbarExternal) return;
				
                $.cookie('zetta_redactor_toolbar_fixed') == 1
                    ? $('body').addClass('zetta_edit_toolbar_fixed')
                    : $('body').removeClass('zetta_edit_toolbar_fixed');

                var button = this.button.add ('pin', this.lang.get('pin_redactor'));

                this.button.addCallback(button, $.proxy(function () {
                    this.pin.callback();
                }, this));

            },
            callback: function () {
                var _fixed = $('body').is('.zetta_edit_toolbar_fixed') ? 0 : 1;

                $.cookie('zetta_redactor_toolbar_fixed', _fixed, {
                    path: '/'
                });

                $('body').toggleClass('zetta_edit_toolbar_fixed');

            },
        }
    }
})(jQuery);
