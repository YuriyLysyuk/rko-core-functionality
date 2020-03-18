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
    // ooo - Если ИП: false, если ООО: true
    'ooo' => 0,
    'income' => 200000,
    // personal_transfer = 0, если ooo = 1
    'personal_transfer' => 120000,
    'people_transfer' => 0,
    'payment_order' => 5,
    'get_atm' => 30000,
    'get_cashbox' => 0,
    'put_atm' => 15000,
    'put_cashbox' => 0,
    'corp_card' => true,
    'sms' => true
  ];

  /* $userParams = [
    // ooo - Если ИП: false, если ООО: true
    'ooo' => 1,
    'income' => 200000,
    // personal_transfer = 0, если ooo = 1
    'personal_transfer' => 0,
    'people_transfer' => 120000,
    'payment_order' => 5,
    'get_atm' => 30000,
    'get_cashbox' => 0,
    'put_atm' => 15000,
    'put_cashbox' => 0,
    'corp_card' => true,
    'sms' => true
  ]; */

  return $userParams;
}
