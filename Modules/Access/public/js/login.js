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

});
