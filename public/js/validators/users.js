$("document").ready(function () {
  /*Fichero que gestiona las validaciones cliente de los formularios de usuarios*/

  $('#name').on('blur', function() {
    var sError = '';
    sError = $(this).isValidStringNotNull('El nombre no puede superar los 255 caracteres',
      'El nombre del usuario es obligatorio', 255);

    if (sError == '')
      $.ajax({
        async: false,
        url: '/usuarios/checkNombreRepetido/' + encodeURI($(this).val()) + '/'
          + $("#idUsuario").val(),
        success: function (repetido) {
          if (repetido == "true")
            sError = 'El nombre de usuario elegido ya existe en la base de datos';
        }
      });

    $(this).validator(sError != '', sError);
  });

  $('#email').on('blur', function() {
    var sError = '';
    sError = $(this).isValidStringNotNull('El correo no puede superar los 255 caracteres',
      'El correo es obligatorio', 255);

    if (sError == '') {
      sError = ($(this).val().toUpperCase().match('^[A-Z0-9._%-]+@[A-Z0-9._-]+\\.[A-Z]{2,4}$'))
        ? '' : 'Debe indicarse una dirección de correo válida';

      if (sError == '') {
        $.ajax({
          async:false,
          url: '/usuarios/checkEmailRepetido/' + encodeURI($(this).val()) + '/'
            + $("#idUsuario").val(),
          success: function (repetido) {
            if (repetido == "true")
              sError = 'El correo indicado ya existe en la base de datos'
          }
        });
      }
    }

    $(this).validator(sError != '', sError);

  });

  $('#password').on('blur', function() {
    var sError = '';
    sError = ($(this).val() == '') ? 'La contraseña es obligatoria'
      : (($(this).val().length < 8) ? 'La contraseña debe tener al menos 8 caracteres'
      : '');

    $(this).validator(sError != '', sError);
  });

  $('#confirmPassword').on('blur', function() {
    var sError = '';
    sError = ($(this).val() == '') ? 'Debe confirmarse la contraseña'
      : (($(this).val() != $("#password").val()) ? 'La confirmación no coincide con la contraseña'
      : '');

    $(this).validator(sError != '', sError);
  });

});
