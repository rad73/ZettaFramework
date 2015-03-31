var RedactorPlugins = RedactorPlugins || {};

(function () {
    'use strict';

    var _lastArray = function (array) {
        return array[array.length - 1];
    };

    RedactorPlugins.undo = {
        init: function () {
            var history = [this.get()], redactor = this;

            var push_history = function () {
                if (redactor.get() !== _lastArray(history)) {
                    history.push(redactor.get());
                }
            };

            window.setInterval(push_history, 300);

            this.buttonAdd('undo', 'Undo', function (_redactor, ev, button_key) {
                if (history.length) {
                    if (_lastArray(history) === redactor.get()) {
                        history.pop();
                    }
                    if (history.length) {
                        redactor.set(history.pop());
                    }
                }
                $(this).removeClass('redactor_act');
            });

            //this.buttonAddSeparatorBefore('undo');

            jQuery('a.redactor_btn_undo').css({
                backgroundImage : 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAALFJREFUeNpi/P//PwMlgImBQsCCSyI/P98eifth4sSJF7GpY4R5AahhPlBRIpCuB3ILgFgATe0DIE4AqjmIy4D/UEUKBFwNMmQhrjBA1vwBiDcA8QE0NQuAlukTCkSwS4A2BQKxI5BtABWDgQZCBoBcMgHGgQZgApJ8AEYYEAOg4QQzlJEq6YCJBNvj0QKY5JQYgMTeQI4BoEC8ALW9gdxA5AelUGAAPiTLAJrkRoAAAwANDkRPnyCEwwAAAABJRU5ErkJggg==)',
                backgroundPosition : '5px 5px'
            });
        }
    };
})();
