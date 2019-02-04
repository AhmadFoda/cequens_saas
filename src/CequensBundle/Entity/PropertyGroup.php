<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PropertyGroup
 *
 * @ORM\Table(name="property_group")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\PropertyGroupRepository")
 */
class PropertyGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="property_group_name", type="string", length=255)
     */
    private $propertyGroupName;

    /**
     * @var string
     *
     * @ORM\Column(name="property_group_type", type="string", length=255)
     */
    private $propertyGroupType;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set propertyGroupName
     *
     * @param string $propertyGroupName
     *
     * @return PropertyGroup
     */
    public function setPropertyGroupName($propertyGroupName)
    {
        $this->propertyGroupName = $propertyGroupName;

        return $this;
    }

    /**
     * Get propertyGroupName
     *
     * @return string
     */
    public function getPropertyGroupName()
    {
        return $this->propertyGroupName;
    }

    /**
     * Set propertyGroupType
     *
     * @param string $propertyGroupType
     *
     * @return PropertyGroup
     */
    public function setPropertyGroupType($propertyGroupType)
    {
        $this->propertyGroupType = $propertyGroupType;

        return $this;
    }

    /**
     * Get propertyGroupType
     *
     * @return string
     */
    public function getPropertyGroupType()
    {
        return $this->propertyGroupType;
    }
}

