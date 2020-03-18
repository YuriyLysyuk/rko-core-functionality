(function($) {
  "use strict";

  $.ajax({
    type: "GET",
    url: rkoCalc.restURL + "rko-calc/v1/calculate",

    success: function(post) {
      console.clear;
      console.log(post);
    }
  });

  let rkoCalcForm = $("#rko-calc-form");
  rkoCalcForm.on("submit", function(e) {
    e.preventDefault();

    $.ajax({
      type: "GET",
      url: rkoCalc.restURL + "rko-calc/v1/calculate",
      data: rkoCalcForm.serialize(),

      success: function(post) {
        console.clear;
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
