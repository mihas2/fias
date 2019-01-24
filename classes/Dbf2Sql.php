<?php

namespace Fias;

use Bitrix\Main\Application;

class Dbf2Sql extends DbfReader
{
    /** @var array */
    const TYPE = [
        'N' => "DOUBLE",
        'C' => "VARCHAR",
        'F' => "FLOAT",
        'D' => "DATE",
        'L' => "BIT",
        'M' => "TEXT"
    ];

    /** @var string */
    private $tableName;

    /** @var int */
    private $fieldCount;

    /** @var string */
    private $tableInfo;

    /** @var array */
    private $fields = [];

    /**
     * @var mixed
     */
    private $db;

    /**
     * Dbf2Sql constructor.
     * @param string $filename
     * @param string $tableInfo
     * @throws \Exception
     */
    public function __construct($filename, $tableInfo)
    {
        if (!file_exists($filename)) {
            throw new \Exception("File '{$filename}' not exist");
        }
        /** @var $tableInfo TableInfo */
        if (!is_subclass_of($tableInfo, 'Fias\TableInfo')) {
            throw new \Exception('tableInfo must be implements interface Fias\TableInfo');
        }

        $this->tableInfo = $tableInfo;
        $this->tableName = $tableInfo::getTableName();
        $this->db = static::getDb();

        parent::__construct($filename);

        $this->fieldCount = count($this->getInfos());

        foreach ($this->getInfos() as $field) {
            $fieldName = strtoupper(trim($field['fieldName']));
            $this->fields[$fieldName] = [
                "NAME" => $fieldName,
                "TYPE" => static::TYPE[$field['fieldType']],
                "SIZE" => $field['fieldLen'],
                "OFFSET" => $field['offset'],
            ];
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function convert()
    {
        $tableInfo = $this->tableInfo;

        $inserted = 0;

        $fileHandle = fopen($this->tableName . ".sql", "a");

        for ($i = 0; $i < $this->getRecordCount(); $i++) {
            $row = $this->fetch($i, false);

            if (!$tableInfo::isActual($row)) { // только актуальные адреса ФИАС
                continue;
            }

            foreach ($row as $field => $val) {
                if (count($tableInfo::getTableFields()) > 0 && !in_array(strtolower($field), $tableInfo::getTableFields())) {
                    unset($row[$field]);
                    continue; // удаляем и пропускаем поля которые нам не нужны
                }
                switch ($this->fields[$field]['TYPE']) {
                    case 'TEXT':
                    case 'VARCHAR':
                        $row[$field] = "'"
                                       .iconv("IBM866", "UTF-8", $val)
                                       . "'";
                        break;

                    case 'DATE':
                        if (trim($val)) {
                            try {
                                $row[$field] = "'" . (new \DateTime(trim($val)))->format("Y-m-d H:i:s") . "'";
                            } catch (\Exception $e) {
                                $row[$field] = "''";
                            }
                        }
                        break;

                    case 'BIT':
                        $row[$field] = (int)trim($val);
                        break;

                    case 'DOUBLE':
                        $row[$field] = (double)trim($val);
                        break;

                    case 'FLOAT':
                        $row[$field] = (float)$val;
                        break;
                }
            }
            $sql = sprintf(
                "insert into %s (%s) values (%s) on duplicate key update %s;\n",
                $this->tableName,
                implode(", ", array_keys($row)), implode(", ", $row),
                implode(", ", array_map(function ($val, $key) {
                    return sprintf("%s = %s", $key, $val);
                }, $row, array_keys($row)))
            );

            fwrite($fileHandle, $sql);

            $inserted++;
        }

        fclose($fileHandle);

        return [
            "inserted" => $inserted,
            "recordCount" => $this->getRecordCount()
        ];
    }

    /**
     * @return \Bitrix\Main\DB\Connection
     */
    private static function getDb()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return int
     */
    public function getFieldCount()
    {
        return $this->fieldCount;
    }

    /**
     *
     */
    public function createTable()
    {
    }

    /**
     * @return bool
     */
    public function isTableExist()
    {
        return false;
    }

    public function createIdentTable()
    {
        foreach ($this->fields as $field) {
            $t = ($field['TYPE'] === 'VARCHAR') ? "(" . $field['SIZE'] . ")" : '';

            $ta[] = " " . strtolower($field['NAME']) . " "
                    . $field['TYPE'] . $t;
        }

        $sql = sprintf(
            "create table if not exists %s (%s) engine=InnoDB collate=utf8_unicode_ci",
            $this->tableName,
            implode(", ", $ta)
        );

        $this->db->queryExecute($sql);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
