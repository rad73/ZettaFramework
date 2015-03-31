var RedactorPlugins = RedactorPlugins || {};

(function () {
    'use strict';

    RedactorPlugins.clearformatting = {
        
    	init: function () {

            this.buttonAddBefore('formatting', 'clearformatting', 'Clear Formatting', function (_redactor, ev, button_key) {
                this.selectionSave();
                
                this.insertText(this.getRange());
                
				this.selectionRestore();
				this.sync();
				
                $(this).removeClass('redactor_act');
            });
            
            jQuery('a.re-clearformatting').css({
                backgroundImage : 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAQFJREFUeNpi/P//PwM1ABMDlQD9DVq3bp2Hurr6eVNT04PXr19XwFAACiNCeO3atR5qamrngfg/CJuYmBy8du2aArIakg3BZRgTIe9UVla2A5kG6HKfPn2yi4uLWwjzJiOu6MdnCDLg4+M7dPr0aXsmfIZIS0sbEIoEoMv4gJiFCZchQUFBBvv27WMA0vjMudDe3l4JdNUfrAFbUVHxHxmA+OiBDVIHUo8Ra0+ePBHCZggOw1AMIRhryADoBQZgmMG9A/TyDpwJEpfXkFyE4RKcCRLZsI8fP4IxIUNwpmyQBlDKhabo8yA2PkNAmJFa5RELiADlanI037x50xDGBggwANN4kVdG334eAAAAAElFTkSuQmCC)',
                backgroundPosition : '7px 7px'
            });
        }
    };
})();
