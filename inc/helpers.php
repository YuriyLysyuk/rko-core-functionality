<?php
/**
 * Helpers
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.0
 **/

/**
 * Atomic filewriter.
 *
 * Safely writes new contents to a file using an atomic two-step process.
 * If the script is killed before the write is complete, only the temporary
 * trash file will be corrupted.
 *
 * The algorithm also ensures that 100% of the bytes were written to disk.
 *
 * @param string $filename     Filename to write the data to.
 * @param string $data         Data to write to file.
 * @param string $atomicSuffix Lets you optionally provide a different
 *                             suffix for the temporary file.
 *
 * @return int|bool Number of bytes written on success, otherwise `FALSE`.
 */
function atomicWrite($filename, $data, $atomicSuffix = 'atomictmp')
{
  // Perform an exclusive (locked) overwrite to a temporary file.
  $filenameTmp = sprintf('%s.%s', $filename, $atomicSuffix);
  $writeResult = @file_put_contents($filenameTmp, $data, LOCK_EX);

  // Only proceed if we wrote 100% of the data bytes to disk.
  if ($writeResult !== false && $writeResult === strlen($data)) {
    // Удаляем предыдущий файл, если он был
    if (file_exists($filename)) {
      @unlink($filename);
    }

    // Now move the file to its real destination (replaces if exists).
    $moveResult = @rename($filenameTmp, $filename);
    if ($moveResult === true) {
      // Successful write and move. Return number of bytes written.
      return $writeResult;
    }
  }

  // We've failed. Remove the temporary file if it exists.
  if (is_file($filenameTmp)) {
    @unlink($filenameTmp);
  }

  return false; // Failed.
}

/**
 * Returns the size of a file without downloading it, or -1 if the file
 * size could not be determined.
 *
 * @param $url - The location of the remote file to download. Cannot
 * be null or empty.
 *
 * @return The size of the file referenced by $url, or -1 if the size
 * could not be determined.
 */
function curl_get_file_size($url)
{
  // Assume failure.
  $result = -1;

  $curl = curl_init($url);

  // Issue a HEAD request and follow any redirects.
  curl_setopt($curl, CURLOPT_NOBODY, true);
  curl_setopt($curl, CURLOPT_HEADER, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
  // curl_setopt($curl, CURLOPT_USERAGENT, get_user_agent_string());

  $data = curl_exec($curl);
  curl_close($curl);

  if ($data) {
    $content_length = "unknown";
    $status = "unknown";

    if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
      $status = (int) $matches[1];
    }

    if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
      $content_length = (int) $matches[1];
    }

    // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
    if ($status == 200 || ($status > 300 && $status <= 308)) {
      $result = $content_length;
    }
  }

  return $result;
}
