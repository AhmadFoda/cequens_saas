<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Property
 *
 * @ORM\Table(name="property")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\PropertyRepository")
 */
class Property
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
     * @var int
     *
     * @ORM\Column(name="property_group_id", type="integer")
     */
    private $propertyGroupId;

    /**
     * @var string
     *
     * @ORM\Column(name="property_name", type="string", length=255)
     */
    private $propertyName;

    /**
     * @var string
     *
     * @ORM\Column(name="property_display_name", type="string", length=255)
     */
    private $propertyDisplayName;

    /**
     * @var int
     *
     * @ORM\Column(name="property_type", type="integer")
     */
    private $propertyType;

    /**
     * @var string
     *
     * @ORM\Column(name="property_default_value", type="string", length=255)
     */
    private $propertyDefaultValue;

    /**
     * @var int
     *
     * @ORM\Column(name="property_is_required", type="integer")
     */
    private $propertyIsRequired;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="property_created_at", type="datetime")
     */
    private $propertyCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="property_updated_at", type="datetime")
     */
    private $propertyUpdatedAt;


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
     * Set propertyGroupId
     *
     * @param integer $propertyGroupId
     *
     * @return Property
     */
    public function setPropertyGroupId($propertyGroupId)
    {
        $this->propertyGroupId = $propertyGroupId;

        return $this;
    }

    /**
     * Get propertyGroupId
     *
     * @return int
     */
    public function getPropertyGroupId()
    {
        return $this->propertyGroupId;
    }

    /**
     * Set propertyName
     *
     * @param string $propertyName
     *
     * @return Property
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    /**
     * Get propertyName
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Set propertyDisplayName
     *
     * @param string $propertyDisplayName
     *
     * @return Property
     */
    public function setPropertyDisplayName($propertyDisplayName)
    {
        $this->propertyDisplayName = $propertyDisplayName;

        return $this;
    }

    /**
     * Get propertyDisplayName
     *
     * @return string
     */
    public function getPropertyDisplayName()
    {
        return $this->propertyDisplayName;
    }

    /**
     * Set propertyType
     *
     * @param integer $propertyType
     *
     * @return Property
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * Get propertyType
     *
     * @return int
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * Set propertyDefaultValue
     *
     * @param string $propertyDefaultValue
     *
     * @return Property
     */
    public function setPropertyDefaultValue($propertyDefaultValue)
    {
        $this->propertyDefaultValue = $propertyDefaultValue;

        return $this;
    }

    /**
     * Get propertyDefaultValue
     *
     * @return string
     */
    public function getPropertyDefaultValue()
    {
        return $this->propertyDefaultValue;
    }

    /**
     * Set propertyIsRequired
     *
     * @param integer $propertyIsRequired
     *
     * @return Property
     */
    public function setPropertyIsRequired($propertyIsRequired)
    {
        $this->propertyIsRequired = $propertyIsRequired;

        return $this;
    }

    /**
     * Get propertyIsRequired
     *
     * @return int
     */
    public function getPropertyIsRequired()
    {
        return $this->propertyIsRequired;
    }

    /**
     * Set propertyCreatedAt
     *
     * @param \DateTime $propertyCreatedAt
     *
     * @return Property
     */
    public function setPropertyCreatedAt($propertyCreatedAt)
    {
        $this->propertyCreatedAt = $propertyCreatedAt;

        return $this;
    }

    /**
     * Get propertyCreatedAt
     *
     * @return \DateTime
     */
    public function getPropertyCreatedAt()
    {
        return $this->propertyCreatedAt;
    }

    /**
     * Set propertyUpdatedAt
     *
     * @param \DateTime $propertyUpdatedAt
     *
     * @return Property
     */
    public function setPropertyUpdatedAt($propertyUpdatedAt)
    {
        $this->propertyUpdatedAt = $propertyUpdatedAt;

        return $this;
    }

    /**
     * Get propertyUpdatedAt
     *
     * @return \DateTime
     */
    public function getPropertyUpdatedAt()
    {
        return $this->propertyUpdatedAt;
    }
}

