<?php
if ($_SERVER['DOCUMENT_ROOT']) {
    die();
}

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

require("vendor/autoload.php");

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

/**
 * @param array $files
 * @param string $dir
 * @param string $tableClass
 * @param bool $update
 */
function doConvert($files, $dir, $tableClass, $update = false)
{
    /** @var  $tableClass \Fias\TableInfoInterface */

    static $totalInserted = 0;
    static $totalRecords = 0;
    static $totalTime = 0;

    foreach (array_values($files) as $key => $file) {
        $timeStart = microtime(true);

        $fileSize = formatSizeUnits(filesize($dir . $file));
        fwrite(STDOUT, "{$file} ({$fileSize}) - ");


        $dbf = new \Fias\Fias2Sql($dir . $file, $tableClass);

        if ($key === 0 && !$update) {
            $dbf->dropTable();
            $dbf->createTable();
        }

        $result = $dbf->convert();

        $time = (microtime(true) - $timeStart);

        fwrite(STDOUT, "inserted: {$result['inserted']} of {$result['recordCount']}. Time {$time}\n");

        $totalInserted += $result['inserted'];
        $totalRecords += $result['recordCount'];
        $totalTime += $time;
    }

    fwrite(STDOUT, "Total inserted: {$totalInserted} of {$totalRecords}. Total time {$totalTime}\n");
}

ini_set('max_execution_time', 0);
@set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);

$dir = $_SERVER['DOCUMENT_ROOT'] . "/dbf/";
$files = scandir($dir);

$addrObjFiles = array_filter(
    $files, function ($file) {
    return (bool)preg_match("/^addrob\d+\.dbf$/i", $file);
});

$housesFiles = array_filter(
    $files, function ($file) {
    return (bool)preg_match("/^house\d+\.dbf$/i", $file);
});

switch (strtolower($argv[1])) {
    case 'update':
        $update = true;
        break;
    case 'full':
        $update = false;
        break;
    default:
        fwrite(STDOUT, "USEGE: php convert.php <PARAM>\nParam: 'update' or 'full'\n");
        exit();
}

doConvert($addrObjFiles, $dir, \Fias\FiasAddressTable::class, $update);
doConvert($housesFiles, $dir, \Fias\FiasHousesTable::class, $update);
