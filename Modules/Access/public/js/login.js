google.load("elements", "1", {packages: "keyboard"});

	
$(function () {
	
	{	/* Отправка формы */
		
		$('form#login').submit(function() {
	
			var _pass = $('INPUT#auth_password', this).val(),
				_hash = $('INPUT#auth_hash', this).val().split('~'),
				_publicKey = _hash[0], 
				_module = _hash[1];
				
			// хэшируем в md5 для русских паролей и для пущей безопасности
			var _encode = RSA.encrypt(md5.hex(_pass), _publicKey, _module);
			
			$('INPUT#auth_hash', this).val(_encode);
			$('INPUT#auth_password', this).val('password');
	
		});
		
	}

	$('#username').focus();
	
	{	/* Работаем с клавиатурой от google */
		
		var _kbd = new google.elements.keyboard.Keyboard(['en']);
		_kbd.setVisible(false);
		
		var _toggleGoogleKbd = function (mode) {
	
			if ($('#kbd').is(':visible') || mode == 'hide') {
				$('#kbd').fadeOut();
			}
			else {
				$('#kbd').fadeIn();
				$('#auth_password').focus();
			}
		
		}
		
		$('#protected_ico').click (function () {
			_toggleGoogleKbd();
			return false;
		});
		
		$(window).blur(function () {
			_toggleGoogleKbd('hide');
		});
		
		$(document).keypress (
    		function ( e ) {
    			if ( e.keyCode == 27 ) {
    				_toggleGoogleKbd('hide');
    			}
    			
    		}
    	);
		
	}

	

});
