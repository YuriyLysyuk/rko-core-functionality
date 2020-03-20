(function($) {
  "use strict";

  let rkoCalcForm = $("#rko-calc-form");
  rkoCalcForm.on("submit", function(e) {
    e.preventDefault();

    // serialize() не обрабатывает поля без name, значит служебные переключатели можно исключить из URL
    let rkoCalcFormData = rkoCalcForm.serialize();
    console.clear();
    console.log(rkoCalcFormData);
    console.log(
      "REST URL: " +
        rkoCalc.restURL +
        "rko-calc/v1/calculate?" +
        rkoCalcFormData
    );
    console.log(rkoCalc.allTariffOptions);

    $.ajax({
      type: "GET",
      url: rkoCalc.restURL + "rko-calc/v1/calculate?" + rkoCalcFormData,
      data: rkoCalcFormData,

      success: function(post) {
        console.log(post);
      }
    });
  });

  /* function results(val) {
    $("#results").empty();
    $("#results").append('<div class="post-title">' + val.title + "</div>");
    $("#results").append('<div class="post-content">' + val.content + "</div>");
  } */

  $(document).ready(function() {});
})(jQuery);
