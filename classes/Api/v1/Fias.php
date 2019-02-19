<?php

namespace Fias\Api\v1;

use Fias\AddressManager;
use Fias\Api\ApiAbstract;
use Fias\DbQuery;

class Fias extends ApiAbstract
{
    /**
     * @return \Fias\Models\AddressModel[]
     * @throws \Exception
     * @url GET /region-list/
     */
    public function getRegionList()
    {
        $addressManager = new AddressManager();

        return $addressManager->getRegionList();
    }


    /**
     * @param $regionUuid {@pattern /^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$/i}
     *
     * @return \Fias\Models\AddressModel[]
     * @throws \Exception
     * @url GET /city-list/{regionUuid}/
     */
    public function getCityList($regionUuid)
    {
        $addressManager = new AddressManager();

        return $addressManager->getCityList($regionUuid);
    }

    /**
     * @param string $id {@pattern /^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$/i}
     *
     * @return \Fias\Models\AddressModel
     * @throws \Exception
     */
    public function getById($id)
    {
        $addressManager = new AddressManager();

        return $addressManager->getById($id);
    }

    /**
     * @param $uuid {@pattern /^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$/i}
     *
     * @return \Fias\Models\AddressModel[]
     * @throws \Exception
     * @url GET /addr-chain/{uuid}/
     */
    public function getAddrChain($uuid)
    {
        $addressManager = new AddressManager();

        return $addressManager->getFullAddr($uuid);
    }
}
