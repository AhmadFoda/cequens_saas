<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bot
 *
 * @ORM\Table(name="bot")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\BotRepository")
 */
class Bot
{
    /**
     * @var int
     *
     * @ORM\Column(name="bot_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="bot_name", type="string", length=255)
     */
    private $botName;

    /**
     * @var string
     *
     * @ORM\Column(name="bot_description", type="text")
     */
    private $botDescription;

    /**
     * @var int
     *
     * @ORM\Column(name="bot_type", type="integer")
     */
    private $botType;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set botName.
     *
     * @param string $botName
     *
     * @return Bot
     */
    public function setBotName($botName)
    {
        $this->botName = $botName;

        return $this;
    }

    /**
     * Get botName.
     *
     * @return string
     */
    public function getBotName()
    {
        return $this->botName;
    }

    /**
     * Set botDescription.
     *
     * @param string $botDescription
     *
     * @return Bot
     */
    public function setBotDescription($botDescription)
    {
        $this->botDescription = $botDescription;

        return $this;
    }

    /**
     * Get botDescription.
     *
     * @return string
     */
    public function getBotDescription()
    {
        return $this->botDescription;
    }

    /**
     * Set botType.
     *
     * @param int $botType
     *
     * @return Bot
     */
    public function setBotType($botType)
    {
        $this->botType = $botType;

        return $this;
    }

    /**
     * Get botType.
     *
     * @return int
     */
    public function getBotType()
    {
        return $this->botType;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Bot
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Bot
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }


}
