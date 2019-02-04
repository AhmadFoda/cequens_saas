<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleSettingOption
 *
 * @ORM\Table(name="module_setting_option")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleSettingOptionRepository")
 */
class ModuleSettingOption
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="module_setting_option_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $moduleSettingOptionId;

	/**
	 * @var ModuleSetting
	 *
	 * @ORM\ManyToOne(targetEntity="ModuleSetting", inversedBy="moduleSettingOptions")
	 * @ORM\JoinColumn(name="module_setting_id", referencedColumnName="module_setting_id")
	 */
	protected $moduleSetting;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="option_value", type="string", length=255)
	 */
	protected $value;

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getModuleSettingOptionId()
	{
		return $this->moduleSettingOptionId;
	}

	/**
	 * Set moduleSettingId
	 *
	 * @param ModuleSetting $moduleSetting
	 *
	 * @return ModuleSettingOption
	 */
	public function setModuleSetting($moduleSetting)
	{
		$moduleSetting->assignedToModuleSettingOption($this);
		$this->moduleSetting = $moduleSetting;

		return $this;
	}

	/**
	 * Get moduleSettingId
	 *
	 * @return ModuleSetting
	 */
	public function getModuleSetting()
	{
		return $this->moduleSetting;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
}

