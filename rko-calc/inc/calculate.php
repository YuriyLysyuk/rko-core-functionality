<?php
/**
 * Функции для вычисления стоимости использования тарифа
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.2.0
 **/

/**
 *  Вычисляем стоимость обслуживания счета
 *	*	в месяц
 */
function calculate_service_cost($tariffOptions)
{
  $calculatedServiceCost = 0;

  $calculatedServiceCost = $tariffOptions['service']['cond']['0']['cost'];

  return $calculatedServiceCost;
}

/**
 *  Вспомогательная функция для функции вычисления комиссии с диапазоном условий.
 *  Возвращает условия по тарифу исходя из контекта. Меняет контекст для получения
 *  условий, если они одинаковые.
 *
 */
function get_tariff_options_context_cond($userParams, $tariffOptions, $context)
{
  if ($userParams['ooo']) {
    // Если расчет нужен для ООО и для ООО заданы отдельные условия
    if (
      isset($tariffOptions[$context]['same_for_ooo']) &&
      !$tariffOptions[$context]['same_for_ooo']
    ) {
      // Получаем условия по тарифу для ООО
      $tariffOptionsContextCond = $tariffOptions[$context]['cond_ooo'];
    } else {
      if (
        // Если расчет для получения наличных в кассе банка, а кассы в банке нет
        $context === 'get_cashbox' &&
        isset($tariffOptions['have_cashbox']) &&
        !$tariffOptions['have_cashbox']
      ) {
        // Проверяем, заданы ли для получения наличных через банкомат отдельные условия для ООО...
        if (
          isset($tariffOptions['get_atm']['same_for_ooo']) &&
          !$tariffOptions['get_atm']['same_for_ooo']
        ) {
          // ...используем условия получения наличных через банкомат для ООО
          $tariffOptionsContextCond = $tariffOptions['get_atm']['cond_ooo'];
        } else {
          // ...используем условия получения наличных через банкомат для ИП
          $tariffOptionsContextCond = $tariffOptions['get_atm']['cond'];
        }
      } elseif (
        // Если расчет для внесения наличных в кассе банка, а кассы в банке нет...
        $context === 'put_cashbox' &&
        isset($tariffOptions['have_cashbox']) &&
        !$tariffOptions['have_cashbox']
      ) {
        // ...используем условия внесения наличных через банкомат
        $tariffOptionsContextCond = $tariffOptions['put_atm']['cond'];
      } else {
        // Получаем условия по тарифу для ИП, которые такие же как и для ООО
        $tariffOptionsContextCond = $tariffOptions[$context]['cond'];
      }
    }
  } else {
    // Если расчет для ИП, мы обсчитываем стоимость перевода на личный счет и условия переводов на личный счет такие же как для переводов на счета других физ. лиц
    if (
      $context === 'personal_transfer' &&
      isset($tariffOptions['personal_transfer_same_as_people_transfer']) &&
      $tariffOptions['personal_transfer_same_as_people_transfer']
    ) {
      // Получаем условия переводов на счета других физ. лиц
      $tariffOptionsContextCond = $tariffOptions['people_transfer']['cond'];
    } elseif (
      // Если расчет для получения наличных в кассе банка, а кассы в банке нет...
      $context === 'get_cashbox' &&
      isset($tariffOptions['have_cashbox']) &&
      !$tariffOptions['have_cashbox']
    ) {
      // ...используем условия получения наличных через банкомат
      $tariffOptionsContextCond = $tariffOptions['get_atm']['cond'];
    } elseif (
      // Если расчет для внесения наличных в кассе банка, а кассы в банке нет...
      $context === 'put_cashbox' &&
      isset($tariffOptions['have_cashbox']) &&
      !$tariffOptions['have_cashbox']
    ) {
      // ...используем условия внесения наличных через банкомат
      $tariffOptionsContextCond = $tariffOptions['put_atm']['cond'];
    } else {
      // Иначе получаем отдельно заданные условия
      $tariffOptionsContextCond = $tariffOptions[$context]['cond'];
    }
  }

  return $tariffOptionsContextCond;
}

/**
 *  Функция для вычисления комиссий с диапазоном условий
 *  * в месяц
 */
