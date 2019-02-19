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
     * @return array
     */
    public function getCityList($regionUuid)
    {
        $this->db->query("SET collation_connection = utf8_unicode_ci");
        $sql = "
            SELECT fa.*
            FROM fias_addresses fa
                   JOIN (SELECT aoguid, parentguid
                         FROM (SELECT * FROM fias_addresses ORDER BY parentguid, aoguid) products_sorted,
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
     * @return array
     */
    public function getFullAddr($uuid)
    {
        $this->db->query("SET collation_connection = utf8_unicode_ci");
        $sql = "
            SELECT fa.*, @pid := fa.parentguid pid
            FROM (
                 SELECT fap.*
                 FROM fias_addresses fap
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
