#!/usr/bin/env php

<?php
/**
 * â¡Â ÐÂ«Ð³Ð¸ÐÂ  Â¤Â«Ð¿ sendmail Ð±Â®Ð£ÌÐÌÐ°Â ÒÐ² ÐÌÐ±ÐµÂ®Â¤Ð¿Ð¸ÌÐÌÒ ÐÌÐÌÐ±Ð¼Â¬Â  Â®Ð² Ð´Ð³Â­ÐÐ¶ÐÌÐÌ mail() ÑÌ Ð´Â Â©Â«Ð» *.eml
 *
 * â¬Ð±ÐÌÂ®Â«Ð¼Â§Â®ÑÌÂ Â­ÐÌÒ:
 * ÑÌ php.ini ÑÌÂ¬ÒÐ±Ð²Â® Â®ÐÌÐÌÐ¶ÐÌÐÌ:
 * sendmail_path =  "/usr/sbin/sendmail -t -i"
 *
 * ÐÌÐ±ÐÌÂ®Â«Ð¼Â§Â®ÑÌÂ Ð²Ð¼ Â®ÐÌÐ¶ÐÌÐ¾:
 * sendmail_path =  "/usr/local/bin/sendmail.php --dir /tmp/mail"
 *
 * ÐÂ¤Ò,
 * /usr/local/bin/sendmail.php - ÐÌÐ³Ð²Ð¼ Ð Ð½Ð²Â®Â¬Ð³ Ð±ÐÐ°ÐÌÐÌÐ²Ð³
 * --dir /tmp/mail - Â®Ð¶ÐÌÐ¿, Â§Â Â¤Â Ð¾Ð¸ÌÂ Ð¿ Â¤ÐÌÐ°ÒÐÐ²Â®Ð°ÐÌÐ¾ ÐÐ³Â¤Â  Ð£ÌÐ³Â¤Ð³Ð² Ð±ÐÂ«Â Â¤Ð»ÑÌÂ Ð²Ð¼Ð±Ð¿ Ð´Â Â©Â«Ð» ÐÌÐÌÐ±ÒÂ¬
 */

/**
 * @param string $dirname
 * @param int    $i default 1
 *
 * @return string $fileName
 */

function getFileName($dirname, $i = 1)
{
    $fileName = $dirname . date('Y-m-d_H-i-s_') . $i . '.eml';

    return file_exists($fileName) ? getFileName($dirname, ++$i) : $fileName;
}

$options = getopt("", ['dir:']);
$options['dir'] = isset($options['dir']) ? $options['dir'] : sys_get_temp_dir() . '/mail';
$options['dir'] = rtrim($options['dir'], "/") . "/";


if (!is_dir($options['dir'])) {
    mkdir($options['dir'], 0777, true);
    if (!is_dir($options['dir'])) {
        die('Error create dir ' . $options['dir'] . '');
    }
}
$hMail = fopen('php://stdin', 'r') or die();
$hFile = fopen(getFileName($options['dir']), 'w');

while (!feof($hMail)) {
    fputs($hFile, fgets($hMail));
}

fclose($hFile);
fclose($hMail);
