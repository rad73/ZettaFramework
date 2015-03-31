var RedactorPlugins = RedactorPlugins || {};

(function () {
    'use strict';

    RedactorPlugins.pin = {
        
    	init: function () {

    		$.cookie('zetta_redactor_toolbar_fixed') == 1
    			? $('body').addClass('zetta_edit_toolbar_fixed')
    			: $('body').removeClass('zetta_edit_toolbar_fixed');
    		
            this.buttonAdd('pin', 'Pin redactor', function (_redactor, ev, button_key) {
            	
            	var _fixed = $('body').is('.zetta_edit_toolbar_fixed') ? 0 : 1;
            	
        		$.cookie('zetta_redactor_toolbar_fixed', _fixed, {
        			path: '/'
        		});

            	$('body').toggleClass('zetta_edit_toolbar_fixed');

            
            });
            
            jQuery('a.re-pin').css({
                backgroundImage : 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAMZJREFUeNpi/P//PwMlgAXGYGRkBFFGQHo1kObEof470MJgIH0BZjETmoLbQAMWQtn7gPgRsiRU7g6yGLoBn//9+7cBxADaMB2oYS+yJFTuCz4DSAY0NYCRGAuwKVCBBlgFMBx8sMmh2AKLDmg0ugHp2UCaFYeFv4HqU4H0Lrg+JAOMoWmAg4CrfwD1hALxWZSEBDGDcQ2ySqCiACD1FCh+Gk0clYMrOQM1nmBiYmrFJgfTN7jTAQhcBybf2/gUMFKanQECDABZgVfz4GDpnQAAAABJRU5ErkJggg==)',
                backgroundPosition : '8px 9px'
            });
        }
    };
})();
