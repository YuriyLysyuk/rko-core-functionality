<?php
/**
 * Автоматическая проверка изменения тарифов РКО на сайтах банков
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.0
 **/

// ToDo: В crontab нужно добавить строку
// ToDo: */15 * * * * wget -O /dev/null -q 'https://rko.guru/wp-cron.php'
// ToDo: для срабатывания проверки заданий каждые 15 минут, если чаще никто не заходит

// Проверка существования расписания во время работы плагина на всякий пожарный случай
if (!wp_next_scheduled('rko_check_update_tariff_docs')) {
  // Добавляем задание для проверки изменений тарифов РКО дважды в день
  wp_schedule_event(time(), 'twicedaily', 'rko_check_update_tariff_docs');
}

// Добавляем функцию запуска проверки
add_action('rko_check_update_tariff_docs', "rko_do_check_update_tariff_docs");

// Функция для загрузки и проверки изменений файлов с тарифами с сайтов банков
function rko_do_check_update_tariff_docs()
{
  // Полный отчет по изменениям
  $message = "";
  // Обнуляем флаг изменений
  $haveChanges = false;

  // Получаем данные каждого тарифа и конструируем ассоциативный массив
  $allTariffOptions = get_all_tariff_options();

  // Перебираем все тарифы
  foreach ($allTariffOptions as $tariffOptions) {
    // Обнуляем флаг изменений в отдельном тарифу
    $haveRowChanges = false;
    // Обнуляем отдельный отчет по изменениям для одного тарифа
    $rowMessage = "";

    // Если в опциях тарифа есть поле с ссылками на тарифы
    if (have_rows('docs', $tariffOptions['id'])) {
      // Для всех записей...
      while (have_rows('docs', $tariffOptions['id'])) {
        // ... устанавливаем значения записи
        the_row();

        // Обнуляем переменные с именем файла (текущим и предыдушим)
        $previousLocalFilename = "";
        $currentLocalFilename = "";
        $oldestLocalFilename = "";

        // Получаем поля запими
        $doc = [
          'structure' => get_sub_field('structure'),
          'view' => get_sub_field('view'),
          'suffix' => get_sub_field('suffix'),
          'ext' => get_sub_field('ext'),
          'url' => get_sub_field('url'),
        ];

        /**
         * Формируем жесткое имя файла
         */

        //  Добавляем имя банка или тарифа в зависимости от состава файла
        switch ($doc['structure']['value']) {
          case 'common':
            $previousLocalFilename .= $tariffOptions['bank']['slug'];
            $currentLocalFilename .= $tariffOptions['bank']['slug'];
            break;

          case "single":
            $previousLocalFilename .= $tariffOptions['slug'];
            $currentLocalFilename .= $tariffOptions['slug'];
            break;
        }

        // Состав файла
        $previousLocalFilename .= '_' . $doc['structure']['value'];
        $currentLocalFilename .= '_' . $doc['structure']['value'];

        // Вид файла
        $previousLocalFilename .= '_' . $doc['view']['value'];
        $currentLocalFilename .= '_' . $doc['view']['value'];

        // Наличие других условий
        $previousLocalFilename .= '_' . $doc['suffix']['value'];
        $currentLocalFilename .= '_' . $doc['suffix']['value'];

        // Временная приписка
        $previousLocalFilename .= '_previous';
        $currentLocalFilename .= '_current';

        // Расширение
        $previousLocalFilename .= '.' . $doc['ext'];
        $currentLocalFilename .= '.' . $doc['ext'];

        // Формируем начало вывода в виде идентификации строки
        $beginRowMessage = '<li>';
        $beginRowMessage .= $doc['structure']['label'] . ' ';
        $beginRowMessage .= $doc['view']['label'] . ', ';
        $beginRowMessage .=
          'none' === $doc['suffix']['value']
            ? ''
            : $doc['suffix']['label'] . ', ';
        $beginRowMessage .= $doc['ext'] . ': ';

        // Если файла с текущим тарифом не существует
        if (!file_exists(TARIFF_DOCS_UPLOAD_DIR . $currentLocalFilename)) {
          // Фиксируем наличие изменений
          $haveChanges = $haveRowChanges = true;

          // Добавляем идентификацию строки
          $rowMessage .= $beginRowMessage;

          // Скачиваем тариф и сохраняем под текущим именем
          $errors = download_tariff_docs($doc['url'], $currentLocalFilename);

          /**
           * Проверяем есть ли ошибки при скачивании и сохранении
           */

          // Если переменая ошибок это объект с ошибками — выводим их
          if (
            !is_null($errors) &&
            is_object($errors) &&
            $errors->get_error_code()
          ) {
            $rowMessage .=
              'файл не найден, <span style="color:red;">ошибки при скачивании:</span>';
            $rowMessage .= '<ul>';

            foreach ($errors->get_error_messages() as $error) {
              $rowMessage .= '<li>' . $error . '</li>';
            }

            $rowMessage .= '</ul></li>'; // $doc['structure']['label']
          } elseif (!is_null($errors) && is_string($errors)) {
            // Если переменная ошибок это строка, выводим её
            $rowMessage .=
              '<span style="color:red;">' . $errors . '</span></li>'; // $doc['structure']['label']
          } else {
            // Если ошибок нет, значит скачен новый файл — выводим сообщение и ссылку на файл
            $rowMessage .=
              'файл не найден, <span style="color:green;">скачен и сохранен </span><a target="_blank" href="' .
              TARIFF_DOCS_UPLOAD_URL .
              $currentLocalFilename .
              '">' .
              $currentLocalFilename .
              '</a></li>'; // $doc['structure']['label']
          }
        } else {
          // Файл с тарифом существует
          // Проверяем размеры текущего локального тарифа и удаленного на сайте банка и если они не совпадают
          if (
            filesize(TARIFF_DOCS_UPLOAD_DIR . $currentLocalFilename) !==
            curl_get_file_size($doc['url'])
          ) {
            // Фиксируем наличие изменений
            $haveChanges = $haveRowChanges = true;

            // Добавляем идентификацию строки
            $rowMessage .= $beginRowMessage;

            // Если существует предыдущий файл
            if (file_exists(TARIFF_DOCS_UPLOAD_DIR . $previousLocalFilename)) {
              $oldestLocalFilename =
                $previousLocalFilename .
                date('_d-m-Y-G-i-s') .
                '.' .
                $doc['ext'];
              // Переименовываем его с меткой времени
              @rename(
                TARIFF_DOCS_UPLOAD_DIR . $previousLocalFilename,
                TARIFF_DOCS_UPLOAD_DIR . $oldestLocalFilename
              );
            }

            // Если существует текущий файл
            if (file_exists(TARIFF_DOCS_UPLOAD_DIR . $currentLocalFilename)) {
              /// Переименовываем текущий файл тарифа в предыдущий
              @rename(
                TARIFF_DOCS_UPLOAD_DIR . $currentLocalFilename,
                TARIFF_DOCS_UPLOAD_DIR . $previousLocalFilename
              );
            }

            // Скачиваем файл с тарифом и сохраняем как текущий тариф
            download_tariff_docs($doc['url'], $currentLocalFilename);

            // Выводим сообщение и ссылки на старейший, прошлый и текущий тариф
            $rowMessage .= '<span style="color:green;">есть изменения: </span>';
            $rowMessage .= '<ul>';

            if ($oldestLocalFilename) {
              $rowMessage .=
                '<li><a target="_blank" href="' .
                TARIFF_DOCS_UPLOAD_URL .
                $oldestLocalFilename .
                '">Старейший тариф' .
                '</a></li>';
            }

            $rowMessage .=
              '<li><a target="_blank" href="' .
              TARIFF_DOCS_UPLOAD_URL .
              $previousLocalFilename .
              '">Прошлый тариф' .
              '</a></li>';

            $rowMessage .=
              '<li><a target="_blank" href="' .
              TARIFF_DOCS_UPLOAD_URL .
              $currentLocalFilename .
              '">Новый тариф' .
              '</a></li>';

            $rowMessage .= '</ul></li>'; // $doc['structure']['label']
          }
        }
      }

      // Если по тарифу есть изменения, показываем их
      if ($haveRowChanges) {
        $message .=
          "<p><strong>" .
          $tariffOptions['name'] .
          ' (' .
          $tariffOptions['bank']['name']['chto'] .
          ")</strong></p><ol>";
        $message .= $rowMessage;
        $message .= "</ol>";
      }
    }
  }

  // Подготавливаем служебные поля для письма
  $to = get_option('admin_email');
  $subject = '[New] Отчет по изменениям в тарифах';

  if (!$haveChanges) {
    $subject = 'Отчет по изменениям в тарифах';
    $message = '<p>Изменений нет. Займись чем-нибудь интересным :)</p>';
  }

  // Устанавливаем html формат письма
  add_filter('wp_mail_content_type', 'rko_set_html_mail_content_type');

  // Устанавливаем имя отправителя письма
  add_filter('wp_mail_from_name', 'rko_set_mail_from_name');

  // Отправляем письмо администратору с отчетом
  wp_mail($to, $subject, $message);

  // Сбрасываем фильтр для предотвращения ошибок -- https://core.trac.wordpress.org/ticket/23578
  remove_filter('wp_mail_content_type', 'rko_set_html_mail_content_type');
}

// Шорткод для тестирования
add_shortcode('test_rko_calc_cron', 'rko_do_check_update_tariff_docs');

// Устанавливаем html формат письма
function rko_set_html_mail_content_type()
{
  return 'text/html';
}

// Устанавливаем имя отправителя письма
function rko_set_mail_from_name()
{
  return get_option('blogname');
}
