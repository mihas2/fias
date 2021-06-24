<?php

namespace Fias;


class FiasHousesTable implements TableInfoInterface
{
    /** @var array */
    const ESTSTAT = [
        0 => 'Не определено',
        1 => 'Владение',
        2 => 'Дом',
        3 => 'Домовладение',
        4 => 'Гараж',
        5 => 'Здание',
        6 => 'Шахта',
    ];

    /** @var array */
    const STRSTAT = [
        0 => 'Не определено',
        1 => 'Строение',
        2 => 'Сооружение',
        3 => 'Литер',
    ];

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'fias_houses';
    }

    /**
     * @return array
     */
    public static function getCreateTableSql()
    {
        $tableName = static::getTableName();

        return [
            "
                create table if not exists ${tableName}
                (
                    aoguid varchar(36) null,
                    buildnum varchar(10) null,
                    eststatus double null,
                    estname varchar(80) null,
                    houseguid varchar(36) not null,
                    houseid varchar(36) null,
                    housenum varchar(20) null,
                    statstatus double null,
                    postalcode varchar(6) null,
                    strucnum varchar(10) null,
                    strstatus double null,
                    strname varchar(80) null
                )
                collate=utf8_unicode_ci;
            ",
            "
                create index fias_houses_aoguid_index2
	            on ${tableName} (aoguid);
	        ",
            "
                create index fias_houses_houseguid_index
	            on ${tableName} (houseguid);
            ",
            "
                create index fias_houses_housenum_index
            	on ${tableName} (housenum);
	        "
        ];
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public static function recordProcessing($record)
    {
        foreach ($record as $field => $val) {
            switch (strtolower($field)) {
                case 'eststatus':
                    $record['ESTNAME'] = static::ESTSTAT[(int)$val];
                    break;
                case 'strstatus':
                    $record['STRNAME'] = static::STRSTAT[(int)$val];
                    break;
            }
        }

        return $record;
    }

    /**
     * @return array
     */
    public static function getTableFields()
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
            'estname',
            'strname',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function isActual($row)
    {
        return ((new \DateTime(trim($row['ENDDATE']))) > (new \DateTime()));
    }
}
