<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 12/19/2017
 * Time: 12:47 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleInstanceSetting
 *
 * @ORM\Table(name="workflow_module_instance_setting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkflowModuleInstanceSettingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class WorkflowModuleInstanceSetting
{
    /**
     * @var int
     *
     * @ORM\Column(name="workflow_module_instance_setting_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $workflowModuleInstanceSettingId;

    /**
     * @var ModuleInstance
     *
     * @ORM\ManyToOne(targetEntity="WorkflowModuleInstance", inversedBy="workflowModuleInstanceSettings")
     * @ORM\JoinColumn(name="workflow_module_instance_id", referencedColumnName="workflow_module_instance_id")
     */
    protected $workflowModuleInstance;

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
    public function getWorkflowModuleInstanceSettingId()
    {
        return $this->workflowModuleInstanceSettingId;
    }

    /**
     * Set moduleInstanceId
     *
     * @param WorkflowModuleInstance $moduleInstance
     *
     * @return WorkflowModuleInstanceSetting
     */
    public function setWorkflowModuleInstance($moduleInstance)
    {
        //$moduleInstance->assignedToModuleSetting($this);
        $this->workflowModuleInstance = $moduleInstance;

        return $this;
    }

    /**
     * @return ModuleInstance
     */
    public function getWorkflowModuleInstance()
    {
        return $this->workflowModuleInstance;
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
        //$moduleSetting->assignedToModuleInstanceSetting($this);
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