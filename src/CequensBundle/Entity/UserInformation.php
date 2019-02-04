<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserInformation
 *
 * @ORM\Table(name="user_information")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\UserInformationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserInformation
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
     * @ORM\Column(name="userInformationKey", type="string", length=255)
     */
    private $userInformationKey;

    /**
     * @var string
     *
     * @ORM\Column(name="userInformationValue", type="string", length=255)
     */
    private $userInformationValue;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="CequensBundle\Entity\User", inversedBy="userInformation")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


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
     * Set userInformationKey.
     *
     * @param string $userInformationKey
     *
     * @return UserInformation
     */
    public function setUserInformationKey($userInformationKey)
    {
        $this->userInformationKey = $userInformationKey;

        return $this;
    }

    /**
     * Get userInformationKey.
     *
     * @return string
     */
    public function getUserInformationKey()
    {
        return $this->userInformationKey;
    }

    /**
     * Set userInformationValue.
     *
     * @param string $userInformationValue
     *
     * @return UserInformation
     */
    public function setUserInformationValue($userInformationValue)
    {
        $this->userInformationValue = $userInformationValue;

        return $this;
    }

    /**
     * Get userInformationValue.
     *
     * @return string
     */
    public function getUserInformationValue()
    {
        return $this->userInformationValue;
    }

    /**
     * Set userId
     *
     * @param User $user
     *
     * @return Adapter
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}
