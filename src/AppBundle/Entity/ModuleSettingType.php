<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleSettingType
 *
 * @ORM\Table(name="module_setting_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleSettingTypeRepository")
 */
class ModuleSettingType
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="module_setting_type_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $moduleSettingTypeId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type_name", type="string", length=255)
	 */
	protected $typeName;

	/**
	 * @var ModuleSetting[]
	 *
	 * @ORM\OneToMany(targetEntity="ModuleSetting", mappedBy="moduleSettingType")
	 * @ORM\JoinColumn(name="module_setting_id", referencedColumnName="module_setting_id")
	 */
	protected $moduleSettings;

	/**
	 * ModuleSettingType constructor.
	 */
	public function __construct()
	{
		$this->moduleSettings = new ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getModuleSettingTypeId()
	{
		return $this->moduleSettingTypeId;
	}

	/**
	 * Set type
	 *
	 * @param string $typeName
	 *
	 * @return ModuleSettingType
	 */
	public function setTypeName($typeName)
	{
		$this->typeName = $typeName;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getTypeName()
	{
		return $this->typeName;
	}

	/**
	 * @return ModuleSetting[]
	 */
	public function getModuleSettings()
	{
		return $this->moduleSettings;
	}

	/**
	 * @param ModuleSetting[] $moduleSettings
	 */
	public function setModuleSettings($moduleSettings)
	{
		$this->moduleSettings = $moduleSettings;
	}

	/**
	 * @param ModuleSetting $moduleSetting
	 */
	public function assignedToModuleSetting(ModuleSetting $moduleSetting)
	{
		$this->moduleSettings[] = $moduleSetting;
	}
}

