<?php

namespace Fias\Models;


class HouseModel implements \JsonSerializable
{
    /** @var string|null */
    protected $id;

    /** @var string|null */
    protected $uuid;

    /** @var string|null */
    protected $parentUuid;

    /** @var string|null */
    protected $number;

    /** @var string|null */
    protected $build;

    /** @var string|null */
    protected $stucture;

    /** @var int|null */
    protected $type;

    /** @var string */
    protected $typeName;

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
    public function getParentUuid(): ?string
    {
        return $this->parentUuid;
    }

    /**
     * @param null|string $parentUuid
     */
    public function setParentUuid(?string $parentUuid): void
    {
        $this->parentUuid = $parentUuid;
    }

    /**
     * @return null|string
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param null|string $number
     */
    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return null|string
     */
    public function getBuild(): ?string
    {
        return $this->build;
    }

    /**
     * @param null|string $build
     */
    public function setBuild(?string $build): void
    {
        $this->build = $build;
    }

    /**
     * @return null|string
     */
    public function getStucture(): ?string
    {
        return $this->stucture;
    }

    /**
     * @param null|string $stucture
     */
    public function setStucture(?string $stucture): void
    {
        $this->stucture = $stucture;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     */
    public function setTypeName(string $typeName): void
    {
        $this->typeName = $typeName;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @param $fields
     *
     * @return static
     */
    public static function mapFields($fields)
    {
        $o = new static();

        $o->setId($fields['houseid']);
        $o->setUuid($fields['houseguid']);
        $o->setParentUuid($fields['aoguid']);
        $o->setNumber($fields['housenum']);
        $o->setType($fields['eststat']);
        $o->setTypeName($fields['estname']);
        $o->setBuild($fields['buildnum']);
        $o->setStucture($fields['strucnum']);

        return $o;
    }
}
