<?php
if ($_SERVER['DOCUMENT_ROOT']) {
    die();
}

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

require ("vendor/autoload.php");

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } else if ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } else if ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } else if ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } else if ($bytes == 1) {
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
 */
function doConvert($files, $dir ,$tableClass) {
    /** @var  $tableClass \Fias\TableInfo */

    static $totalInserted = 0;
    static $totalRecords = 0;
    static $totalTime = 0;

    unlink($tableClass::getTableName() . ".sql");
    foreach ($tableClass::getCreateTableSql() as $sql) {
        file_put_contents($tableClass::getTableName() . ".sql", trim($sql) . "\n", FILE_APPEND);
    }

    foreach (array_values($files) as $key => $file) {
        $timeStart = microtime(true);

        $fileSize = formatSizeUnits(filesize($dir . $file));
        fwrite(STDOUT, "{$file} ({$fileSize}) - ");

        try {
            $dbf = new \Fias\Dbf2Sql($dir . $file, $tableClass);
            $result = $dbf->convert();
        } catch (\Exception $e) {
            fwrite(STDOUT, print_r($e, true));
            die();
        }

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

ignore_user_abort(true);

$dir = $_SERVER['DOCUMENT_ROOT'] . "/dbf/";
$files = scandir($dir);

$addrObjFiles = array_filter($files, function ($file) {
    return (bool)preg_match("/^addrob\d+\.dbf$/iu", $file);
});

$housesFiles = array_filter($files, function ($file) {
    return (bool)preg_match("/^house\d+\.dbf$/iu", $file);
});

doConvert($addrObjFiles, $dir, \Fias\FiasAddressTable::class);
doConvert($housesFiles, $dir, \Fias\FiasHousesTable::class);
