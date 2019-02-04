<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleSetting
 *
 * @ORM\Table(name="module_setting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleSettingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ModuleSetting
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="module_setting_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $moduleSettingId;

	/**
	 * @var Module
	 *
	 * @ORM\ManyToOne(targetEntity="Module", inversedBy="moduleSettings")
	 * @ORM\JoinColumn(name="module_id", referencedColumnName="module_id")
	 */
	protected $module;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	protected $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="display_name", type="string", length=255)
	 */
	protected $displayName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="value", type="string", length=255)
	 */
	protected $value;

	/**
	 * @var ModuleSettingType
	 *
	 * @ORM\ManyToOne(targetEntity="ModuleSettingType", inversedBy="moduleSettings")
	 * @ORM\JoinColumn(name="module_setting_type_id", referencedColumnName="module_setting_type_id")
	 */
	protected $moduleSettingType;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_required", type="boolean")
	 */
	protected $isRequired = true;

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
	 * @var ModuleSettingOption[]
	 *
	 * @ORM\OneToMany(targetEntity="ModuleSettingOption", mappedBy="moduleSetting")
	 * @ORM\JoinColumn(name="module_setting_option_id", referencedColumnName="module_setting_option_id")
	 */
	protected $moduleSettingOptions;

	/**
	 * @var ModuleInstanceSetting[]
	 *
	 * @ORM\OneToMany(targetEntity="ModuleInstanceSetting", mappedBy="moduleSetting")
	 * @ORM\JoinColumn(name="module_instance_setting_id", referencedColumnName="module_instance_setting_id")
	 */
	protected $moduleInstanceSettings;

	/**
	 * ModuleSetting constructor.
	 */
	public function __construct()
	{
		$this->moduleSettingOptions   = new ArrayCollection();
		$this->moduleInstanceSettings = new ArrayCollection();
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
	public function getModuleSettingId()
	{
		return $this->moduleSettingId;
	}

	/**
	 * Set moduleId
	 *
	 * @param Module $module
	 *
	 * @return ModuleSetting
	 */
	public function setModule($module)
	{
		$module->assignedToModuleSetting($this);
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
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return ModuleSetting
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
	 * Set value
	 *
	 * @param string $value
	 *
	 * @return ModuleSetting
	 */
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->isRequired;
	}

	/**
	 * @param bool $isRequired
	 */
	public function setIsRequired($isRequired)
	{
		$this->isRequired = $isRequired;
	}

	/**
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 *
	 * @return ModuleSetting
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
	 * @param string $updatedAt
	 *
	 * @return ModuleSetting
	 */
	public function setUpdatedAt($updatedAt)
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * Get updatedAt
	 *
	 * @return string
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	/**
	 * @return ModuleSettingType
	 */
	public function getModuleSettingType()
	{
		return $this->moduleSettingType;
	}

	/**
	 * @param ModuleSettingType $moduleSettingType
	 */
	public function setModuleSettingType($moduleSettingType)
	{
		$moduleSettingType->assignedToModuleSetting($this);
		$this->moduleSettingType = $moduleSettingType;
	}

	/**
	 * @param ModuleSettingOption $moduleSettingOption
	 */
	public function assignedToModuleSettingOption(ModuleSettingOption $moduleSettingOption)
	{
		$this->moduleSettingOptions[] = $moduleSettingOption;
	}

	/**
	 * @param ModuleInstanceSetting $moduleInstanceSetting
	 */
	public function assignedToModuleInstanceSetting(ModuleInstanceSetting $moduleInstanceSetting)
	{
		$this->moduleInstanceSettings[] = $moduleInstanceSetting;
	}

	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * @param string $displayName
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}
}

