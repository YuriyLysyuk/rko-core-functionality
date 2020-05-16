(function ($) {
  "use strict";

  // Формат чисел для денежных значений
  const moneyFormat = wNumb({
    decimals: 0,
    thousand: " ",
    suffix: " ₽",
  });

  // Формат чисел для штучных значений
  const grandFormat = wNumb({
    decimals: 0,
    thousand: " ",
    suffix: " шт",
  });

  // Формат чисел для денежных значений без денежного знака
  const moneyFormatWOS = wNumb({
    decimals: 0,
    thousand: " ",
  });

  // Формат чисел для денежных значений с дробной частью без денежного знака
  const moneyFormatWOSFloat = wNumb({
    decimals: 2,
    mark: ",",
    thousand: " ",
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
    max: 10000000,
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
    max: 3000000,
  };

  // Настройки слайдеров noUISlider для используемых полей
  const inputSliderParams = [
    {
      ID: "income",
      range: incomeRange,
      numberFormat: moneyFormat,
    },
    {
      ID: "payment_order",
      range: {
        min: [0, 1],
        max: 100,
      },
      numberFormat: grandFormat,
    },
    {
      ID: "people_transfer",
      range: otherRange,
      numberFormat: moneyFormat,
    },
    {
      ID: "personal_transfer",
      range: otherRange,
      numberFormat: moneyFormat,
    },
    {
      ID: "get_atm",
      range: otherRange,
      numberFormat: moneyFormat,
    },
    {
      ID: "get_cashbox",
      range: otherRange,
      numberFormat: moneyFormat,
    },
    {
      ID: "put_atm",
      range: otherRange,
      numberFormat: moneyFormat,
    },
    {
      ID: "put_cashbox",
      range: otherRange,
      numberFormat: moneyFormat,
    },
  ];

  /**
   * Подготавливает URL строку с параметрами формы для ajax
   * с учетом слайдеров с параметрами
   *
   *  Подсмотрел здесь https://gomakethings.com/how-to-serialize-form-data-with-vanilla-js/
   */
  var serialize = function (form) {
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

  $(document).ready(function () {
    // Объект формы калькулятора
    const rkoCalcForm = $("#rko-calc-form");

    // let personalTransferInputSlider;

    // Обходим все заранее заданные слайдеры
    inputSliderParams.forEach(function (params) {
      let input = document.getElementById(params.ID),
        inputSlider = document.getElementById(params.ID + "-slider");

      // Создаем слайдеры с параметрами
      noUiSlider.create(inputSlider, {
        start: 0,
        connect: "lower",
        range: params.range,
        format: params.numberFormat,
      });

      // Текущий ползунок
      let inputSliderHandle = inputSlider.querySelector(".noUi-handle");
      // Текущая полоска для тапа
      let inputSliderConnects = inputSlider.querySelector(".noUi-connects");

      // При клике по полоске слайдера...
      inputSliderConnects.addEventListener("click", function () {
        // ...устанавливаем фокус на ползунок для управления с клавиатуры
        inputSliderHandle.focus();
      });

      // В начале перемещения ползунка слайдера мышкой или пальцем...
      inputSlider.noUiSlider.on("start", function () {
        // ...устанавливаем фокус на ползунок для управления с клавиатуры.
        inputSliderHandle.focus();
      });

      // При движении ползунка слайдера...
      inputSlider.noUiSlider.on("update", function (values, handle) {
        let value = values[handle];
        // ...обновляем его значение в поле для наглядности
        input.value = value;
      });

      // При изменении значения слайдера (когда опускаем ползунок)...
      inputSlider.noUiSlider.on("change", function (values, handle, unencoded) {
        let value = values[handle];

        // ...обновляем его значение в поле...
        input.value = value;
        // ...в атрибут data-value сохраняем неотформатированное значение...
        input.dataset.value = Math.round(unencoded[handle]);
        // ...и отправляем форму.
        rkoCalcForm.submit();
      });

      // При попадании фокуса на инпут со сладером...
      input.addEventListener(
        "focus",
        function (event) {
          // ...приводим значение поля к неотформатированному варианту...
          event.target.value = event.target.dataset.value;

          // ...и выделяем все значение для быстрого ввода нового.
          // setTimeout и setSelectionRange нужен для нормальной работы выделения в Safari
          // и чтобы не появлялось контекстное меню на мобилках в Chrome
          setTimeout(function () {
            event.target.setSelectionRange(0, event.target.value.length);
          }, 1);
        },
        true
      );

      // При изменении значения инпута со слайдером...
      input.addEventListener("change", function (event) {
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
        function (event) {
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
      input.addEventListener("keypress", function (event) {
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

    // Обертка калькулятора
    const rkoCalcWrap = document.querySelector(".rko-calc");
    // Коллекция переключателя ИП и ООО
    const inputsSwitchIPOOO = document.querySelectorAll('input[name="ooo"]');
    // Лейбл переключателя ИП и ООО
    const labelPersonalTransfer = document.querySelector(
      'label[for="personal_transfer"]'
    );
    // Инпут переключателя ИП и ООО
    const inputPersonalTransfer = document.querySelector(
      'input[name="personal_transfer"]'
    );
    // Слайдер переводов на свою карту
    const personalTransferInputSlider = document.querySelector(
      "#personal_transfer-slider"
    );

    // Для каждой радиокнопки переключателя ИП и ООО...
    inputsSwitchIPOOO.forEach(function (inputSwich) {
      // ...при изменении значения радиокнопки...
      inputSwich.addEventListener("change", function () {
        // ...выбираем одно из значений:
        switch (inputSwich.value) {
          // Если выбран ИП...
          case "0":
            // ...добавляем зеленый фон для обертки калькулятора...
            rkoCalcWrap.classList.add("color-bg-green");
            // ...и добавляем поле для ввода суммы переводов на свою карту...
            labelPersonalTransfer.classList.remove("dn");
            break;
          // Если выбрвн ООО...
          case "1":
            // ...убираем зеленый фон для обертки калькулятора...
            rkoCalcWrap.classList.remove("color-bg-green");
            // ...убираем поле для ввода суммы переводов на свою карту...
            labelPersonalTransfer.classList.add("dn");
            // ...обнуляем неотформатированное значение поля переводов на свою карту...
            inputPersonalTransfer.dataset.value = 0;
            // ...обнуляем отформатированное значение поля переводов на свою карту...
            inputPersonalTransfer.value = moneyFormat.to(0);
            // ...обнуляем слайдер поля переводов на свою карту.
            personalTransferInputSlider.noUiSlider.setHandle(0, 0, null);
            break;
        }
      });
    });

    // Кнопка дополнительных параметров
    const detailedCalculationButton = document.querySelector(
      ".detailed-calculation"
    );
    // Контейнер кнопки дополнительных параметров
    const detailedCalculation = document.querySelector(".detailed-calculation");
    // Скрытые поля дополнительных параметров
    const detailedHiddens = document.querySelectorAll(".detailed-hidden");

    // При каждом клике по кнопке дополнительных параметров...
    detailedCalculationButton.addEventListener("click", function () {
      // ...переключать состояние контейнера кнопки...
      detailedCalculation.classList.toggle("active");
      // ...и переключать видимость скрытых полей дополнительных параметров.
      detailedHiddens.forEach(function (detailedHidden) {
        detailedHidden.classList.toggle("active");
      });
    });

    // Шаблон для вывода диапазона условий
    function tariffCondTemplate(cond) {
      return `
        <li>
          ${
            // Если поле «от» не нулевое...
            cond.from
              ? // ...выводим его
                "от " + moneyFormatWOSFloat.to(cond.from) + " ₽ "
              : // ...поле «от» равно 0. Если поле «до» также нулевое...
              !cond.to
              ? // ...выводим текст «любая сумма»
                "любая сумма "
              : // ...иначе ничего не выводим
                ""
          }
          ${
            // Если поле «до» не нулевое — выводим его, иначе ничего не выводим
            cond.to ? "до " + moneyFormatWOS.to(cond.to) + " ₽ " : ""
          }
          ${
            // Если поле «цена» не нулевое...
            cond.cost
              ? // ...выводим его
                "— " + cond.cost + "%"
              : // ...поле «цена» нулевое. Если поле «плюс» не нулевое
              cond.plus
              ? // ...подготавливаем место для вывода поля «плюс»
                "— "
              : // ...иначе выводим «бесплатно»
                "— бесплатно"
          }
          ${
            // Если «цена» не нулевая и есть «плюс», добавляем перед ним знак +
            cond.cost && cond.plus ? "+ " : ""
          }
          ${
            // Если есть поле «плюс» — выводим его
            cond.plus ? cond.plus + " ₽" : ""
          }
          ${
            // Если есть поле «минимум», выводим его
            cond.min_cost ? ", мин. " + cond.min_cost + " ₽" : ""
          }
        </li>
      `;
    }

    // Формируем верстку с детальной расшифровкой результата по тарифу
    function tariffDetails(tariffCalculated, tariffOptions, userParams) {
      // Объект с именованием полей (в данной части неизменяемых динамически)
      let paramsName = {
        corp_card: "Корпоративная карта",
        opening_cost: "Открытие счета",
        service: "Обслуживание счета",
        sms: "SMS-информирование",
      };

      // html-вывод
      let html = "";

      // Индикатор первого выводимого параметра в цикле
      let isFirstParamOutput = true;

      // Для каждого параметра в результатах вычислений
      for (let param in tariffCalculated) {
        // Если обрабатываются переводы на личную карту и расчет для ООО, перейти к следующему параметру
        if (param == "personal_transfer" && userParams.ooo) continue;

        // Если параметр (из списка) не задан пользователем, перейти к следующему параметру
        if (
          (param == "income" ||
            param == "people_transfer" ||
            param == "personal_transfer" ||
            param == "get_atm" ||
            param == "get_cashbox" ||
            param == "put_atm" ||
            param == "put_cashbox" ||
            param == "payment_order" ||
            param == "corp_card" ||
            param == "sms") &&
          !userParams[param]
        )
          continue;

        // Если это первый выводимый параметр...
        isFirstParamOutput
          ? // ...переключаем индикатор в false
            (isFirstParamOutput = false)
          : // ...это не первый вывод — выводим раделитель
            (html += "<hr class='result-details-sep'>");

        // Добавляем в объект с именованием полей динамические поля
        paramsName.personal_transfer = `Перевод <span class="user-value">${moneyFormatWOS.to(
          userParams[param]
        )} ₽</span> на личную карту в ${tariffOptions.bank.name.gde}`;
        paramsName.income = `Поступление <span class="user-value">${moneyFormatWOS.to(
          userParams[param]
        )} ₽</span> на счет от юр. лиц и ИП`;
        paramsName.people_transfer = `Перевод <span class="user-value">${moneyFormatWOS.to(
          userParams[param]
        )} ₽</span> физ. лицам`;
        paramsName.get_atm = `Снятие <span class="user-value">${moneyFormatWOS.to(
          userParams[param]
        )} ₽</span> в банкомате`;
        paramsName.get_cashbox = `Снятие <span class="user-value">${moneyFormatWOS.to(
          userParams[param]
        )} ₽</span> в кассе банка`;
        paramsName.put_atm = `Внесение <span class="user-value">${moneyFormatWOS.to(
          userParams[param]
        )} ₽</span> в банкомате`;
        paramsName.put_cashbox = `Внесение <span class="user-value">${moneyFormatWOS.to(
          userParams[param]
        )} ₽</span> в кассе банка`;
        paramsName.payment_order = `Платежные поручения <span class="user-value">(${moneyFormatWOS.to(
          userParams[param]
        )} шт)</span>`;

        // Формируем верстку заголовка параметра с вычисленной стоимостью
        html += `
          <div class="result-details-param">
            ${paramsName[param]} — <span class="price">
            ${
              // Если результат вычисления 0...
              !tariffCalculated[param]
                ? // ...выводим слово «бесплатно»
                  "бесплатно"
                : // ...результат не нулевой. Если обрабатывается параметр стоимости открытия...
                param == "opening_cost"
                ? // ...выводим стоимость с ₽ в конце
                  moneyFormatWOS.to(tariffCalculated[param]) +
                  " <span> ₽</span>"
                : // ...иначе выводим стоимость с ₽/мес в конце
                  moneyFormatWOS.to(tariffCalculated[param]) +
                  " <span> ₽/мес</span>"
            }
            </span>
            </div>
            `;

        // Формируем верску условий тарифа в зависимости от обрабатываемого параметра
        // tariffCondTemplate() используется для вывода диапазона условий
        switch (param) {
          case "people_transfer":
          case "get_atm":
            html += `
              <div class="result-details-notes-title">Условия тарифа:</div>
              <div class="result-details-notes-body">
                <ul>
                ${
                  // Если расчет был для ИП...
                  !userParams.ooo
                    ? // ...вывести условия для ИП
                      tariffOptions[param].cond.map(tariffCondTemplate).join("")
                    : // Расчет для ООО. Если тарифы для ООО такие же как для ИП...
                    tariffOptions[param].same_for_ooo
                    ? // ...выводим условия для ИП
                      tariffOptions[param].cond.map(tariffCondTemplate).join("")
                    : // ...для ООО заданы отдельные тарифы — выводим их
                      tariffOptions[param].cond_ooo
                        .map(tariffCondTemplate)
                        .join("")
                }
                </ul>
              </div>
            `;
            break;

          case "personal_transfer":
            html += `
              <div class="result-details-notes-title">Условия тарифа:</div>
              <div class="result-details-notes-body">
                <ul>
                ${
                  // Если условия переводов на свою карту такие же как условия переводов физ. лицам...
                  tariffOptions.personal_transfer_same_as_people_transfer
                    ? // ...выводим условия переводов физ. лицам
                      tariffOptions["people_transfer"].cond
                        .map(tariffCondTemplate)
                        .join("")
                    : // ...условия переводов на свою карту заданы отдельно. Выводим их
                      tariffOptions[param].cond.map(tariffCondTemplate).join("")
                }
                </ul>
              </div>
            `;
            break;

          case "get_cashbox":
          case "put_cashbox":
            html += `
              <div class="result-details-notes-title">Условия тарифа:</div>
              <div class="result-details-notes-body">
                <ul>
                ${
                  // Если в банке есть касса...
                  tariffOptions.have_cashbox
                    ? // ...выводим условия параметра в кассе банка
                      tariffOptions[param].cond.map(tariffCondTemplate).join("")
                    : // ...кассы в банке нет. Выводим соответствующее сообщение
                      "<li>В банке нет физических отделений с кассой, расчет выполнен по условиям операции через банкомат</li>"
                }
                </ul>
              </div>
            `;
            break;

          case "income":
            // Если поступления на счет не бесплатные — выводим условия тарифа
            if (tariffCalculated[param]) {
              html += `
                <div class="result-details-notes-title">Условия тарифа:</div>
                <div class="result-details-notes-body">
                  <ul>
                  ${tariffOptions[param].cond.map(tariffCondTemplate).join("")}
                  </ul>
                </div>
              `;
            }
            break;

          case "put_atm":
            html += `
                  <div class="result-details-notes-title">Условия тарифа:</div>
                  <div class="result-details-notes-body">
                    <ul>
                    ${tariffOptions[param].cond
                      .map(tariffCondTemplate)
                      .join("")}
                    </ul>
                  </div>
                `;
            break;

          case "service":
            // Если обсуживание счета не бесплатное — выводим условия тарифа
            if (tariffCalculated[param]) {
              html += `
                <div class="result-details-notes-title">Условия тарифа:</div>
                <div class="result-details-notes-body">
                  <ul>
                  ${tariffOptions[param].cond
                    .map(
                      (cond) =>
                        `<li>${cond.period} мес — ${moneyFormatWOS.to(
                          cond.cost
                        )} ₽</li>`
                    )
                    .join("")}
                  </ul>
                </div>
              `;
            }

            break;

          case "payment_order":
            html += `
                <div class="result-details-notes-title">Условия тарифа:</div>
                <div class="result-details-notes-body">
                  <ul>
                    ${
                      // Если в тариф включены бесплатные платежки...
                      tariffOptions[param].free
                        ? // ...выводим их и стоимость платных
                          "<li>до " +
                          tariffOptions[param].free +
                          " шт — бесплатно</li><li>остальные — " +
                          tariffOptions[param].paid +
                          " ₽/шт</li>"
                        : // ...беслпатных платежек нет. Если задана стоимость платных...
                        tariffOptions[param].paid
                        ? // ...выводим стоимость платежки
                          "<li>" + tariffOptions[param].paid + " ₽/шт</li>"
                        : // ...иначе все платежки бесплатные
                          "<li>любое количество — бесплатно</li>"
                    }
                  </ul>
                </div>
              `;

            break;

          case "corp_card":
            // Если для корпоративной карты заполнены поля кэшбэка и бесплатного периода — выводим их
            if (
              tariffOptions[param].cachback ||
              tariffOptions[param].free_period
            ) {
              html += `
              <div class="result-details-notes-title">Бонусы:</div>
              <div class="result-details-notes-body">
                <ul>
                  ${
                    tariffOptions[param].free_period
                      ? "<li>бесплатный период — " +
                        tariffOptions[param].free_period +
                        "</li>"
                      : ""
                  }
                  ${
                    tariffOptions[param].cachback
                      ? "<li>кэшбэк — " +
                        tariffOptions[param].cachback +
                        "</li>"
                      : ""
                  }
                </ul>
              </div>
            `;
            }

            break;
        }

        // ToDO добавить вывод дополнительных сообщений: Мелким шрифтом, Обратите внимание, Как сэкономить
      }

      return html;
    }

    // Шаблон формирования html с результатом по отдельному тарифу
    function tariffTemplate(rkoCalcResult, index) {
      // Получаем пользовательские данные, используемые в расчетах
      let userParams = rkoCalcResult.user_params;
      // Получаем параметры текущего тарифа
      let tariffOptions = rkoCalc.allTariffOptions[rkoCalcResult.id];

      // Возвращаем html с версткой по отдельному тарифу
      // tariffDetails() возвращает верстку с детальной расшифровкой результата
      return `
        <li class="result-wrap">
          <div class="result-position">${index + 1}</div>
          <div class="result-bank">
            <img src="/biglogo_326.svg" class="result-bank-logo">
            <div class="result-bаnk-license">лиц. № 1234</div>
          </div>
          <div class="result-tariff">
            <div class="result-tariff-label">Тариф</div>
            <div class="result-tariff-name">${tariffOptions.name}</div>
          </div>
          <div class="result-calculated">
            <div class="result-calculated-sum price">${moneyFormatWOS.to(
              rkoCalcResult.calculated_sum
            )} <span>₽/мес</span></div>
            <div class="result-calculated-details">
              <span>Детально</span><span> <svg class="open" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m11.9932649 19.500812c-.6352342.000819-1.2400333-.2833911-1.65984-.78l-9.41856003-11.15199999c-.61913211-.76859096-.53599584-1.91147321.1872475-2.57410938.72324334-.66263618 1.82204752-.60264878 2.4748325.13510938l8.23584003 9.75199999c.0455704.0541548.1113462.0852092.18048.0852092.0691337 0 .1349096-.0310544.18048-.0852092l8.23584-9.75199999c.4135143-.51333692 1.0615094-.75310007 1.6932379-.626511s1.1480791.59966995 1.3492454 1.2361798c.2011663.63650986.0555875 1.33658731-.3804033 1.8293312l-9.41568 11.14799999c-.4205256.497811-1.0261445.7833703-1.66272.784z" fill-rule="evenodd"/></svg><svg class="close" enable-background="new 0 0 24 24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 4.5c.6 0 1.2.3 1.7.8l9.4 11.2c.6.8.5 1.9-.2 2.6s-1.8.6-2.5-.1l-8.2-9.8c0-.2-.1-.2-.2-.2s-.1 0-.2.1l-8.2 9.8c-.4.5-1.1.8-1.7.6-.6-.1-1.1-.6-1.3-1.2s-.1-1.3.4-1.8l9.4-11.1c.4-.6 1-.9 1.6-.9z"/></svg></span>
            </div>
          </div>
          <div class="result-button-wrap">
            <a href="${
              tariffOptions.button.url
            }" target="_blank" onclick="${tariffOptions.button.onclick.calc_list}" class="result-button button button-small button-outline">Перейти на сайт</a>
          </div>
          <div class="result-details-wrap">
            ${tariffDetails(
              rkoCalcResult.calculated,
              tariffOptions,
              userParams
            )}
          </div>
        </li>
      `;
    }

    // Функция для вывода результатов работы калькулятора
    // Для каждого тарифа вызывает шаблон формирования html с результатом
    function rkoCalcResultsTemplate(rkoCalcResults) {
      document.getElementById("rko-calc-results").innerHTML = `
        <h2>Личный Топ ${rkoCalcResults.length} тарифов РКО</h2>
        <ul class="rko-calc-results-list">
          ${rkoCalcResults.map(tariffTemplate).join("")}
        </ul>
      `;

      // Собираем все кнопки детальных сведений с расшифровкой суммы
      const resultDetailsButtons = document.querySelectorAll(
        ".result-calculated-details"
      );
      // Собираем все скрытае области детальных сведений
      const resultDetailsWraps = document.querySelectorAll(
        ".result-details-wrap"
      );

      // Для каждой кнопки детальных сведений...
      resultDetailsButtons.forEach(function (resultDetailsButton, index) {
        // ...при клике на кнопку...
        resultDetailsButton.addEventListener("click", function () {
          // ...переключать состояние контейнера кнопки...
          resultDetailsButton.classList.toggle("active");
          // ...и переключать видимость скрытой области детальных сведений
          resultDetailsWraps[index].classList.toggle("active");
        });
      });
    }

    // Функция для получения результатор работы калькулятора через ajax-запрос
    function rkoCalcFormAjax() {
      // Подготавливаем сериализованную строку с помощью собственной функции
      let rkoCalcFormData = serialize(rkoCalcForm[0]);
      // ToDO: добавить отображение загрузки результатов, подумать какой вид loading использовать
      console.log(rkoCalc.allTariffOptions);

      $.ajax({
        type: "GET",
        // Подготавливаем url для запроса к REST API
        url: rkoCalc.restURL + "rko-calc/v1/calculate?" + rkoCalcFormData,
        // Передаем в запрос сериализованные поля формы
        data: rkoCalcFormData,

        // При успешном запросе...
        success: function (rkoCalcResults) {
          console.log(rkoCalcResults);
          // ...вывести результаты на экран
          rkoCalcResultsTemplate(rkoCalcResults);
        },
      });
    }

    // Запускаем ajax-запрос для получения результатов работы калькулятора при загрузке страницы
    rkoCalcFormAjax();

    // При изменении состояния инпутов в форме, отправляем ее на сервер (для ползунков слайдера отдельное событие)
    rkoCalcForm.change(function () {
      rkoCalcForm.submit();
    });

    // При отправке формы...
    rkoCalcForm.on("submit", function (e) {
      // ...отключаем события браузера по умолчанию...
      e.preventDefault();
      // ...и отправляем ajax-запрос для получения результатов работы калькулятор
      rkoCalcFormAjax();
    });
  });
})(jQuery);
