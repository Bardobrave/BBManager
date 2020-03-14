$("document").ready(function() {

  //Evento de control para vaciar formularios
  $("span.reset").on("click", function() {
    var parentForm = $(this).closest("form");

    //Se vacían todos los inputs
    parentForm.find('input[name!="_token"]').each(function() {
      $(this).val("");
    });

    //Se vacían todos los selects
    parentForm.find("select").each(function() {
      $(this).val("");
    });

  });

});
