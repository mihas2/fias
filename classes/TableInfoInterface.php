<?php

namespace Fias;


interface TableInfoInterface
{
    /**
     * @return string
     */
    public static function getTableName();

    /**
     * @return array
     */
    public static function getCreateTableSql();

    /**
     * @return array
     */
    public static function getTableFields();

    /**
     * @param array $row - исходные данные записи ввиде массива, где ключ имя поля
     *
     * @return bool
     */
    public static function isActual($row);

    /**
     * @param array $record
     *
     * @return array
     */
    public static function recordProcessing($record);
}
