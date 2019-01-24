<?php

namespace Fias;


class FiasHousesTable implements TableInfo
{
    /**
     * @return string
     */
    static public function getTableName()
    {
        return 'fias_houses';
    }

    /**
     * @return array
     */
    static public function getCreateTableSql()
    {
        $tableName = static::getTableName();

        return [
            "
                create table if not exists `mirmagnitov.ru`.fias_houses
                (
                    aoguid varchar(36) null,
                    buildnum varchar(10) null,
                    eststatus double null,
                    houseguid varchar(36) not null
                        primary key,
                    houseid varchar(36) null,
                    housenum varchar(20) null,
                    statstatus double null,
                    postalcode varchar(6) null,
                    strucnum varchar(10) null,
                    strstatus double null,
                    constraint fias_houses_pk_2
                        unique (houseid)
                )
                collate=utf8_unicode_ci;
            ",
            "
                create index fias_houses_aoguid_index
                    on `mirmagnitov.ru`.fias_houses (aoguid);
            "
        ];
    }

    /**
     * @return array
     */
    static public function getTableFields()
    {
        return [
            'aoguid',
            'buildnum',
            'eststatus',
            'houseguid',
            'houseid',
            'housenum',
            'statstatus',
            'postalcode',
            'strucnum',
            'strstatus',
        ];
    }

    /**
     * @inheritdoc
     */
    static public function isActual($row)
    {
        return ((new \DateTime(trim($row['ENDDATE']))) > (new \DateTime()));
    }
}