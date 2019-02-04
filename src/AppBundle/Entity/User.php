<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use AppBundle\Entity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
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
     * @ORM\OneToMany(targetEntity="Adapter", mappedBy="user")
     * @ORM\JoinColumn(name="adapter_id", referencedColumnName="adapter_id")
     */
    protected $adapters;

    /**
     * @var FileImports[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Workflow", mappedBy="admin")
     */
    protected $workflows;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->adapters = new ArrayCollection();
    }

    /**
     * @param Adapter $adapter
     */
    public function assignedToAdapter(Adapter $adapter)
    {
        $this->adapters[] = $adapter;
    }

    /**
     * @param Workflow $fileImport
     */
    public function assignedToFileImport($fileImport)
    {
        $this->workflows[] = $fileImport;
    }

}

