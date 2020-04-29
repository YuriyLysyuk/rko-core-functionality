// ToDo Стилизация, скрытие и раскрытие Подробного поиска
// ToDo Стилизация формы под разные разрешения

(function($) {
  "use strict";

  // Формат чисел для денежных значений
  const moneyFormat = wNumb({
    decimals: 0,
    thousand: " ",
    suffix: " ₽"
  });

  // Формат чисел для штучных значений
  const grandFormat = wNumb({
    decimals: 0,
    thousand: " ",
    suffix: " шт."
  });

  // Диапазон значений и шаг для входящих денег на счет
  const incomeRange = {
    min: [0, 100],
    "10%": [20000, 500],
    "20%": [50000, 1000],
    "30%": [100000, 2500],
    "40%": [150000, 5000],
    "50%": [250000, 10000],
    "60%": [500000, 25000],
    "70%": [1000000, 100000],
    "85%": [2500000, 250000],
    max: 10000000
  };

  // Диапазон значений и шаг для всех других денежных полей
  const otherRange = {
    min: [0, 100],
    "10%": [20000, 500],
    "20%": [50000, 1000],
    "30%": [100000, 2500],
    "40%": [150000, 5000],
    "50%": [250000, 10000],
    "60%": [500000, 25000],
    "70%": [1000000, 50000],
    "85%": [2000000, 100000],
    max: 3000000
  };

  // Настройки слайдеров noUISlider для используемых полей
  const inputSliderParams = [
    {
      ID: "income",
      range: incomeRange,
      numberFormat: moneyFormat
    },
    {
      ID: "payment_order",
      range: {
        min: [0, 1],
        max: 100
      },
      numberFormat: grandFormat
    },
    {
      ID: "people_transfer",
      range: otherRange,
      numberFormat: moneyFormat
    },
    {
      ID: "personal_transfer",
      range: otherRange,
      numberFormat: moneyFormat
    },
    {
      ID: "get_atm",
      range: otherRange,
      numberFormat: moneyFormat
    },
    {
      ID: "get_cashbox",
      range: otherRange,
      numberFormat: moneyFormat
    },
    {
      ID: "put_atm",
      range: otherRange,
      numberFormat: moneyFormat
    },
    {
      ID: "put_cashbox",
      range: otherRange,
      numberFormat: moneyFormat
    }
  ];

  /**
   * Подготавливает URL строку с параметрами формы для ajax
   *
   *  Подсмотрел здесь https://gomakethings.com/how-to-serialize-form-data-with-vanilla-js/
   */
  var serialize = function(form) {
    // Setup our serialized data
    var serialized = [];

    // Loop through each field in the form
    for (var i = 0; i < form.elements.length; i++) {
      var field = form.elements[i];

      // Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
      if (
        !field.name ||
        field.disabled ||
        field.type === "file" ||
        field.type === "reset" ||
        field.type === "submit" ||
        field.type === "button"
      )
        continue;

      // Convert field data to a query string
      if (
        (field.type !== "checkbox" && field.type !== "radio") ||
        field.checked
      ) {
        // В инпутах слайдеров есть атрибут data-value с неотформатированным значением. Если он присутствует, используем его.
        if (field.dataset.value) {
          serialized.push(
            encodeURIComponent(field.name) +
              "=" +
              encodeURIComponent(field.dataset.value)
          );
        } else {
          serialized.push(
            encodeURIComponent(field.name) +
              "=" +
              encodeURIComponent(field.value)
          );
        }
      }
    }

    return serialized.join("&");
  };

  /* function results(val) {
    $("#results").empty();
    $("#results").append('<div class="post-title">' + val.title + "</div>");
    $("#results").append('<div class="post-content">' + val.content + "</div>");
  } */

  $(document).ready(function() {
    // Объект формы калькулятора
    const rkoCalcForm = $("#rko-calc-form");
    // const rkoCalcForm = $("#rko-calc-form");

    // Обходим все заранее заданные слайдеры
    inputSliderParams.forEach(function(params) {
      let input = document.getElementById(params.ID),
        inputSlider = document.getElementById(params.ID + "-slider");

      // Создаем слайдеры с параметрами
      noUiSlider.create(inputSlider, {
        start: 0,
        connect: "lower",
        range: params.range,
        format: params.numberFormat
      });

      // Текущий ползунок
      let inputSliderHandle = inputSlider.querySelector(".noUi-handle");
      // Текущая полоска для тапа
      let inputSliderConnects = inputSlider.querySelector(".noUi-connects");

      // При клике по полоске слайдера...
      inputSliderConnects.addEventListener("click", function() {
        // ...устанавливаем фокус на ползунок для управления с клавиатуры
        inputSliderHandle.focus();
      });

      // При движении ползунка слайдера...
      inputSlider.noUiSlider.on("update", function(values, handle) {
        let value = values[handle];
        // ...обновляем его значение в поле для наглядности
        input.value = value;
      });

      // При изменении значения слайдера (когда опускаем ползунок)...
      inputSlider.noUiSlider.on("change", function(values, handle, unencoded) {
        let value = values[handle];

        // ...обновляем его значение в поле...
        input.value = value;
        // ...в атрибут data-value сохраняем неотформатированное значение...
        input.dataset.value = Math.round(unencoded[handle]);
        // ...и отправляем форму.
        rkoCalcForm.submit();

        // Устанавливаем фокус на ползунок для управления с клавиатуры
        inputSliderHandle.focus();
      });

      // При попадании фокуса на инпут со сладером...
      input.addEventListener(
        "focus",
        function(event) {

          // ...приводим значение поля к неотформатированному варианту...
          event.target.value = event.target.dataset.value;

          // ...и выделяем все значение для быстрого ввода нового.
          // setTimeout и setSelectionRange нужен для нормальной работы выделения в Safari
          // и чтобы не появлялось контекстное меню на мобилках в Chrome
          setTimeout(function() {
            event.target.setSelectionRange(0, event.target.value.length);
          }, 1);
        },
        true
      );

      // При изменении значения инпута со слайдером...
      input.addEventListener("change", function(event) {

        // ...записываем в data-value предварительно очищенное от нечисловых символов значение...
        event.target.dataset.value = Number(
          event.target.value.replace(/[^\d]/g, "")
        );

        // ...переносим ползунок слайдера на место, соответстующее введенному значению, без вызова события change для слайдера...
        inputSlider.noUiSlider.setHandle(0, event.target.dataset.value, null);

        // ...записываем в value отформатированное значение...
        event.target.value = params.numberFormat.to(
          Number(event.target.dataset.value)
        );

        // ...устанавливаем вспомогательный флаг для blur-события, что значение в инпуте было изменено.
        event.target.hasChanged = true;
      });

      // При потере фокуса инпутом слайдера...
      input.addEventListener(
        "blur",
        function(event) {

          // ...если значение инпута было изменено...
          if (event.target.hasChanged) {

            // ...отключаем флаг изменений, форма отправляется с помощью отдельного события.
            event.target.hasChanged = false;

            // Если значение инпута изменено не было...
          } else {
            // ...форматируем значение в нужный формат.
            event.target.value = params.numberFormat.to(
              Number(event.target.dataset.value)
            );
          }
        },
        true
      );

      // При каждом вводе символа в инпут слайдера...
      input.addEventListener("keypress", function(event) {
        let e = event || window.event,
          key = e.keyCode || e.which;

        // ...получаем строчное значение кода введенного символа
        key = String.fromCharCode(key);

        // ...объявляем регулярку для чисел...
        let regex = /[0-9]|\./;

        // ...если введенный символ не число...
        if (!regex.test(key)) {
          // ...прекращаем его вывод...
          e.returnValue = false;
          if (e.preventDefault) e.preventDefault();
        }
      });
    });

    // При изменении состояния инпутов в форме, отправляем ее на сервер (для ползунков слайдера отдельное событие)
    rkoCalcForm.change(function(){
      rkoCalcForm.submit();
    });

    rkoCalcForm.on("submit", function(e) {
      e.preventDefault();
      // serialize() не обрабатывает поля без name, значит служебные переключатели можно исключить из URL
      let rkoCalcFormData = serialize(rkoCalcForm[0]);
      // console.clear();
      // console.log(rkoCalcFormData);
      console.log(
        "REST URL: " +
          rkoCalc.restURL +
          "rko-calc/v1/calculate?" +
          rkoCalcFormData
      );
      // console.log(rkoCalc.allTariffOptions);

      $.ajax({
        type: "GET",
        url: rkoCalc.restURL + "rko-calc/v1/calculate?" + rkoCalcFormData,
        data: rkoCalcFormData,

        success: function(post) {
          // console.log(post);
        }
      });
    });
  });
})(jQuery);
