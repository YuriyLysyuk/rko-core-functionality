<?php
/**
 * Функции для получения параметров, заданных пользователем для расчета стоимости обслуживания по тарифу
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.2.0
 **/

function get_user_params(WP_REST_Request $request)
{
  $userParams = [
    // ooo - Если ИП: false, если ООО: true
    'ooo' => $request['ooo'],
    'income' => $request['income'],
    // personal_transfer = 0, если ooo = 1
    'personal_transfer' => $request['personal_transfer'],
    'people_transfer' => $request['people_transfer'],
    'payment_order' => $request['payment_order'],
    'get_atm' => $request['get_atm'],
    'get_cashbox' => $request['get_cashbox'],
    'put_atm' => $request['put_atm'],
    'put_cashbox' => $request['put_cashbox'],
    'corp_card' => $request['corp_card'],
    'sms' => $request['sms']
  ];

  return $userParams;
}
