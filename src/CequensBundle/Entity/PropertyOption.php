<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PropertyOption
 *
 * @ORM\Table(name="property_option")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\PropertyOptionRepository")
 */
class PropertyOption
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
     * @ORM\Column(name="property_id", type="integer")
     */
    private $propertyId;

    /**
     * @var string
     *
     * @ORM\Column(name="property_option_name", type="string", length=255)
     */
    private $propertyOptionName;

    /**
     * @var string
     *
     * @ORM\Column(name="property_option_value", type="string", length=255)
     */
    private $propertyOptionValue;


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
     * Set propertyId
     *
     * @param integer $propertyId
     *
     * @return PropertyOption
     */
    public function setPropertyId($propertyId)
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    /**
     * Get propertyId
     *
     * @return int
     */
    public function getPropertyId()
    {
        return $this->propertyId;
    }

    /**
     * Set propertyOptionName
     *
     * @param string $propertyOptionName
     *
     * @return PropertyOption
     */
    public function setPropertyOptionName($propertyOptionName)
    {
        $this->propertyOptionName = $propertyOptionName;

        return $this;
    }

    /**
     * Get propertyOptionName
     *
     * @return string
     */
    public function getPropertyOptionName()
    {
        return $this->propertyOptionName;
    }

    /**
     * Set propertyOptionValue
     *
     * @param string $propertyOptionValue
     *
     * @return PropertyOption
     */
    public function setPropertyOptionValue($propertyOptionValue)
    {
        $this->propertyOptionValue = $propertyOptionValue;

        return $this;
    }

    /**
     * Get propertyOptionValue
     *
     * @return string
     */
    public function getPropertyOptionValue()
    {
        return $this->propertyOptionValue;
    }
}

