<?php

namespace Fias\Models;


class AddressModel implements \JsonSerializable
{
    public const REGION = "REGION";
    public const AREA = "AREA";
    public const CITY = "CITY";
    public const STREET = "STREET";
    public const HOUSE = "HOUSE";

    /** @var string|null */
    protected $uuid;

    /** @var string|null */
    protected $id;

    /** @var string|null */
    protected $parentId;

    /** @var string|null */
    protected $postalIndex;

    /** @var string|null */
    protected $type;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $prefix;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param null|string $uuid
     */
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * @param null|string $parentId
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return null|string
     */
    public function getPostalIndex(): ?string
    {
        return $this->postalIndex;
    }

    /**
     * @param null|string $postalIndex
     */
    public function setPostalIndex(?string $postalIndex): void
    {
        $this->postalIndex = $postalIndex;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param null|string $prefix
     */
    public function setPrefix(?string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @param $fields
     *
     * @return static
     */
    public static function mapFields($fields)
    {
        $o = new static();

        $o->setId($fields['aoid']);
        $o->setUuid($fields['aoguid']);
        $o->setName($fields['offname']);
        $o->setParentId($fields['parentguid']);
        $o->setPostalIndex($fields['postalcode']);
        $o->setPrefix($fields['shortname']);

        switch ((int)$fields['aolevel']) {
            case 1:
            case 2:
                $o->setType(static::REGION);
                break;
            case 3:
                $o->setType(static::AREA);
                break;
            case 4:
            case 5:
            case 6:
            case 35:
            case 65:
                $o->setType(static::CITY);
                break;
            case 7:
                $o->setType(static::STREET);
                break;
            case 8:
                $o->setType(static::HOUSE);
                break;
        }

        return $o;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
