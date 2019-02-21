<?php

namespace Fias;


use Fias\Models\AddressModel;

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
            WHERE fa.aolevel IN (35, 4, 5, 6)
              AND (parentguid = '{$regionUuid}'
                     OR parentguid IN (SELECT aoguid FROM {$tableName} WHERE parentguid = '{$regionUuid}'))";

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
     * @throws \Exception
     */
    public function getFullAddr($uuid)
    {
        $addresses = [];

        do {
            $query = new DbQuery(["aoguid = '$uuid'"]);
            $query->setLimit(1);

            $address = $this->getList($query)[0];
            if ($address) {
                $addresses[] = $address;
                $uuid = $address->getParentId();
            }

        } while ($address);


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

        $name = $this->db->quote($name . "%");
        if ($uuid) {
            $query = new DbQuery(["aoguid = '{$uuid}'"]);
            $query->setLimit(1);
            $address = $this->getList($query)[0];
            if ($address) {
                $sql = "
                SELECT *
                FROM fias_addresses
                WHERE offname LIKE {$name}
                    AND parentguid = '{$uuid}'";
            } else {
                return [];
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
