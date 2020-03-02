$("document").ready(function () {
    /* Se crea este plugin de validación para simplificar las tareas de validación cliente*/

    $.fn.activateError = function (sMessage) {
        this.addClass('is-invalid');
        this.after('<small class="field-validation-error form-text alert-danger" id="'
          + this.attr("name") + 'ErrorMsg">' + sMessage + '</small>');
    }

    $.fn.deactivateError = function () {
        this.removeClass("is-invalid");
        this.next(".field-validation-error").remove();
    };

    $.fn.hasError = function () {
        return (this.hasClass("is-invalid"));
    };

    $.fn.isValidNumber = function (sMessage, length) {
        if ((this.val() == "") || (!isNaN(this.val()) && (length == null || this.val().length <= length)))
            return ""
        return sMessage;
    };

    $.fn.isValidNumberNotNull = function (sMessage, sMessageNull, length) {
        if (!isNaN(this.val()) && (length == null || this.val().length <= length))
            return ((this.val() != "") ? "" : sMessageNull);
        return sMessage;
    };

    $.fn.isValidStringNotNull = function (sMessage, sMessageNull, length) {
        if (this.val().length > length)
            return sMessage;
        else
            return ((this.val() != "") ? "" : sMessageNull);
    };

    /*El método validator recibe una evaluación de error y el mensaje que debe mostrar en caso de que la evaluación sea cierta.
    Cuando la evaluación es cierta añade la clase de error al objeto que lo llama y le adjunta un <span> con el mensaje de error, eliminando
    spans de errores previos si los hubiera. Cuando la evaluación es falsa elimina los posibles spans de errores previos y quita la clase
    de error al objeto que lo llama*/
    $.fn.validator = function (bEvalError, sMessage) {
        if (bEvalError) {
            if (this.hasError())
                this.deactivateError();
            this.activateError(sMessage);
        } else
            this.deactivateError();
    };

});
