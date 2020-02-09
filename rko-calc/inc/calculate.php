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
 *  Вычисляем стоимость поступления денег от юр. лиц и ИП на счет
 *	*	в месяц
 */
function calculate_income_cost($userParams, $tariffOptions)
{
  if (empty($userParams['income'])) {
    return 0;
  }

  $calculatedIncomeCost = 0;
  // Получаем объем поступлений от юр. лиц и ИП, которые ввел пользователь
  $userIncome = $userParams['income'];
  // Переводим величину комиссии из процентов в число
  $incomeCostFee = $tariffOptions['income_cost'] / 100;

  $calculatedIncomeCost = $userIncome * $incomeCostFee;

  return $calculatedIncomeCost;
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
  // Получаем условия по тарифу
  $tariffOptionsContextCond = $tariffOptions[$context]['cond'];

  if ($tariffOptionsContextCond) {
    foreach ($tariffOptionsContextCond as $cond) {
      // Комиссия указана в %, переводим % в число
      $condCostPercentToNumber = $cond['cost'] / 100;
      // Минимальная комиссия
      $minFee = $cond['min_cost'];
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
          if ($currentFee > $minFee) {
            $calculatedFee += $currentFee;
          } else {
            $calculatedFee += $minFee;
          }
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
            if ($currentFee > $minFee) {
              $calculatedFee += $currentFee;
            } else {
              $calculatedFee += $minFee;
            }
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

  // Стоимость поступления денег от юр. лиц и ИП на счет
  $calculated['income_cost'] = calculate_income_cost(
    $userParams,
    $tariffOptions
  );

  // Считаем комиссию по опциям с диапазонами условий
  $rangeFeeContext = [
    'personal_transfer',
    'people_transfer',
    'get_atm',
    'get_cashbox',
    'put_atm',
    'put_cashbox'
  ];

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
