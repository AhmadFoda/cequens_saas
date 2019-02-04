<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 *
 * @ORM\Table(name="module")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Module
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="module_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $moduleId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	protected $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="processor_class_name", type="string", length=255)
	 */
	protected $processorClassName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255, nullable=true)
	 */
	protected $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="operates_on", type="string", length=255)
	 */
	protected $operatesOn;

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
     * @var text
     *
     * @ORM\Column(name="ui_settings", type="text", nullable=true)
     */
    protected $uiSettings;

    /**
     * @var string
     *
     * @ORM\Column(name="ui_name", type="string", length=255, nullable=true)
     */
    protected $uiName;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $moduleType;

    /**
	 * @var ModuleInstance[]
	 *
	 * @ORM\OneToMany(targetEntity="ModuleInstance", mappedBy="module")
	 * @ORM\JoinColumn(name="module_instance_id", referencedColumnName="module_instance_id")
	 */
	protected $moduleInstances;

	/**
	 * @var ModuleSetting[]
	 *
	 * @ORM\OneToMany(targetEntity="ModuleSetting", mappedBy="module")
	 * @ORM\JoinColumn(name="module_setting_id", referencedColumnName="module_setting_id")
	 */
	protected $moduleSettings;

	public function __construct()
	{
		$this->moduleInstances = new ArrayCollection();
		$this->moduleSettings  = new ArrayCollection();
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
	public function getModuleId()
	{
		return $this->moduleId;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Module
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getProcessorClassName()
	{
		return $this->processorClassName;
	}

	/**
	 * @param string $processorClassName
	 */
	public function setProcessorClassName($processorClassName)
	{
		$this->processorClassName = $processorClassName;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Module
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set operatesOn
	 *
	 * @param string $operatesOn
	 *
	 * @return Module
	 */
	public function setOperatesOn($operatesOn)
	{
		$this->operatesOn = $operatesOn;

		return $this;
	}

	/**
	 * Get operatesOn
	 *
	 * @return string
	 */
	public function getOperatesOn()
	{
		return $this->operatesOn;
	}

	/**
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 *
	 * @return Module
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
	 * @return Module
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
	 * @param ModuleInstance $moduleInstance
	 */
	public function assignedToModuleInstance(ModuleInstance $moduleInstance)
	{
		$this->moduleInstances[] = $moduleInstance;
	}

	/**
	 * @param ModuleSetting $moduleSetting
	 */
	public function assignedToModuleSetting(ModuleSetting $moduleSetting)
	{
		$this->moduleSettings[] = $moduleSetting;
	}

    /**
     * @return string
     */
    public function getUiSettings()
    {
        return $this->uiSettings;
    }

    /**
     * @param string $uiSettings
     */
    public function setUiSettings($uiSettings)
    {
        $this->uiSettings = $uiSettings;

        return $this;
    }

    /**
     * @return string
     */
    public function getUiName()
    {
        return $this->uiName;
    }

    /**
     * @param string $uiName
     */
    public function setUiName($uiName)
    {
        $this->uiName = $uiName;

        return $this;
    }

    /**
     * @return int
     */
    public function getModuleType(): int
    {
        return $this->moduleType;
    }

    /**
     * @param int $moduleType
     */
    public function setModuleType(int $moduleType)
    {
        $this->moduleType = $moduleType;

        return $this;
    }


}
