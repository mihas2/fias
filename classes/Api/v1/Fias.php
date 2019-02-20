<?php

namespace Fias\Api\v1;

use Fias\AddressManager;
use Fias\Api\ApiAbstract;
use Fias\HouseManager;
use Fias\Models\AddressModel;

class Fias extends ApiAbstract
{
    /**
     * @return \Fias\Models\AddressModel[]
     * @access protected
     * @expires 86400
     * @cache max-age={expires}, must-revalidate
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
     * @access protected
     * @expires 86400
     * @cache max-age={expires}, must-revalidate
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
     * @access protected
     * @expires 86400
     * @cache max-age={expires}, must-revalidate
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
     * @access protected
     * @expires 86400
     * @cache max-age={expires}, must-revalidate
     * @throws \Exception
     * @url GET /addr-chain/{uuid}/
     */
    public function getAddrChain($uuid)
    {
        $addressManager = new AddressManager();

        return $addressManager->getFullAddr($uuid);
    }

    /**
     * @param string $name
     * @param string|null $uuid {@pattern /^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$/i}
     *
     * @return AddressModel[]
     * @access protected
     * @expires 86400
     * @cache max-age={expires}, must-revalidate
     * @throws \Exception
     *
     * @url GET /address-search/{name}/
     */
    public function getAddressSearch($name, $uuid = null)
    {
        $addressManager = new AddressManager();

        return $addressManager->search($name, $uuid);
    }

    /**
     * @param string|null $id {@pattern /^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$/i}
     *
     * @return \Fias\Models\HouseModel
     * @access protected
     * @expires 86400
     * @cache max-age={expires}, must-revalidate
     * @throws \Exception
     */
    public function getHouseById($id)
    {
        $houseManager = new HouseManager();

        return $houseManager->getById($id);
    }

    /**
     * @param string $uuid {@pattern /^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$/i}
     * @param string $name
     *
     * @return \Fias\Models\HouseModel[]
     * @access protected
     * @expires 86400
     * @cache max-age={expires}, must-revalidate
     * @throws \Exception
     *
     * @url GET /house-search/{uuid}/{name}
     */
    public function getHouseSearch($uuid, $name)
    {
        $houseManager = new HouseManager();

        return $houseManager->search($uuid, $name);
    }
}
