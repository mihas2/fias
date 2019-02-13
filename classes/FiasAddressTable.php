<?php

namespace Fias;


class FiasAddressTable implements TableInfoInterface
{
    /**
     * @return string
     */
    static public function getTableName()
    {
        return 'fias_addresses';
    }

    /**
     * @return array
     */
    static public function getCreateTableSql()
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
    static public function getTableFields()
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
    static public function isActual($row)
    {
        return (bool)trim($row['ACTSTATUS']);
    }

    /**
     * @param array $record
     *
     * @return array
     */
    static public function recordProcessing($record)
    {
        return $record;
    }

}