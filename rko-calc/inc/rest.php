<?php
/**
 * REST API
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.21
 **/

/**
 * Добавляем маршрут REST API для результатов калькулятора
 *
 */
add_action('rest_api_init', function () {
  register_rest_route('rko-calc/v1', '/calculate/', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'rko_calc',
    'args' => [
      'ooo' => [
        'default' => false,
        'sanitize_callback' => function ($param, $request, $key) {
          return (bool) $param;
        },
      ],
      'income' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'personal_transfer' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'people_transfer' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'payment_order' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'get_atm' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'get_cashbox' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'put_atm' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'put_cashbox' => [
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => function ($param, $request, $key) {
          return (int) $param;
        },
      ],
      'corp_card' => [
        'default' => false,
        'sanitize_callback' => function ($param, $request, $key) {
          return (bool) $param;
        },
      ],
      'sms' => [
        'default' => false,
        'sanitize_callback' => function ($param, $request, $key) {
          return (bool) $param;
        },
      ],
      'banks' => [
        'default' => 'all',
        'sanitize_callback' => function ($param, $request, $key) {
          return (string) $param;
        },
      ],
    ],
  ]);
});
