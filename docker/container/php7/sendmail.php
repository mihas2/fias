#!/usr/bin/env php

<?php
/**
 * � �«ги�  ¤«п sendmail б®У���а �в ��бе®¤пи���� ����бм¬  ®в дг­�ж���� mail() �� д ©«л *.eml
 *
 * �б��®«м§®�� ­���:
 * �� php.ini ��¬�бв® ®����ж����:
 * sendmail_path =  "/usr/sbin/sendmail -t -i"
 *
 * ��б��®«м§®�� вм ®��ж��о:
 * sendmail_path =  "/usr/local/bin/sendmail.php --dir /tmp/mail"
 *
 * �¤�,
 * /usr/local/bin/sendmail.php - ��гвм � нв®¬г б�а����вг
 * --dir /tmp/mail - ®ж��п, § ¤ ои� п ¤��а��в®а��о �г¤  У�г¤гв б�« ¤л�� вмбп д ©«л ����б�¬
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