function calculate_range_fee($userParams, $tariffOptions, $context = '')
{
  if (empty($context) || empty($userParams[$context])) {
    return 0;
  }

  // Общая комиссия
  $calculatedFee = 0;
  // Текущий диапазон
  $range = 0;
  // Объем переводов, которые ввел пользователь
  $userValue = $userParams[$context];

  // Получаем условия по тарифу, при необходимости меняя контекст (если условия по тарифам одинаковые)
  $tariffOptionsContextCond = get_tariff_options_context_cond(
    $userParams,
    $tariffOptions,
    $context
  );

  if ($tariffOptionsContextCond) {
    foreach ($tariffOptionsContextCond as $cond) {
      // Комиссия указана в %, переводим % в число
      $condCostPercentToNumber = $cond['cost'] / 100;
      // Минимальная комиссия
      // Убрал учет минимальной комиссии, потому что переводы идут не одним платежом, и расчет для общего объема переводов не актуален
      // $minFee = $cond['min_cost'];
      // Вычисленная комиссия на текущей итерации
      $currentFee = 0;
      $range = round($cond['to'] - $cond['from']);

      // Если указано от 0 до 0, значит ограничения объема переводов нет
      // или
      // Если указано от n до 0, значит это конец диапазона
      if (
        ($cond['from'] == 0 && $cond['to'] == 0) ||
        ($cond['from'] > 0 && $cond['to'] == 0)
      ) {
        $currentFee = $userValue * $condCostPercentToNumber;
        if ($currentFee) {
          // if ($currentFee > $minFee) {
          $calculatedFee += $currentFee;
          // } else {
          // $calculatedFee += $minFee;
          // }
        }

        $userValue = 0;

        // Если указано от 0 до n, значит это начало диапазона
        // или
        // Если указано от n до m, значит это промежуточный диапазон
      } elseif (
        ($cond['from'] == 0 && $cond['to'] > 0) ||
        ($cond['from'] > 0 && $cond['to'] > 0)
      ) {
        if ($userValue > $range) {
          $calculatedFee += $range * $condCostPercentToNumber;
          $userValue -= $range;
        } else {
          $currentFee = $userValue * $condCostPercentToNumber;
          if ($currentFee) {
            // if ($currentFee > $minFee) {
            $calculatedFee += $currentFee;
            // } else {
            // $calculatedFee += $minFee;
            // }
          }

          $userValue = 0;
        }
      }
    }
  }

  return $calculatedFee;
}

/**
 *  Функция для вычисления комиссии за платежные поручения
 *  * в месяц
 */
function calculate_payment_order_cost($userParams, $tariffOptions)
{
  if (empty($userParams['payment_order'])) {
    return 0;
  }

  $calculatedPaymentOrderCost = 0;
  // Получаем количество платежных поручений на счета юр. лиц и ИП, которые ввел пользователь
  $userPaymentOrder = $userParams['payment_order'];
  // Количество бесплатных платежных поручений
  $paymentOrderFreeCount = $tariffOptions['payment_order']['free'];
  // Стоимость платных платежных поручений
  $paymentOrderPaidCost = $tariffOptions['payment_order']['paid'];

  if ($userPaymentOrder - $paymentOrderFreeCount > 0) {
    $calculatedPaymentOrderCost =
      ($userPaymentOrder - $paymentOrderFreeCount) * $paymentOrderPaidCost;
  } else {
    $calculatedPaymentOrderCost = 0;
  }

  return $calculatedPaymentOrderCost;
}

/**
 *  Функция для вычисления комиссий по опциям, которые либо включены либо нет
 *  * в месяц
 */
function calculate_boolean_fee($userParams, $tariffOptions, $context = '')
{
  if (empty($context) || empty($userParams[$context])) {
    return 0;
  }

  // Общая комиссия
  $calculatedFee = 0;
  // Получаем значение, которые ввел пользователь. В данной функции это либо true либо false
  $userValue = $userParams[$context];

  if ($userValue) {
    $calculatedFee = $tariffOptions[$context]['cost'];
  }

  return $calculatedFee;
}

/**
 *
 *
 */
function calculate($userParams = false, $tariffOptions = false)
{
  if (!$userParams || !$tariffOptions) {
    return;
  }

  $calculated = [];

  // Стоимость открытия счета
  $calculated['opening_cost'] = $tariffOptions['opening_cost'];

  // Стоимость обслуживания счета
  $calculated['service'] = calculate_service_cost($tariffOptions);

  // Считаем комиссию по опциям с диапазонами условий
  // Первая часть. Вынес отдельно, что бы разместить после income payment_order
  $rangeFeeContext = ['income'];

  foreach ($rangeFeeContext as $context) {
    $calculated[$context] = calculate_range_fee(
      $userParams,
      $tariffOptions,
      $context
    );
  }

  // Считаем комиссию за платежные поручения
  $calculated['payment_order'] = calculate_payment_order_cost(
    $userParams,
    $tariffOptions
  );

  // Считаем комиссию по опциям с диапазонами условий
  // Вторая часть. Вынес отдельно, что бы разместить после income payment_order
  $rangeFeeContext = [
    'people_transfer',
    'personal_transfer',
    'get_atm',
    'get_cashbox',
    'put_atm',
    'put_cashbox',
  ];

  foreach ($rangeFeeContext as $context) {
    $calculated[$context] = calculate_range_fee(
      $userParams,
      $tariffOptions,
      $context
    );
  }

  // Считаем комиссию по опциям, которые либо включены либо нет
  $booleanFeeContext = ['corp_card', 'sms'];

  foreach ($booleanFeeContext as $context) {
    $calculated[$context] = calculate_boolean_fee(
      $userParams,
      $tariffOptions,
      $context
    );
  }

  return $calculated;
}
