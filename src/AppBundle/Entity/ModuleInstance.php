<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use AppBundle\Enumeration\EnumExecutionStatus;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * ModuleInstance
 *
 * @ORM\Table(name="module_instance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleInstanceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ModuleInstance
{
	/**
	 * @var int
	 * @ORM\Column(name="module_instance_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $moduleInstanceId;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="execute_order", type="integer")
	 */
	protected $executeOrder;

	/**
	 * @var Adapter
	 *
	 * @ORM\ManyToOne(targetEntity="Adapter", inversedBy="moduleInstances")
	 * @ORM\JoinColumn(name="adapter_id", referencedColumnName="adapter_id")
	 */
	protected $adapter;

	/**
	 * @var Module
	 *
	 * @ORM\ManyToOne(targetEntity="Module", inversedBy="moduleInstances")
	 * @ORM\JoinColumn(name="module_id", referencedColumnName="module_id")
	 */
	protected $module;

	/**
	 * @var ModuleInstanceSetting[]
	 * @ORM\OneToMany(targetEntity="ModuleInstanceSetting", mappedBy="moduleInstance")
	 * @ORM\JoinColumn(name="module_instance_setting_id", referencedColumnName="module_instance_setting_id")
	 */
	protected $moduleInstanceSettings;

	/**
	 * @var ModuleInstanceSetting[]
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\ModuleInstanceConnection", mappedBy="moduleInstance")
	 * @ORM\JoinColumn(name="module_instance_connection_id", referencedColumnName="module_instance_connection_id")
	 */
	protected $moduleInstanceConnections;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="continue_on_error", type="integer")
	 */
	protected $continueOnError = false;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="continue_on_warning", type="integer")
	 */
	protected $continueOnWarning = true;

	/**
 	 * @var bool
	 *
	 * @ORM\Column(name="is_active", type="boolean", options={"default" : 1})
	 */
	protected $isActive = 1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	protected $name;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="updated_at", type="datetime", nullable=true)
	 */
	protected $updatedAt;

	/**
	 * ModuleInstance constructor.
	 */
	public function __construct()
	{
		$this->moduleInstanceSettings  = new ArrayCollection();
		$this->moduleInstanceConnections = new ArrayCollection();
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
	public function getModuleInstanceId()
	{
		return $this->moduleInstanceId;
	}

	/**
	 * @return int
	 */
	public function getExecuteOrder()
	{
		return $this->executeOrder;
	}

	/**
	 * @param int $executeOrder
	 */
	public function setExecuteOrder($executeOrder)
	{
		$this->executeOrder = $executeOrder;
	}

	/**
	 * Set adapter
	 *
	 * @param Adapter $adapter
	 *
	 * @return ModuleInstance
	 */
	public function setAdapter($adapter)
	{
		$adapter->assignedToModuleInstance($this);
		$this->adapter = $adapter;

		return $this;
	}

	/**
	 * Get adapterId
	 *
	 * @return Adapter
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Set moduleId
	 *
	 * @param Module $module
	 *
	 * @return ModuleInstance
	 */
	public function setModule($module)
	{
		$module->assignedToModuleInstance($this);
		$this->module = $module;

		return $this;
	}

	/**
	 * Get moduleId
	 *
	 * @return Module
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * @param ModuleInstanceSetting $moduleInstanceSetting
	 */
	public function assignedToModuleSetting(ModuleInstanceSetting $moduleInstanceSetting)
	{
		$this->moduleInstanceSettings[] = $moduleInstanceSetting;
	}

	/**
	 * @param ModuleInstanceConnection $moduleInstanceConnection
	 */
	public function assignedToModuleConnection(ModuleInstanceConnection $moduleInstanceConnection)
	{
		$this->moduleInstanceConnections[] = $moduleInstanceConnection;
	}

	/**
	* @return ModuleInstanceSetting[]
	*/
	public function getModuleInstanceSettings()
	{
		return $this->moduleInstanceSettings;
	}

	/**
	 * @return ModuleInstanceSetting[]
	 */
	public function getModuleInstanceConnections()
	{
		return $this->moduleInstanceConnections;
	}



	/**
	 * @return int
	 */
	public function getContinueOnError()
	{
		return $this->continueOnError;
	}

	/**
	 * @param int $continueOnError
	 */
	public function setContinueOnError($continueOnError)
	{
		$this->continueOnError = $continueOnError;
	}

	/**
	 * @return int
	 */
	public function getContinueOnWarning()
	{
		return $this->continueOnWarning;
	}

	/**
	 * @param int $continueOnWarning
	 */
	public function setContinueOnWarning($continueOnWarning)
	{
		$this->continueOnWarning = $continueOnWarning;
	}

	/**
	 * Set isActive
	 *
	 * @param boolean $isActive
	 *
	 * @return User
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
	public function isActive()
	{
		return $this->isActive;
	}


	/**
	 * Set updatedAt
	 *
	 * @param \DateTime $updatedAt
	 *
	 * @return ModuelInstance
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
}

