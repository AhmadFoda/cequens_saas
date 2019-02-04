<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BotUser
 *
 * @ORM\Table(name="bot_user")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\BotUserRepository")
 */
class BotUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="bot_user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="bot_id", type="integer", nullable=true)
     */
    private $botId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_fname", type="string", length=255, nullable=true)
     */
    private $userFname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_lname", type="string", length=255, nullable=true)
     */
    private $userLname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_fullname", type="string", length=255, nullable=true)
     */
    private $userFullname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_email", type="string", length=255, nullable=true)
     */
    private $userEmail;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_mobile", type="string", length=255, nullable=true)
     */
    private $userMobile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_fb_id", type="string", length=255, nullable=true)
     */
    private $userFbId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_avatar", type="string", length=255, nullable=true)
     */
    private $userAvatar;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


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
     * Set botId.
     *
     * @param int|null $botId
     *
     * @return BotUser
     */
    public function setBotId($botId = null)
    {
        $this->botId = $botId;

        return $this;
    }

    /**
     * Get botId.
     *
     * @return int|null
     */
    public function getBotId()
    {
        return $this->botId;
    }

    /**
     * Set userFname.
     *
     * @param string|null $userFname
     *
     * @return BotUser
     */
    public function setUserFname($userFname = null)
    {
        $this->userFname = $userFname;

        return $this;
    }

    /**
     * Get userFname.
     *
     * @return string|null
     */
    public function getUserFname()
    {
        return $this->userFname;
    }

    /**
     * Set userLname.
     *
     * @param string|null $userLname
     *
     * @return BotUser
     */
    public function setUserLname($userLname = null)
    {
        $this->userLname = $userLname;

        return $this;
    }

    /**
     * Get userLname.
     *
     * @return string|null
     */
    public function getUserLname()
    {
        return $this->userLname;
    }

    /**
     * Set userFullname.
     *
     * @param string|null $userFullname
     *
     * @return BotUser
     */
    public function setUserFullname($userFullname = null)
    {
        $this->userFullname = $userFullname;

        return $this;
    }

    /**
     * Get userFullname.
     *
     * @return string|null
     */
    public function getUserFullname()
    {
        return $this->userFullname;
    }

    /**
     * Set userEmail.
     *
     * @param string|null $userEmail
     *
     * @return BotUser
     */
    public function setUserEmail($userEmail = null)
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Get userEmail.
     *
     * @return string|null
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set userMobile.
     *
     * @param string|null $userMobile
     *
     * @return BotUser
     */
    public function setUserMobile($userMobile = null)
    {
        $this->userMobile = $userMobile;

        return $this;
    }

    /**
     * Get userMobile.
     *
     * @return string|null
     */
    public function getUserMobile()
    {
        return $this->userMobile;
    }

    /**
     * Set userFbId.
     *
     * @param string|null $userFbId
     *
     * @return BotUser
     */
    public function setUserFbId($userFbId = null)
    {
        $this->userFbId = $userFbId;

        return $this;
    }

    /**
     * Get userFbId.
     *
     * @return string|null
     */
    public function getUserFbId()
    {
        return $this->userFbId;
    }

    /**
     * Set userAvatar.
     *
     * @param string|null $userAvatar
     *
     * @return BotUser
     */
    public function setUserAvatar($userAvatar = null)
    {
        $this->userAvatar = $userAvatar;

        return $this;
    }

    /**
     * Get userAvatar.
     *
     * @return string|null
     */
    public function getUserAvatar()
    {
        return $this->userAvatar;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return BotUser
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime|null $updatedAt
     *
     * @return BotUser
     */
    public function setUpdatedAt($updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
