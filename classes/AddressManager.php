<?php

namespace Fias;


use Fias\Models\AddressModel;
use Fias\Models\HouseModel;

class AddressManager extends AbstractManager
{
    const TABLE = 'fias_addresses';

    /**
     * @param DbQuery $query
     *
     * @return AddressModel[]
     * @throws \Exception
     */
    public function getList(DbQuery $query)
    {
        $query->setTableName(static::TABLE);

        $result = $this->db->query($query->build());

        $addresses = [];
        while ($address = $result->fetch(\PDO::FETCH_ASSOC)) {
            $addresses[] = AddressModel::mapFields($address);
        }

        return $addresses;
    }

    /**
     * @return AddressModel[]
     * @throws \Exception
     */
    public function getRegionList()
    {
        $query = new DbQuery(["aolevel in (1, 2)"]);
        $query->setOrders(['offname' => 'ASC']);

        return $this->getList($query);
    }

    /**
     * @param string $regionUuid
     *
     * @return AddressModel[]
     */
    public function getCityList($regionUuid)
    {
        $tableName = static::TABLE;

        $this->db->query("SET collation_connection = utf8_unicode_ci");
        $sql = "
            SELECT fa.*
            FROM {$tableName} fa
                   JOIN (SELECT aoguid, parentguid
                         FROM (SELECT * FROM {$tableName} ORDER BY parentguid, aoguid) products_sorted,
                              (SELECT @pid := '{$regionUuid}') initialisation
                         WHERE FIND_IN_SET(parentguid, @pid) > 0 AND @pid := CONCAT(@pid, ',', aoguid)) tree
                     ON tree.aoguid = fa.aoguid
            WHERE fa.aolevel IN (4, 5, 6)
            ORDER BY fa.offname";

        $result = $this->db->query($sql, \PDO::FETCH_ASSOC);
        $addresses = [];
        while ($address = $result->fetch(\PDO::FETCH_ASSOC)) {
            $addresses[] = AddressModel::mapFields($address);
        }

        return $addresses;
    }

    /**
     * @param string $uuid
     *
     * @return AddressModel[]
     */
    public function getFullAddr($uuid)
    {
        $tableName = static::TABLE;

        $this->db->query("SET collation_connection = utf8_unicode_ci");
        $sql = "
            SELECT fa.*, @pid := fa.parentguid pid
            FROM (
                 SELECT fap.*
                 FROM {$tableName} fap
                 JOIN (SELECT @pid := '{$uuid}') initial
                 ORDER BY fap.aolevel DESC
                 ) fa
            WHERE fa.aoguid = @pid";

        $result = $this->db->query($sql, \PDO::FETCH_ASSOC);
        $addresses = [];
        while ($address = $result->fetch(\PDO::FETCH_ASSOC)) {
            $addresses[] = AddressModel::mapFields($address);
        }

        return $addresses;

    }

    /**
     * @param $name
     * @param $uuid
     *
     * @return AddressModel[]
     * @throws \Exception
     */
    public function search($name, $uuid)
    {
        $tableName = static::TABLE;

        $name = $this->db->quote($name."%");
        if ($uuid) {
            $query = new DbQuery(["aoguid = '{$uuid}'"]);
            $query->setLimit(1);
            $address = $this->getList($query)[0];
            if ($address) {
                switch ($address->getType()) {
                    case AddressModel::REGION:
                        $level = "35, 4, 5, 6";
                        break;
                    case AddressModel::AREA:
                        $level = "35, 4, 5, 6";
                        break;
                    case AddressModel::CITY:
                        $level = "7";
                        break;
                    case AddressModel::STREET:
                        $level = "8";
                        break;
                    default:
                        $level = "1, 2";
                }
                $sql = "
                SELECT *
                FROM (SELECT *
                      FROM (SELECT * FROM {$tableName} ORDER BY parentguid, aoguid) products_sorted,
                           (SELECT @pid := '{$uuid}') initialisation
                      WHERE FIND_IN_SET(parentguid, (@pid COLLATE utf8_unicode_ci)) > 0
                              AND @pid := CONCAT(@pid, ',', aoguid)) fa
                WHERE 1=1
                    AND offname LIKE {$name}
                    AND aolevel IN ($level)
            ";

            }
        } else {
            $sql = "
                SELECT *
                FROM {$tableName}
                WHERE offname LIKE {$name}
                  AND aolevel IN (1, 2)";
        }
        $result = $this->db->query($sql, \PDO::FETCH_ASSOC);
        $addresses = [];
        while ($address = $result->fetch(\PDO::FETCH_ASSOC)) {
            $addresses[] = AddressModel::mapFields($address);
        }

        return $addresses;
    }

    /**
     * @param $id
     *
     * @return AddressModel
     * @throws \Exception
     */
    public function getById($id)
    {
        $query = new DbQuery(["aoid" => $id]);
        $query->setLimit(1);

        return $this->getList($query)[0];
    }
}
