<?php
/**
 * REST API
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since
 **/

/**
 * Добавляем маршрут REST API для результатов калькулятора
 *
 */
add_action('rest_api_init', function () {
  register_rest_route('rko-calc/v1', '/calculate/', array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'rko_calc',
    'args' => array(
      'ooo' => array(
        'default' => false,
        'sanitize_callback' => function ($param, $request, $key) {
          return (bool) $param;
        }
      ),
      'income' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'personal_transfer' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'people_transfer' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'payment_order' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'get_atm' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'get_cashbox' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'put_atm' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'put_cashbox' => array(
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        }
      ),
      'corp_card' => array(
        'default' => false,
        'sanitize_callback' => function ($param, $request, $key) {
          return (bool) $param;
        }
      ),
      'sms' => array(
        'default' => false,
        'sanitize_callback' => function ($param, $request, $key) {
          return (bool) $param;
        }
      )
    )
  ));
});
