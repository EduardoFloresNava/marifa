/**
 * Manejo de UI.
 */
!(function($) {

	/**
	 * Función para la carga de un formulario modal por AJAX mostrando un mensaje de carga.
	 */
	function cargar_formulario_modal(url, callback, context)
	{
		// Aseguro cancelar otras cargas.
		if ($('#modal-loader').length > 0)
		{
			$('#modal-loader').data('loader').abort();
		}
		else
		{
			$('body').append('<div id="modal-loader"><img src="'+window.theme_url+'/assets/img/modal-loader.gif" /><div id="modal-overlay"></div></div>');
		}

		// Contexto.
		if (context == undefined)
		{
			context = this;
		}

		//TODO: Mostrar error de carga.

		// Cargo el elemento.
		$('#modal-loader').data('loader', $.ajax({
			url: url,
			context: context,
			success: function (data) {
				// Borro el cargador.
				jQuery.removeData($('#modal-loader'), 'loader');
				$('#modal-loader').fadeOut();

				$('body').append(data);

				callback.call(this, this);
			},
			type: 'GET'
		}));
	}

	/**
	 * Formulario modal de inicio de sesión.
	 */
	$('#login-modal').click(function (e) {
		e.preventDefault();

		// Verifico si tengo que cargar el formulario.
		if ($('#login-modal-form').length <= 0)
		{
			cargar_formulario_modal(window.site_url+'/usuario/login', function () {
				// Muestro el formulario.
				$('#login-modal-form').modal();

				// Tooltips.
				$('#login-modal-form [title]').tooltip();

				// Agrego evento.
				$('#login-modal-form .modal-footer .btn[type="submit"]').click(do_modal_login);

				// Doy foco.
				$('#login-modal-form-nick').focus();
			});
		}

		// Muestro modal.
		$('#login-modal-form').modal();
	});

	/**
	 * Inicio de sesión por AJAX.
	 */
	$('#login [type="submit"]').click(function(e) {
		// Valor de los campos.
		var nick = $('#login #nick').val(),
		    password = $('#login #password').val();

		// Desactivo el botón.
		$(this).attr('disabled', true);

		// Borro mensajes de error viejos.
		$('#login .alert').remove();

		// Agrego ícono de carga.
		$(this).after(' <img class="login-ajax" src="'+window.theme_url+'/assets/img/ajax.gif" />');

		// Realizo la petición ajax.
		$.ajax({
			url: window.site_url+'/usuario/login',
			complete: function (jqXHR, textStatus) {
				if (jqXHR['status'] == 303)
				{
					location.href = jqXHR['responseText'];
				}
			},
			success: function (data) {
				// Borro icono ajax.
				$('#login .login-ajax').remove();

				// Activo el botón.
				$('#login [type="submit"]').removeAttr('disabled');

				// Verifico formato.
				if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
				{
					$('#login legend').before('<div class="alert">Se produjo un error al procesar la petición.</div>');
				}

				// Verifico la respuesta.
				if (data['response'] == 'ERROR')
				{
					// Muestro el mensaje de error.
					if (data['body']['error'])
					{
						$('#login legend').before('<div class="alert">'+data['body']['error']+'</div>');
					}

					// Mensajes de error en elementos.
					if (data['body']['error_nick'])
					{
						$('#login #nick').closest('.control-group').addClass('error');
					}
					else
					{
						$('#login #nick').closest('.control-group').removeClass('error');
					}
					
					if (data['body']['error_password'])
					{
						$('#login #password').closest('.control-group').addClass('error');
					}
					else
					{
						$('#login #password').closest('.control-group').removeClass('error');
					}
				}
				else if (data['response'] == 'OK' && data['redirect'] != undefined)
				{
					location.href = data['redirect'];
				}
				else
				{
					$('#login legend').before('<div class="alert">Se produjo un error al procesar la petición.</div>');
				}
			},
			data: {
				nick: nick,
				password: password
			},
			dataType: 'json',
			type: 'POST'
		});

		e.preventDefault();
	});

	/**
	 * Inicio de sesión del cuadro modal.
	 */
	function do_modal_login(e) {
		// Valor de los campos.
		var nick = $('#login-modal-form #login-modal-form-nick').val(),
		    password = $('#login-modal-form #login-modal-form-password').val();

		// Desactivo el botón.
		$(this).attr('disabled', true);

		// Borro mensajes de error viejos.
		$('#login-modal-form .alert').remove();

		// Agrego ícono de carga.
		$(this).before(' <img class="login-modal-form-ajax" src="'+window.theme_url+'/assets/img/ajax.gif" />');

		// Realizo la petición ajax.
		$.ajax({
			url: window.site_url+'/usuario/login',
			complete: function (jqXHR, textStatus) {
				if (jqXHR['status'] == 303)
				{
					location.href = jqXHR['responseText'];
				}
			},
			success: function (data) {
				// Borro icono ajax.
				$('#login-modal-form .login-modal-form-ajax').remove();

				// Activo el botón.
				$('#login-modal-form .modal-footer .btn[type="submit"]').removeAttr('disabled');

				// Verifico formato.
				if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
				{
					$('#login-modal-form .modal-body').prepend('<div class="alert">Se produjo un error al procesar la petición.</div>');
				}

				// Verifico la respuesta.
				if (data['response'] == 'ERROR')
				{
					// Muestro el mensaje de error.
					if (data['body']['error'])
					{
						$('#login-modal-form .modal-body').prepend('<div class="alert">'+data['body']['error']+'</div>');
					}

					// Mensajes de error en elementos.
					if (data['body']['error_nick'])
					{
						$('#login-modal-form #login-modal-form-nick').closest('.control-group').addClass('error');
					}
					else
					{
						$('#login-modal-form #login-modal-form-nick').closest('.control-group').removeClass('error');
					}
					
					if (data['body']['error_password'])
					{
						$('#login-modal-form #login-modal-form-password').closest('.control-group').addClass('error');
					}
					else
					{
						$('#login-modal-form #login-modal-form-password').closest('.control-group').removeClass('error');
					}
				}
				else if (data['response'] == 'OK' && data['redirect'] != undefined)
				{
					location.href = data['redirect'];
				}
				else
				{
					$('#login-modal-form .modal-body').prepend('<div class="alert">Se produjo un error al procesar la petición.</div>');
				}
			},
			data: {
				nick: nick,
				password: password
			},
			dataType: 'json',
			type: 'POST'
		});

		e.preventDefault();
	};

	/**
	 * Formulario modal de inicio de sesión.
	 */
	$('#register-modal').click(function (e) {
		e.preventDefault();

		// Verifico si tengo que cargar el formulario.
		if ($('#register-modal-form').length <= 0)
		{
			cargar_formulario_modal(window.site_url+'/usuario/register_form', function () {
				// Muestro el formulario.
				$('#register-modal-form').modal();

				// Tooltips.
				$('#register-modal-form [title]').tooltip();

				// Eventos de verificación.
				$('#register-modal-form-nick').focusout(function(e) { registro.verificar_nick_modal(); });
				$('#register-modal-form-email').focusout(function(e) { registro.verificar_email_modal(); });
				$('#register-modal-form-password').focusout(function(e) { registro.verificar_password_modal(); registro.verificar_c_password_modal(); });
				$('#register-modal-form-c_password').focusout(function(e) { registro.verificar_c_password_modal(); });

				// Agrego evento.
				$('#register-modal-form .modal-footer .btn[type="submit"]').click(do_modal_register);

				// Doy foco.
				$('#register-modal-form-nick').focus();
			});
		}

		// Muestro modal.
		$('#register-modal-form').modal();
	});


	function do_modal_register(e) {
		// Prevengo evento por defecto (envío del formulario).
		e.preventDefault();

		// Imagen de carga.
		$(this).after(' <img class="register-ajax-submit" src="'+window.theme_url+'/assets/img/ajax.gif" />');

		// Desactivo el botón.
		$(this).attr('disabled', true);

		// Realizo todas las validaciones.
		registro.verificar_todo_modal();

		// Verifico que todo sea correcto.
		if ($('#register-modal-form .control-group.error').length == 0)
		{
			$.ajax({
				url: window.site_url+'/usuario/register',
				complete: function (jqXHR, textStatus) {
					if (jqXHR['status'] == 303)
					{
						location.href = jqXHR['responseText'];
					}

					if (jqXHR['status'] == 409)
					{
						$('#register-modal-form .modal-body').prepend('<div class="alert">'+jqXHR['responseText']+'</div>');
						$('#register-modal-form .modal-body form').fadeOut(function() { $(this).remove(); });
						$('#register-modal-form .modal-footer .btn[type="submit"]').fadeOut(function () { $(this).remove(); });
					}
				},
				success: function (data) {
					// Borro icono ajax.
					$('#register-modal-form .register-ajax-submit').remove();

					// Activo el botón.
					$('#register-modal-form .btn[type="submit"]').removeAttr('disabled');

					// Verifico formato.
					if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
					{
						$('#register-modal-form .modal-body').prepend('<div class="alert">Se produjo un error al procesar la petición.</div>');
					}

					// Verifico la respuesta.
					if (data['response'] == 'ERROR')
					{
						// Muestro el mensaje de error.
						if (data['body']['error'])
						{
							$('#register-modal-form .modal-body').prepend('<div class="alert">'+data['body']['error']+'</div>');
						}

						// Mensajes de error en elementos.
						if (data['body']['error_nick'])
						{
							$('#register-modal-form-nick').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register-modal-form-nick').closest('.control-group').removeClass('error');
						}

						if (data['body']['error_email'])
						{
							$('#register-modal-form-email').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register-modal-form-email').closest('.control-group').removeClass('error');
						}
						
						if (data['body']['error_password'])
						{
							$('#register-modal-form-password').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register-modal-form-password').closest('.control-group').removeClass('error');
						}

						if (data['body']['error_c_password'])
						{
							$('#register-modal-form-c_password').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register-modal-form-c_password').closest('.control-group').removeClass('error');
						}

						if (data['body']['error_captcha'])
						{
							$('#register-modal-form-captcha').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register-modal-form-captcha').closest('.control-group').removeClass('error');
						}
					}
					else if (data['response'] == 'OK')
					{
						// Agrego el mensaje.
						$('#register-modal-form .modal-body').prepend('<div class="alert alert-success">'+data['body']+'</div>');

						// Oculto el formulario y el botón.
						$('#register-modal-form .modal-body form').fadeOut(function () { $(this).remove(); });
						$('#register-modal-form .modal-footer .btn[type="submit"]').fadeOut(function () { $(this).remove(); });
					}
					else
					{
						$('#register-modal-form .modal-body').prepend('<div class="alert">Se produjo un error al procesar la petición.</div>');
					}
				},
				data: {
					nick: $('#register-modal-form-nick').val(),
					email: $('#register-modal-form-email').val(),
					password: $('#register-modal-form-password').val(),
					c_password: $('#register-modal-form-c_password').val(),
					captcha: $('#register-modal-form-captcha').val()
				},
				dataType: 'json',
				type: 'POST'
			});
		}
	}

	/**
	 * Verificaciones del registro.
	 */
	var registro = {
		verificar_nick: function () {
			var nick = $('#register #nick'),
				nick_val = nick.val();

			// Evito volver a validar lo mismo.
			if (nick.data('validation-text') == nick_val)
			{
				return;
			}

			// Verifico y termino validación ajax.
			if (nick.data('validation-ajax') != undefined)
			{
				// Borro icono de carga.
				$('#register .register-ajax-nick').remove();

				// Termino petición.
				nick.data('validation-ajax').abort();

				// Borro los datos.
				nick.removeData('validation-ajax');
			}

			if (/^[a-zA-Z0-9]{4,16}$/.test(nick_val))
			{
				nick.after(' <img class="register-ajax-nick" src="'+window.theme_url+'/assets/img/ajax.gif" />');

				// Asigno que valido.
				nick.data('validation-text', nick_val);

				// Realizo validación ajax.
				nick.data('validation-ajax', $.ajax({
					async: false,
					url: window.site_url+'/usuario/validar_nick',
					success: function (data) {
						// Borro icono ajax.
						$('#register .register-ajax-nick').remove();

						// Verifico formato.
						if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
						{
							$('#register legend').before('<div class="alert">Se produjo un error al procesar la petición.</div>');
						}
						else
						{
							// Verifico la respuesta.
							if (data['response'] == 'OK')
							{
								$('#register #nick').closest('.control-group').removeClass('error').addClass('success');

								var obj = $('#register #nick').closest('.controls').find('.help-inline[data-text]');
								if (obj.length > 0)
								{
									obj.text(obj.attr('data-text'));
								}
							}
							else
							{
								if (data['body'] != '')
								{
									$('#register #nick').closest('.controls').find('.help-inline').text(data['body']);
								}
								$('#register #nick').closest('.control-group').removeClass('success').addClass('error');
							}
						}						

						nick.removeData('validation-ajax');
					},
					data: {
						nick: nick_val
					},
					dataType: 'json',
					type: 'POST'
				}));
			}
			else
			{
				nick.closest('.control-group').removeClass('success').addClass('error');
				return false;
			}
		},
		// Verificamos el usuario para el formulario modal.
		verificar_nick_modal: function () {
			var nick = $('#register-modal-form-nick'),
				nick_val = nick.val();

			// Evito volver a validar lo mismo.
			if (nick.data('validation-text') == nick_val)
			{
				return;
			}

			// Verifico y termino validación ajax.
			if (nick.data('validation-ajax') != undefined)
			{
				// Borro icono de carga.
				$('#register-modal-form .register-ajax-nick').remove();

				// Termino petición.
				nick.data('validation-ajax').abort();

				// Borro los datos.
				nick.removeData('validation-ajax');
			}

			if (/^[a-zA-Z0-9]{4,16}$/.test(nick_val))
			{
				console.log(nick);
				nick.after(' <img class="register-ajax-nick" src="'+window.theme_url+'/assets/img/ajax.gif" />');

				// Asigno que valido.
				nick.data('validation-text', nick_val);

				// Realizo validación ajax.
				nick.data('validation-ajax', $.ajax({
					async: false,
					url: window.site_url+'/usuario/validar_nick',
					success: function (data) {
						// Borro icono ajax.
						$('#register-modal-form .register-ajax-nick').remove();

						// Verifico formato.
						if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
						{
							$('#register-modal-form .modal-body').prepend('<div class="alert">Se produjo un error al procesar la petición.</div>');
						}
						else
						{
							// Verifico la respuesta.
							if (data['response'] == 'OK')
							{
								// Marco como correcto.
								$('#register-modal-form-nick').closest('.control-group').removeClass('error').addClass('success');

								// Borro posible mensaje de error.
								$('#register-modal-form-nick').parent().find('.help-block').slideUp(function () { $(this).remove(); });
							}
							else
							{
								if (data['body'] != '')
								{
									if ($('#register-modal-form-nick').parent().find('.help-block').length <= 0)
									{
										$('#register-modal-form-nick').after('<div class="help-block">'+data['body']+'</div>');
									}
									else
									{
										$('#register-modal-form-nick').parent().find('.help-block').text(data['body']);
									}
								}
								$('#register-modal-form-nick').closest('.control-group').removeClass('success').addClass('error');
							}
						}						

						nick.removeData('validation-ajax');
					},
					data: {
						nick: nick_val
					},
					dataType: 'json',
					type: 'POST'
				}));
			}
			else
			{
				nick.closest('.control-group').removeClass('success').addClass('error');
				return false;
			}
		},
		// Verifico email.
		verificar_email: function () {
			var email = $('#register #email'),
				email_val = email.val();

			// Evito volver a validar lo mismo.
			if (email.data('validation-text') == email_val)
			{
				return;
			}

			// Verifico y termino validación ajax.
			if (email.data('validation-ajax') != undefined)
			{
				// Borro icono de carga.
				$('#register .register-ajax-email').remove();

				// Termino petición.
				email.data('validation-ajax').abort();

				// Borro los datos.
				email.removeData('validation-ajax');
			}

			if (/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/.test(email_val))
			{
				email.after(' <img class="register-ajax-email" src="'+window.theme_url+'/assets/img/ajax.gif" />');

				// Asigno que valido.
				email.data('validation-text', email_val);

				// Realizo validación ajax.
				email.data('validation-ajax', $.ajax({
					async: false,
					url: window.site_url+'/usuario/validar_email',
					success: function (data) {
						// Borro icono ajax.
						$('#register .register-ajax-email').remove();

						// Verifico formato.
						if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
						{
							$('#register legend').before('<div class="alert">Se produjo un error al procesar la petición.</div>');
						}
						else
						{
							// Verifico la respuesta.
							if (data['response'] == 'OK')
							{
								$('#register #email').closest('.control-group').removeClass('error').addClass('success');

								var obj = $('#register #email').closest('.controls').find('.help-inline[data-text]');
								if (obj.length > 0)
								{
									obj.text(obj.attr('data-text'));
								}
							}
							else
							{
								if (data['body'] != '')
								{
									$('#register #email').closest('.controls').find('.help-inline').text(data['body']);
								}
								$('#register #email').closest('.control-group').removeClass('success').addClass('error');
							}
						}						

						email.removeData('validation-ajax');
					},
					data: {
						email: email_val
					},
					dataType: 'json',
					type: 'POST'
				}));
			}
			else
			{
				email.closest('.control-group').addClass('error').removeClass('success');
				return false;
			}
		},
		// Verifico email.
		verificar_email_modal: function () {
			var email = $('#register-modal-form-email'),
				email_val = email.val();

			// Evito volver a validar lo mismo.
			if (email.data('validation-text') == email_val)
			{
				return;
			}

			// Verifico y termino validación ajax.
			if (email.data('validation-ajax') != undefined)
			{
				// Borro icono de carga.
				$('#register-modal-form .register-ajax-email').remove();

				// Termino petición.
				email.data('validation-ajax').abort();

				// Borro los datos.
				email.removeData('validation-ajax');
			}

			if (/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/.test(email_val))
			{
				email.after(' <img class="register-ajax-email" src="'+window.theme_url+'/assets/img/ajax.gif" />');

				// Asigno que valido.
				email.data('validation-text', email_val);

				// Realizo validación ajax.
				email.data('validation-ajax', $.ajax({
					async: false,
					url: window.site_url+'/usuario/validar_email',
					success: function (data) {
						// Borro icono ajax.
						$('#register-modal-form .register-ajax-email').remove();

						// Verifico formato.
						if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
						{
							$('#register-modal-form .modal-body').before('<div class="alert">Se produjo un error al procesar la petición.</div>');
						}
						else
						{
							// Verifico la respuesta.
							if (data['response'] == 'OK')
							{
								// Marco como correcto.
								$('#register-modal-form-email').closest('.control-group').removeClass('error').addClass('success');

								// Borro posible mensaje de error.
								$('#register-modal-form-email').parent().find('.help-block').slideUp(function () { $(this).remove(); });
							}
							else
							{
								if (data['body'] != '')
								{
									if ($('#register-modal-form-email').parent().find('.help-block').length <= 0)
									{
										$('#register-modal-form-email').after('<div class="help-block">'+data['body']+'</div>');
									}
									else
									{
										$('#register-modal-form-email').parent().find('.help-block').text(data['body']);
									}
								}
								$('#register-modal-form-email').closest('.control-group').removeClass('success').addClass('error');
							}
						}						

						email.removeData('validation-ajax');
					},
					data: {
						email: email_val
					},
					dataType: 'json',
					type: 'POST'
				}));
			}
			else
			{
				email.closest('.control-group').addClass('error').removeClass('success');
				return false;
			}
		},
		// Verificar contraseña.
		verificar_password: function () {
			var password = $('#register #password').val();

			if (/^[a-zA-Z0-9\-_@\*\+\/#$%]{6,20}$/.test(password))
			{
				$('#password').closest('.control-group').removeClass('error').addClass('success');
				return true;
			}
			else
			{
				$('#password').closest('.control-group').removeClass('success').addClass('error');
				return false;
			}
		},
		// Verificar 
		verificar_password_modal: function () {
			var password = $('#register-modal-form-password').val();

			if (/^[a-zA-Z0-9\-_@\*\+\/#$%]{6,20}$/.test(password))
			{
				$('#register-modal-form-password').closest('.control-group').removeClass('error').addClass('success');
				return true;
			}
			else
			{
				$('#register-modal-form-password').closest('.control-group').removeClass('success').addClass('error');
				return false;
			}
		},
		// Verificación de la contraseña de comprobación.
		verificar_c_password: function () {
			// Verifico si es correcto.
			var password = $('#register #password').val(),
				c_password = $('#register #c_password').val();

			// Marco o quito la marca de incorrecto.
			if (c_password != '' && password == c_password)
			{
				$('#c_password').closest('.control-group').removeClass('error').addClass('success');
				return true;
			}
			else
			{
				$('#c_password').closest('.control-group').removeClass('success').addClass('error');
				return false;
			}
		},
		// Verificación de la contraseña de comprobación para formulario modal.
		verificar_c_password_modal: function () {
			var password = $('#register-modal-form-password').val(),
				c_password = $('#register-modal-form-c_password').val();

			// Marco o quito la marca de incorrecto.
			if (c_password != '' && password == c_password)
			{
				$('#register-modal-form-c_password').closest('.control-group').removeClass('error').addClass('success');
				return true;
			}
			else
			{
				$('#register-modal-form-c_password').closest('.control-group').removeClass('success').addClass('error');
				return false;
			}
		},
		// Verificar todos los elementos.
		verificar_todo: function () {
			// Realizo todas las validaciones.
			registro.verificar_nick();
			registro.verificar_email();
			registro.verificar_password();
			registro.verificar_c_password();
		},
		// Verificar todo para el formulario modal.
		verificar_todo_modal: function () {
			registro.verificar_nick_modal();
			registro.verificar_email_modal();
			registro.verificar_password_modal();
			registro.verificar_c_password_modal();
		}
	};

	// Asigno contenido por defecto para los elementos.
	$('#register #nick').closest('.controls').find('.help-inline').attr('data-text', $('#register #nick').closest('.controls').find('.help-inline').text());
	$('#register #email').closest('.controls').find('.help-inline').attr('data-text', $('#register #email').closest('.controls').find('.help-inline').text());
	$('#register #password').closest('.controls').find('.help-inline').attr('data-text', $('#register #password').closest('.controls').find('.help-inline').text());

	// Eventos de verificación.
	$('#register #nick').focusout(function(e) { registro.verificar_nick(); });
	$('#register #email').focusout(function(e) { registro.verificar_email(); });
	$('#register #password').focusout(function(e) { registro.verificar_password(); registro.verificar_c_password(); });
	$('#register #c_password').focusout(function(e) { registro.verificar_c_password(); });

	$('#register #register-button').click(function (e) {
		// Prevengo evento por defecto (envío del formulario).
		e.preventDefault();

		// Imagen de carga.
		$(this).after(' <img class="register-ajax-submit" src="'+window.theme_url+'/assets/img/ajax.gif" />');

		// Desactivo el botón.
		$(this).attr('disabled', true);

		// Realizo todas las validaciones.
		registro.verificar_todo();

		// Verifico que todo sea correcto.
		if ($('#register .control-group.error').length == 0)
		{
			$.ajax({
				url: window.site_url+'/usuario/register',
				complete: function (jqXHR, textStatus) {
					if (jqXHR['status'] == 303)
					{
						location.href = jqXHR['responseText'];
					}

					if (jqXHR['status'] == 409)
					{
						$('#register').before('<div class="alert">'+jqXHR['responseText']+'</div>');
						$('#register').fadeOut(function() { $(this).remove(); });
					}
				},
				success: function (data) {
					// Borro icono ajax.
					$('#register .register-ajax-submit').remove();

					// Activo el botón.
					$('#register #register-button').removeAttr('disabled');

					// Verifico formato.
					if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
					{
						$('#register legend').before('<div class="alert">Se produjo un error al procesar la petición.</div>');
					}

					// Verifico la respuesta.
					if (data['response'] == 'ERROR')
					{
						// Muestro el mensaje de error.
						if (data['body']['error'])
						{
							$('#register legend').before('<div class="alert">'+data['body']['error']+'</div>');
						}

						// Mensajes de error en elementos.
						if (data['body']['error_nick'])
						{
							$('#register #nick').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register #nick').closest('.control-group').removeClass('error');
						}

						if (data['body']['error_email'])
						{
							$('#register #email').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register #email').closest('.control-group').removeClass('error');
						}
						
						if (data['body']['error_password'])
						{
							$('#register #password').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register #password').closest('.control-group').removeClass('error');
						}

						if (data['body']['error_c_password'])
						{
							$('#register #c_password').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register #c_password').closest('.control-group').removeClass('error');
						}

						if (data['body']['error_captcha'])
						{
							$('#register #captcha').closest('.control-group').addClass('error');
						}
						else
						{
							$('#register #captcha').closest('.control-group').removeClass('error');
						}
					}
					else if (data['response'] == 'OK')
					{
						// Agrego el mensaje.
						$('#register').before('<div class="alert alert-success">'+data['body']+'</div>');

						// Oculto el formulario.
						$('#register').fadeOut(function () { $(this).remove(); });
					}
					else
					{
						$('#register legend').before('<div class="alert">Se produjo un error al procesar la petición.</div>');
					}
				},
				data: {
					nick: $('#register #nick').val(),
					email: $('#register #email').val(),
					password: $('#register #password').val(),
					c_password: $('#register #c_password').val(),
					captcha: $('#register #captcha').val()
				},
				dataType: 'json',
				type: 'POST'
			});
		}
	});

	// Denunciar usuarios, fotos y posts.
	var denuncia = {
		// Iniciamos el proceso (registramos eventos).
		iniciar: function() {
			// Registramos los eventos.
			$('#perfil-denunciar-usuario, .perfil-denunciar-usuario').click(function (e) {
				e.preventDefault();

				// Cargo ID.
				var modal_id = $(this).attr('data-modal-id') == undefined ? '' : '-'+$(this).attr('data-modal-id');

				// Verifico si tengo que cargar el formulario.
				if ($('#denunciar-usuario-modal-form'+modal_id).length <= 0)
				{
					cargar_formulario_modal.call(this, $(this).attr('href'), function () {
						// Cargo ID.
						var modal_id = $(this).attr('data-modal-id') == undefined ? '' : '-'+$(this).attr('data-modal-id');

						// Muestro el formulario.
						$('#denunciar-usuario-modal-form'+modal_id).modal();

						// Eventos de validación.
						$('#denunciar-usuario-modal-form-motivo'+modal_id).focusout(function () { denuncia.verificar_motivo($(this), [0, 1, 2, 3, 4, 5]); });
						$('#denunciar-usuario-modal-form-comentario'+modal_id).focusout(function () { denuncia.verificar_comentario($(this)); });

						// Agrego evento.
						$('#denunciar-usuario-modal-form'+modal_id+' .modal-footer .btn[type="submit"]').bind('click', {valores: [0, 1, 2, 3, 4, 5]}, denuncia.enviar_denuncia);

						// Doy foco.
						$('#denunciar-usuario-modal-form-motivo'+modal_id).focus();
					});
				}

				// Muestro modal.
				$('#denunciar-usuario-modal-form'+modal_id).modal();
			});

			$('#post-denunciar, .post-denunciar').click(function (e) {
				e.preventDefault();

				// Cargo ID.
				var modal_id = $(this).attr('data-modal-id') == undefined ? '' : '-'+$(this).attr('data-modal-id');

				// Verifico si tengo que cargar el formulario.
				if ($('#denunciar-post-modal-form'+modal_id).length <= 0)
				{
					cargar_formulario_modal.call(this, $(this).attr('href'), function () {
						// Cargo ID.
						var modal_id = $(this).attr('data-modal-id') == undefined ? '' : '-'+$(this).attr('data-modal-id');

						// Muestro el formulario.
						$('#denunciar-post-modal-form'+modal_id).modal();

						// Eventos de validación.
						$('#denunciar-post-modal-form-motivo'+modal_id).focusout(function () { denuncia.verificar_motivo($(this), [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]); });
						$('#denunciar-post-modal-form-comentario'+modal_id).focusout(function () { denuncia.verificar_comentario($(this)); });

						// Agrego evento.
						$('#denunciar-post-modal-form'+modal_id+' .modal-footer .btn[type="submit"]').bind('click', {valores: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]}, denuncia.enviar_denuncia);

						// Doy foco.
						$('#denunciar-post-modal-form-motivo'+modal_id).focus();
					});
				}

				// Muestro modal.
				$('#denunciar-post-modal-form'+modal_id).modal();
			});

			$('#denunciar-foto, .denunciar-foto').click(function (e) {
				e.preventDefault();

				// Cargo ID.
				var modal_id = $(this).attr('data-modal-id') == undefined ? '' : '-'+$(this).attr('data-modal-id');

				// Verifico si tengo que cargar el formulario.
				if ($('#denunciar-foto-modal-form'+modal_id).length <= 0)
				{
					cargar_formulario_modal.call(this, $(this).attr('href'), function () {
						// Cargo ID.
						var modal_id = $(this).attr('data-modal-id') == undefined ? '' : '-'+$(this).attr('data-modal-id');

						// Muestro el formulario.
						$('#denunciar-foto-modal-form'+modal_id).modal();

						// Eventos de validación.
						$('#denunciar-foto-modal-form-motivo'+modal_id).focusout(function () { denuncia.verificar_motivo($(this), [0, 1, 2, 3, 4, 5, 6, 7]); });
						$('#denunciar-foto-modal-form-comentario'+modal_id).focusout(function () { denuncia.verificar_comentario($(this)); });

						// Agrego evento.
						$('#denunciar-foto-modal-form'+modal_id+' .modal-footer .btn[type="submit"]').bind('click', {valores: [0, 1, 2, 3, 4, 5, 6, 7]}, denuncia.enviar_denuncia);

						// Doy foco.
						$('#denunciar-foto-modal-form-motivo'+modal_id).focus();
					});
				}

				// Muestro modal.
				$('#denunciar-foto-modal-form'+modal_id).modal();
			});
		},
		// Verifico el motivo de la denuncia.
		verificar_motivo: function(elemento, valores) {
			var motivo = elemento,
			    motivo_val = motivo.val();

			if (jQuery.inArray(parseInt(motivo_val), valores) >= 0)
			{
				motivo.closest('.control-group').removeClass('error').addClass('success');
			}
			else
			{
				motivo.closest('.control-group').removeClass('success').addClass('error');
			}
		},
		// Verifico el comentario de la denuncia.
		verificar_comentario: function(elemento) {
			var comentario = elemento,
			    comentario_val = comentario.val();

			if (comentario_val == '' || (comentario_val.length > 5 && comentario_val.length < 400))
			{
				comentario.closest('.control-group').removeClass('error').addClass('success');
			}
			else
			{
				comentario.closest('.control-group').removeClass('success').addClass('error');
			}
		},
		// Verifico todos los datos.
		verificar_todo: function (comentario, motivo, valores) {
			denuncia.verificar_motivo(motivo, valores);
			denuncia.verificar_comentario(comentario);
		},
		// Envío la denuncia.
		enviar_denuncia: function (e) {
			// Evito acción por defecto.
			e.preventDefault();

			// Formulario modal.
			var modal = $(this).closest('.modal');

			// Imagen de carga.
			$(this).after(' <img class="denuncia-ajax-submit" src="'+window.theme_url+'/assets/img/ajax.gif" />');

			// Desactivo el botón.
			$(this).attr('disabled', true);

			// Realizo todas las validaciones.
			denuncia.verificar_todo($(modal).find('[name="comentario"]'), $(modal).find('[name="motivo"]'), e.data.valores);

			// Verifico que todo sea correcto.
			if ($(modal).find('.control-group.error').length == 0)
			{
				$.ajax({
					url: $(modal).find('form').attr('action'),
					context: $(modal),
					complete: function (jqXHR, textStatus) {
						if (jqXHR['status'] == 303)
						{
							location.href = jqXHR['responseText'];
						}

						if (jqXHR['status'] == 409)
						{
							$(this).find('.modal-body').prepend('<div class="alert">'+jqXHR['responseText']+'</div>');
							$(this).find('.modal-body form').fadeOut(function() { $(this).remove(); });
							$(this).find('.modal-footer .btn[type="submit"]').fadeOut(function () { $(this).remove(); });
						}
					},
					success: function (data) {
						// Borro icono ajax.
						$(this).find('.denuncia-ajax-submit').remove();

						// Activo el botón.
						$(this).find('.btn[type="submit"]').removeAttr('disabled');

						// Verifico formato.
						if (data == undefined || data['response'] == undefined || (data['body'] == undefined && data['redirect'] == undefined))
						{
							$(this).find('.modal-body').prepend('<div class="alert">Se produjo un error al procesar la petición.</div>');
						}

						// Verifico la respuesta.
						if (data['response'] == 'ERROR')
						{
							// Muestro el mensaje de error.
							if (data['body']['error'])
							{
								$(this).find('.modal-body').prepend('<div class="alert">'+data['body']['error']+'</div>');
							}

							// Mensajes de error en elementos.
							if (data['body']['error_motivo'])
							{
								$(this).find('[name="motivo"]').closest('.control-group').addClass('error');

								// Agrego mensaje de error.
								if ($(this).find('[name="motivo"]').closest('.controls').find('.help-block').length <= 0)
								{
									$(this).find('[name="motivo"]').after('<div class="help-block">'+data['body']['error_motivo']+'</div>');
								}
								else
								{
									$(this).find('[name="motivo"]').closest('.controls').find('.help-block').text(data['body']['error_motivo']);
								}
							}
							else
							{
								$(this).find('[name="motivo"]').closest('.controls').find('.help-block').slideUp(function() { $(this).remove(); });
								$(this).find('[name="motivo"]').closest('.control-group').removeClass('error');
							}

							if (data['body']['error_comentario'])
							{
								$(this).find('[name="comentario"]').closest('.control-group').addClass('error');

								// Agrego mensaje de error.
								if ($(this).find('[name="comentario"]').closest('.controls').find('.help-block').length <= 0)
								{
									$(this).find('[name="comentario"]').after('<div class="help-block">'+data['body']['error_comentario']+'</div>');
								}
								else
								{
									$(this).find('[name="comentario"]').closest('.controls').find('.help-block').text(data['body']['error_comentario']);
								}
							}
							else
							{
								$(this).find('[name="comentario"]').closest('.controls').find('.help-block').slideUp(function() { $(this).remove(); });
								$(this).find('[name="comentario"]').closest('.control-group').removeClass('error');
							}
						}
						else if (data['response'] == 'OK')
						{
							// Agrego el mensaje.
							$(this).find('.modal-body').prepend('<div class="alert alert-success">'+data['body']+'</div>');

							// Oculto el formulario y el botón.
							$(this).find('.modal-body form').fadeOut(function () { $(this).remove(); });
							$(this).find('.modal-footer .btn[type="submit"]').fadeOut(function () { $(this).remove(); });
						}
						else
						{
							$(this).find('.modal-body').prepend('<div class="alert">Se produjo un error al procesar la petición.</div>');
						}
					},
					data: {
						motivo: $(modal).find('[name="motivo"]').val(),
						comentario: $(modal).find('[name="comentario"]').val()
					},
					dataType: 'json',
					type: 'POST'
				});
			}
			else
			{
				// Borro icono ajax.
				$(this).find('.denuncia-ajax-submit').remove();

				// Activo el botón.
				$(this).find('.btn[type="submit"]').removeAttr('disabled');
			}
		}
	};

	// Iniciamos las denuncias.
	denuncia.iniciar();
} (jQuery));