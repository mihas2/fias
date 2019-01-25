<?php

namespace Fias;


interface TableInfoInterface
{
    /**
     * @return string
     */
    static public function getTableName();

    /**
     * @return array
     */
    static public function getCreateTableSql();

    /**
     * @return array
     */
    static public function getTableFields();

    /**
     * @param array $row - исходные данные записи ввиде массива, где ключ имя поля
     * @return bool
     */
    static public function isActual($row);
}
