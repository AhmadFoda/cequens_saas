<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Adapter
 *
 * @ORM\Table(name="adapter")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdapterRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Adapter
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="adapter_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $adapterId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string")
	 */
	protected $name;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	protected $isActive = 1;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime")
	 */
	protected $createdAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="updated_at", type="datetime", nullable=true)
	 */
	protected $updatedAt;

	/**
	 * @var AdapterType
	 *
	 * @ORM\ManyToOne(targetEntity="AdapterType", inversedBy="adapters")
	 * @ORM\JoinColumn(name="adapter_type_id", referencedColumnName="adapter_type_id")
	 */
	protected $adapterType;

	/**
	 * @var AdapterSetting[]
	 *
	 * @ORM\OneToMany(targetEntity="AdapterSetting", mappedBy="adapter")
	 * @ORM\JoinColumn(name="adapter_setting_id", referencedColumnName="adapter_setting_id")
	 */
	protected $adapterSettings;

	/**
	 * @var ModuleInstance[]
	 *
	 * @ORM\OneToMany(targetEntity="ModuleInstance", mappedBy="adapter")
	 * @ORM\JoinColumn(name="module_instance_id", referencedColumnName="module_instance_id")
	 */
	protected $moduleInstances;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text", length=65532, nullable=true)
	 */
	protected $description;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="export_output_file", type="boolean", nullable=true)
	 */
	protected $exportOutputFile;

    /**
     * @var FileImport[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Workflow", mappedBy="adapter")
     * @ORM\JoinColumn(name="workflow_id", referencedColumnName="workflow_id")
     */
    protected $workflows;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="CequensBundle\Entity\User", inversedBy="adapters")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

	/**
	 * @var text
	 *
	 * @ORM\Column(name="adapter_json", type="text", nullable=true)
	 *
	 */
    protected $adapterJson;

	public function __construct()
	{
		$this->adapterSettings  = new ArrayCollection();
		$this->moduleInstances  = new ArrayCollection();
        $this->workflows      = new ArrayCollection();
	}

	/**
	 * @ORM\PrePersist
	 */
	public function onPrePersist()
	{
		if ($this->getCreatedAt() === null) {
			$this->setCreatedAt(new \DateTime());
		}
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function onPreUpdate()
	{
		$this->setUpdatedAt(new \DateTime());
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getAdapterId()
	{
		return $this->adapterId;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param AdapterType $adapterType
	 */
	public function setAdapterType($adapterType)
	{
		$adapterType->assignedToAdapter($this);
		$this->adapterType = $adapterType;
	}

	/**
	 * @return AdapterType
	 */
	public function getAdapterType()
	{
		return $this->adapterType;
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

	/**
	 * Set isActive
	 *
	 * @param boolean $isActive
	 *
	 * @return Adapter
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;

		return $this;
	}

	/**
	 * Get isActive
	 *
	 * @return bool
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

	/**
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 *
	 * @return Adapter
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
	 * @return Adapter
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
	 * @param AdapterSetting $adapterSetting
	 */
	public function assignedToAdapterSetting(AdapterSetting $adapterSetting)
	{
		$this->adapterSettings[] = $adapterSetting;
	}

	/**
	 * @param ModuleInstance $moduleInstance \
	 */
	public function assignedToModuleInstance(ModuleInstance $moduleInstance)
	{
		$this->moduleInstances[] = $moduleInstance;
	}

	/**
	 * @return ArrayCollection|ModuleInstance[]
	 */
	public function getModuleInstances()
	{
		return $this->moduleInstances;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return int
	 */
	public function getExportOutputFile()
	{
		return $this->exportOutputFile;
	}

	/**
	 * @param int $exportOutputFile
	 */
	public function setExportOutputFile($exportOutputFile)
	{
		$this->exportOutputFile = $exportOutputFile;
	}


    /**
     * @param FileImport $fileImport
     */
    public function assignedToFileImport(Workflow $fileImport)
    {
        $this->workflows[] = $fileImport;
    }

	/**
	 * @return text
	 */
	public function getAdapterJson()
	{
		return $this->adapterJson;
	}

	/**
	 * @param text $adapterJson
	 */
	public function setAdapterJson($adapterJson)
	{
		$this->adapterJson = $adapterJson;
	}


}

