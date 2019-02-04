<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationProperty
 *
 * @ORM\Table(name="application_property")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\ApplicationPropertyRepository")
 */
class ApplicationProperty
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
     * @ORM\Column(name="application_id", type="integer")
     */
    private $applicationId;

    /**
     * @var int
     *
     * @ORM\Column(name="property_id", type="integer")
     */
    private $propertyId;

    /**
     * @var string
     *
     * @ORM\Column(name="property_value", type="text")
     */
    private $propertyValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


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
     * Set applicationId
     *
     * @param integer $applicationId
     *
     * @return ApplicationProperty
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * Get applicationId
     *
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Set propertyId
     *
     * @param integer $propertyId
     *
     * @return ApplicationProperty
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
     * Set propertyValue
     *
     * @param string $propertyValue
     *
     * @return ApplicationProperty
     */
    public function setPropertyValue($propertyValue)
    {
        $this->propertyValue = $propertyValue;

        return $this;
    }

    /**
     * Get propertyValue
     *
     * @return string
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ApplicationProperty
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ApplicationProperty
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

