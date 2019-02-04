<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application
 *
 * @ORM\Table(name="application")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\ApplicationRepository")
 */
class Application
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
     * @ORM\Column(name="application_name", type="string", length=255)
     */
    private $applicationName;

    /**
     * @var string
     *
     * @ORM\Column(name="application_description", type="text")
     */
    private $applicationDescription;

    /**
     * @var int
     *
     * @ORM\Column(name="application_type", type="integer")
     */
    private $applicationType;

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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="application_token", type="text")
	 */
	private $applicationToken;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="adapter_id", type="integer", nullable=true)
	 */
	private $adapterId;

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
     * Set applicationName
     *
     * @param string $applicationName
     *
     * @return Application
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;

        return $this;
    }

    /**
     * Get applicationName
     *
     * @return string
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * Set applicationDescription
     *
     * @param string $applicationDescription
     *
     * @return Application
     */
    public function setApplicationDescription($applicationDescription)
    {
        $this->applicationDescription = $applicationDescription;

        return $this;
    }

    /**
     * Get applicationDescription
     *
     * @return string
     */
    public function getApplicationDescription()
    {
        return $this->applicationDescription;
    }

    /**
     * Set applicationType
     *
     * @param integer $applicationType
     *
     * @return Application
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;

        return $this;
    }

    /**
     * Get applicationType
     *
     * @return int
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Application
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
     * @return Application
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

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Application
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

	/**
	 * @return string
	 */
	public function getApplicationToken()
	{
		return $this->applicationToken;
	}

	/**
	 * @param string $applicationToken
	 */
	public function setApplicationToken($applicationToken)
	{
		$this->applicationToken = $applicationToken;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getAdapterId()
	{
		return $this->adapterId;
	}

	/**
	 * @param int $adapterId
	 */
	public function setAdapterId($adapterId)
	{
		$this->adapterId = $adapterId;
	}
}

