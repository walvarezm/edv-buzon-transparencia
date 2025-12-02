/**
 * Codigo Jquery para validacion de campos del Formulario BT 
 * Autor: @walvarez - 2024
 */
jQuery(document).ready(function($) {
	
	var formularioValido = true;
	
	// Función de validación de campos
	function validateField(textField, type, isRequired) {
		var value = textField.val();
		var regex;
		var errorMessage = '';
		var styleBorder = 'border-color:#dc3232; border:1px solid #dc3232';
		
		// Eliminar cualquier mensaje de error anterior
		textField.next('.error-message').remove();
		textField.next('.wpcf7-not-valid-tip').remove();

		switch(type) {
			case 'text':
				regex = /^[a-zA-Z áéíóúÁÉÍÓÚüÜñÑ]+$/; // Permite letras, números, espacios y tildes 
				if (isRequired && value.trim() === '') {
					errorMessage = 'Este campo es requerido.';
				}else if ((!regex.test(value) && value.trim() !== '')) {
					errorMessage = 'Por favor, ingresa solamente letras y espacios.';	
				}
				break;
			case 'textarea':
				regex = /^[a-zA-Z0-9 .,áéíóúÁÉÍÓÚüÜñÑ]+$/; // Permite letras, números, comas, puntos, espacios y tildes
				if (isRequired && value.trim() === '') {
					errorMessage = 'Este campo es requerido.';
				}else if ((!regex.test(value) && value.trim() !== '')) {
					errorMessage = 'Por favor, ingresa solamente letras y espacios.';	
				}
				break;					
			case 'date':
				regex = /^[a-zA-Z0-9 ]+$/; // Permite letras, números y espacios
				if (isRequired && value.trim() === '') {
					errorMessage = 'Este campo es requerido.';
				}
				break;	
			case 'tel':
				regex = /^\d{8}$/; // Permite solo números y debe tener 8 caracteres
				if (isRequired && value.trim() === '') {
					errorMessage = 'Este campo es requerido.';
				}else if ((!regex.test(value) && value.trim() !== '')) {
					errorMessage = 'Por favor, ingresa un número de teléfono válido de 8 dígitos.';	
				}
				break;
			case 'email':
				regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Permite solo formato de email
				if (isRequired && value.trim() === '') {
					errorMessage = 'Este campo es requerido.';
				}else if ((!regex.test(value) && value.trim() !== '')) {
					errorMessage = 'Por favor, ingresa un correo electrónico válido.';
				}
				break;
			case 'file':
				var file = textField[0].files[0];
				if (isRequired && file) {
					var allowedExtensions = /(\.pdf|\.jpg|\.png)$/i;
					if (!allowedExtensions.exec(file.name)) {
						errorMessage = 'Solo se permiten archivos con extensión .pdf, .jpg, .png.';
					} else if (file.size > 5 * 1024 * 1024) { // 5 MB
						errorMessage = 'El archivo debe ser menor a 5 MB.';
					}
				} else {
					errorMessage = 'Este campo es requerido. Selecciona un archivo.';
				}
				break;
			case 'checkbox':
				var opciones = $('input[name="'+textField[0].name+'"]');
				var valido = true;
				opciones.each(function(i,data) {
					if (!$(data).checked) {
						valido = false;
					}
				});
				textField.next('.wpcf7-not-valid-tip').remove();
				if (isRequired && !valido) {
					errorMessage = 'Este campo es requerido.';
					textField = $('.wpcf7-form-control.wpcf7-checkbox');
					textField.next('.wpcf7-not-valid-tip').remove();
					styleBorder = '';
				}
				break;
			case 'radio':
				textField.next('.wpcf7-not-valid-tip').remove();
				if (!$('input[name="' + textField.attr('name') + '"]:checked').length) {
					textField = $('.wpcf7-form-control.wpcf7-radio');
					textField.next('.wpcf7-not-valid-tip').remove();
					errorMessage = 'Este campo es requerido.';
					styleBorder = '';
				}
				break;	
			}

			if (errorMessage !== '') {	
				// Añadir una clase de error al campo (opcional)
				textField.addClass('wpcf7-not-valid');
				// Añadir el atributo aria-invalid="true"
				textField.attr('aria-invalid', 'true');
				//
				textField.attr('style', styleBorder);

				// Mostrar un mensaje de error debajo del campo
				if(errorMessage !== 'Este campo es requerido.')
					textField.after('<span class="error-message wpcf7-not-valid-tip" >' + errorMessage + '</span>');

			} else {
				// Remover la clase de error si la validación es exitosa
				textField.removeClass('wpcf7-not-valid');
				// Remover el atributo aria-invalid si la validación es exitosa
				textField.removeAttr('aria-invalid');

				textField.removeAttr('style');
			}
		}

	// Validacion de un campo segun el tipo de evento capturado en el formulario BT
	
	//Validacion de la pregunta 1
	$('textarea[name="textarea-pregunta-1"]').on('blur', function() {
		validateField($(this), 'textarea', true); // validacion
		validarFormularioBT();
	});
	//Validacion de la pregunta 2
	$('input[name="date-pregunta-2"]').on('blur', function() {
		validateField($(this), 'date', true); // validacion
		validarFormularioBT();
	});
	//Validacion de la pregunta 3
	$('input[name="checkbox-pregunta-3[]"]').on('change', function() {
		validateField($(this), 'checkbox', true); // validacion
		validarFormularioBT();
	});
	//Validacion de la pregunta 4
	$('input[name="text-pregunta-4"]').on('blur', function() {
		validateField($(this), 'text', false); // validacion
		validarFormularioBT();
	});
	//Validacion de la pregunta 7
	$('input[name="text-pregunta-7"]').on('blur', function() {
		validateField($(this), 'text', false); // validacion
		validarFormularioBT();
	});
	//Validacion de la pregunta 8
	$('input[name="tel-pregunta-8"]').on('blur', function() {
		validateField($(this), 'tel', false); // validcion teléfono
		validarFormularioBT();
	});
	//Validacion de la pregunta 9
	$('input[name="email-pregunta-9"]').on('blur', function() {
		validateField($(this), 'email', false); // validacion email
		validarFormularioBT();
	});
	//Validacion de la pregunta 6
	$('input[name="file-pregunta-6"]').on('change', function(e) {
		validateField($(this), 'file', true); // archivo es requerido
		validarFormularioBT();
	});

	//Validacion de los campos al enviar el formulario
	$('form.wpcf7-form').on('submit', function(event) {
		if($('input[name="form-bt"]').val() == "form-bt"){
			var esValidoFormulario = validarFormularioBT();
			if (!esValidoFormulario) {
				event.preventDefault();
			}
		}
	});
	
	//Funcion de validacion de todos los campos del Formulario BT
	function validarFormularioBT(){
		var valid = true;

		$('textarea[name="textarea-pregunta-1"]').each(function() {
			validateField($(this), 'textarea', true);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});

		$('input[name="date-pregunta-2"]').each(function() {
			validateField($(this), 'date', true);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});
		
		var p3 = $('input[name="checkbox-pregunta-3[]"]');
		validateField(p3, 'checkbox', true);
		if (p3.hasClass('wpcf7-not-valid')) valid = false;
		
		$('input[name="text-pregunta-4"]').each(function() {
			validateField($(this), 'text', false);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});
		
		$('input[name="checkbox-pregunta-5"]').each(function() {
			validateField($(this), 'radio', true);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});

		$('input[name="text-pregunta-7"]').each(function() {
			validateField($(this), 'text', false);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});

		$('input[name="tel-pregunta-8"]').each(function() {
			validateField($(this), 'tel', false);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});

		$('input[name="email-pregunta-9"]').each(function() {
			validateField($(this), 'email', false);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});

		$('input[name="file-pregunta-6"]').each(function() {
			validateField($(this), 'file', true);
			if ($(this).hasClass('wpcf7-not-valid')) valid = false;
		});
		
		if (!valid) {
			$('.wpcf7-submit').removeClass('wpcf7-submit-bt');
			$('.wpcf7-submit').attr('disabled','true');
		}else{
			$('.wpcf7-submit').addClass('wpcf7-submit-bt');
			$('.wpcf7-submit').removeAttr('disabled');
		}
		
		return valid;
	} 
});
