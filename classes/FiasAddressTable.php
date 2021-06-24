<?php

namespace Fias;


class FiasAddressTable implements TableInfoInterface
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'fias_addresses';
    }

    /**
     * @return array
     */
    public static function getCreateTableSql()
    {
        $tableName = static::getTableName();

        return [
            "
                create table if not exists {$tableName}
                (
                    aoguid varchar(36) not null primary key,
                    aoid varchar(36) null,
                    aolevel double null,
                    formalname varchar(120) null,
                    offname varchar(120) null,
                    parentguid varchar(36) null,
                    postalcode varchar(6) null,
                    shortname varchar(10) null,
                    constraint fias_addr_pk_id
                        unique (aoguid)
                )
                collate=utf8_unicode_ci;
                ",
            "
                create index fias_addr__index_parent
                    on {$tableName} (parentguid);
                "
        ];
    }

    /**
     * @return array
     */
    public static function getTableFields()
    {
        return [
            "aoguid",
            "aoid",
            "aolevel",
            "formalname",
            "offname",
            "parentguid",
            "postalcode",
            "shortname",
        ];
    }

    /**
     * @inheritdoc
     */
    public static function isActual($row)
    {
        return (bool)trim($row['ACTSTATUS']);
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public static function recordProcessing($record)
    {
        return $record;
    }

}
