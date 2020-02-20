<?php
/**
 * Функции для получения параметров, заданных пользователем для расчета стоимости обслуживания по тарифу
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.2.0
 **/

/**
 *
 *
 */
function get_user_params()
{
  $userParams = [
    'income' => 100000,
		'personal_transfer' => 100000,
		'people_transfer' => 0,
		'payment_order' => 0,
		'get_atm' => 0,
		'get_cashbox' => 0,
		'put_atm' => 0,
		'put_cashbox' => 0,
		'corp_card' => false,
		'sms' => false
    // ooo - Если ИП: false, если ООО: true
    'ooo' => 0,
  ];

  echo '<pre>';
  print_r($userParams);
  echo '</pre>';

  return $userParams;
}
