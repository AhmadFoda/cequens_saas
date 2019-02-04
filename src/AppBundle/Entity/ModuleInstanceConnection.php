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
 * ModuleInstanceConnection
 *
 * @ORM\Table(name="module_instance_connection")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleInstanceConnectionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ModuleInstanceConnection
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="module_instance_connection_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $moduleInstanceConnectionId;

	/**
	 * @var ModuleInstance
	 *
	 * @ORM\ManyToOne(targetEntity="ModuleInstance", inversedBy="moduleInstanceConnections")
	 * @ORM\JoinColumn(name="module_instance_id", referencedColumnName="module_instance_id")
	 */
	protected $moduleInstance;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="target_module_instance_id", type="integer")
	 */
	protected $targetModuleInstanceId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="condition_type", type="string", length=255)
	 */
	protected $conditionType;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="condition_key", type="string", length=255)
	 */
	protected $conditionKey;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="condition_value", type="string", length=255)
	 */
	protected $conditionValue;

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
	 * @return string
	 */
	public function getTargetModuleInstanceId()
	{
		return $this->targetModuleInstanceId;
	}

	/**
	 * @param string $targetModuleInstanceId
	 */
	public function setTargetModuleInstanceId($targetModuleInstanceId)
	{
		$this->targetModuleInstanceId = $targetModuleInstanceId;
	}

	// TODO: Add module instance settings options



	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getModuleInstanceConnectionId()
	{
		return $this->moduleInstanceConnectionId;
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
		$moduleInstance->assignedToModuleConnection($this);
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
	 * @return string
	 */
	public function getConditionType()
	{
		return $this->conditionType;
	}

	/**
	 * @param string $conditionType
	 */
	public function setConditionType($conditionType)
	{
		$this->conditionType = $conditionType;
	}

	/**
	 * @return string
	 */
	public function getConditionKey()
	{
		return $this->conditionKey;
	}

	/**
	 * @param string $conditionKey
	 */
	public function setConditionKey($conditionKey)
	{
		$this->conditionKey = $conditionKey;
	}

	/**
	 * @return string
	 */
	public function getConditionValue()
	{
		return $this->conditionValue;
	}

	/**
	 * @param string $conditionValue
	 */
	public function setConditionValue($conditionValue)
	{
		$this->conditionValue = $conditionValue;
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