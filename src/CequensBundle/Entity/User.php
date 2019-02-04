<?php

namespace CequensBundle\Entity;

use AppBundle\Entity\Adapter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\UserRepository")
 */
class User extends \FOS\UserBundle\Model\User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

	/**
	 * Adapter[]
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Adapter", mappedBy="user")
	 * @ORM\JoinColumn(name="adapter_id", referencedColumnName="adapter_id")
	 */
	protected $adapters;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
	protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
	protected $lastName;

    /**
     * Adapter[]
     *
     * @ORM\OneToMany(targetEntity="CequensBundle\Entity\UserInformation", mappedBy="user")
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="user_info_id")
     */
	protected $userInformation;

    public function __construct()
    {
    	parent::__construct();
	    $this->adapters = new ArrayCollection();
	    $this->userInformation = new ArrayCollection();

    }

	/**
	 * @param Adapter $adapter
	 */
	public function assignedToAdapter(Adapter $adapter)
	{
		$this->adapters[] = $adapter;
	}

    /**
     * @param Adapter $adapter
     */
    public function assignedToUserInformation(UserInformation $userInformation)
    {
        $this->userInformation[] = $userInformation;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }


}

