<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 6/4/17
 * Time: 5:18 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleInstanceSetting
 *
 * @ORM\Table(name="module_instance_setting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleInstanceSettingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ModuleInstanceSetting
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="module_instance_setting_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $moduleInstanceSettingId;

	/**
	 * @var ModuleInstance
	 *
	 * @ORM\ManyToOne(targetEntity="ModuleInstance", inversedBy="moduleInstanceSettings")
	 * @ORM\JoinColumn(name="module_instance_id", referencedColumnName="module_instance_id")
	 */
	protected $moduleInstance;

	/**
	 * @var ModuleSetting
	 *
	 * @ORM\ManyToOne(targetEntity="ModuleSetting", inversedBy="moduleInstanceSettings")
	 * @ORM\JoinColumn(name="module_setting_id", referencedColumnName="module_setting_id")
	 */
	protected $moduleSetting;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="value", type="string", length=255)
	 */
	protected $value;

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

	// TODO: Add module instance settings options

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getModuleInstanceSettingId()
	{
		return $this->moduleInstanceSettingId;
	}

	/**
	 * Set moduleInstanceId
	 *
	 * @param ModuleInstance $moduleInstance
	 *
	 * @return ModuleInstanceSetting
	 */
	public function setModuleInstance($moduleInstance)
	{
		$moduleInstance->assignedToModuleSetting($this);
		$this->moduleInstance = $moduleInstance;

		return $this;
	}

	/**
	 * @return ModuleInstance
	 */
	public function getModuleInstance()
	{
		return $this->moduleInstance;
	}

	/**
	 * @return ModuleSetting
	 */
	public function getModuleSetting()
	{
		return $this->moduleSetting;
	}

	/**
	 * @param ModuleSetting $moduleSetting
	 */
	public function setModuleSetting($moduleSetting)
	{
		$moduleSetting->assignedToModuleInstanceSetting($this);
		$this->moduleSetting = $moduleSetting;
	}

	/**
	 * Set value
	 *
	 * @param string $value
	 *
	 * @return ModuleInstanceSetting
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
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 *
	 * @return ModuleInstanceSetting
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
	 * @return ModuleInstanceSetting
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
}