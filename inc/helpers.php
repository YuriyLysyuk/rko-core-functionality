<?php
/**
 * Helpers
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.0
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
