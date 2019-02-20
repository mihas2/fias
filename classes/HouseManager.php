<?php

namespace Fias;


use Fias\Models\HouseModel;

class HouseManager extends AbstractManager
{
    const TABLE = 'fias_houses';

    /**
     * @param DbQuery $query
     *
     * @return HouseModel[]
     * @throws \Exception
     */
    public function getList(DbQuery $query)
    {
        $query->setTableName(static::TABLE);

        $result = $this->db->query($query->build());

        $addresses = [];
        while ($address = $result->fetch(\PDO::FETCH_ASSOC)) {
            $addresses[] = HouseModel::mapFields($address);
        }

        return $addresses;
    }

    /**
     * @param $id
     *
     * @return HouseModel
     * @throws \Exception
     */
    public function getById($id)
    {
        $query = new DbQuery(["houseid = '{$id}'"]);
        $query->setLimit(1);

        return $this->getList($query)[0];
    }

    /**
     * @param $uuid
     * @param $number
     *
     * @return HouseModel[]
     * @throws \Exception
     */
    public function search($uuid, $number)
    {
        $number = $this->db->quote($number . "%");
        $query = new DbQuery();
        $query->addFilter(0, "aoguid = '{$uuid}'");
        $query->addFilter(1, "housenum LIKE {$number}");

        return $this->getList($query);
    }
}
