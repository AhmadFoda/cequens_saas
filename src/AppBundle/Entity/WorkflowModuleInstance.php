<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * FileImportModuleInstance
 * @ORM\Table(name="workflow_module_instance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkflowModuleInstanceRepository")
 */
class WorkflowModuleInstance
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="workflow_module_instance_id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $workflowModuleInstanceId;

	/**
	 * @var ModuleInstance
	 *
	 * @ORM\ManyToOne(targetEntity="ModuleInstance")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="module_instance_id", referencedColumnName="module_instance_id")
	 * })
	 */
	private $moduleInstance;

	/**
	 * @var Workflow
	 *
	 * @ORM\ManyToOne(targetEntity="Workflow")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="workflow_id")
	 * })
	 */
	private $workflow;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="execution_status", type="integer", nullable=true)
	 */
	private $executionStatus;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="output_log_path", type="string", length=255, nullable=true)
	 */
	private $outputLogPath;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="error_warning_path", type="string", length=255, nullable=true)
	 */
	private $errorWarningPath;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="errors_count", type="integer", nullable=true)
	 */
	private $errorsCount;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="warnings_count", type="integer", nullable=true)
	 */
	private $warningsCount;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="started_at", type="datetime", nullable=true)
	 */
	private $startedAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="finished_at", type="datetime", nullable=true)
	 */
	private $finishedAt;

    /**
     * @var ModuleInstanceSetting[]
     *
     * @ORM\OneToMany(targetEntity="WorkflowModuleInstanceSetting", mappedBy="workflowModuleInstance")
     * @ORM\JoinColumn(name="workflow_module_instance_setting_id", referencedColumnName="workflow_module_instance_setting_id")
     */
    protected $workflowModuleInstanceSettings;

    /**
     * ModuleInstance constructor.
     */
    public function __construct()
    {
        $this->workflowModuleInstanceSettings  = new ArrayCollection();
    }

    /**
     * @param ModuleInstanceSetting $moduleInstanceSetting
     */
    public function assignedToModuleSetting(WorkflowModuleInstanceSetting $workflowModuleInstanceSetting)
    {
        $this->workflowModuleInstanceSettings[] = $workflowModuleInstanceSetting;
    }

	/**
	 * Get WrokflowModuleInstance
	 *
	 * @return integer
	 */
	public function getWorkflowModuleInstanceId()
	{
		return $this->workflowModuleInstanceId;
	}

	/**
	 * Set executionStatus
	 *
	 * @param integer $executionStatus
	 *
	 * @return WorkflowModuleInstance
	 */
	public function setExecutionStatus($executionStatus)
	{
		$this->executionStatus = $executionStatus;

		return $this;
	}

	/**
	 * Get executionStatus
	 *
	 * @return integer
	 */
	public function getExecutionStatus()
	{
		return $this->executionStatus;
	}

	/**
	 * Set outputLogPath
	 *
	 * @param string $outputLogPath
	 *
	 * @return FileImportModuleInstance
	 */
	public function setOutputLogPath($outputLogPath)
	{
		$this->outputLogPath = $outputLogPath;

		return $this;
	}

	/**
	 * Get outputLogPath
	 *
	 * @return string
	 */
	public function getOutputLogPath()
	{
		return $this->outputLogPath;
	}

	/**
	 * Set errorWarningPath
	 *
	 * @param string $errorWarningPath
	 *
	 * @return FileImportModuleInstance
	 */
	public function setErrorWarningPath($errorWarningPath)
	{
		$this->errorWarningPath = $errorWarningPath;

		return $this;
	}

	/**
	 * Get errorWarningPath
	 *
	 * @return string
	 */
	public function getErrorWarningPath()
	{
		return $this->errorWarningPath;
	}

	/**
	 * Set startedAt
	 *
	 * @param \DateTime $startedAt
	 *
	 * @return FileImportModuleInstance
	 */
	public function setStartedAt($startedAt)
	{
		$this->startedAt = $startedAt;

		return $this;
	}

	/**
	 * Get startedAt
	 *
	 * @return \DateTime
	 */
	public function getStartedAt()
	{
		return $this->startedAt;
	}

	/**
	 * Set finishedAt
	 *
	 * @param \DateTime $finishedAt
	 *
	 * @return FileImportModuleInstance
	 */
	public function setFinishedAt($finishedAt)
	{
		$this->finishedAt = $finishedAt;

		return $this;
	}

	/**
	 * Get finishedAt
	 *
	 * @return \DateTime
	 */
	public function getFinishedAt()
	{
		return $this->finishedAt;
	}

	/**
	 * Set moduleInstance
	 *
	 * @param \AppBundle\Entity\ModuleInstance $moduleInstance
	 *
	 * @return ModuleInstance
	 */
	public function setModuleInstance(\AppBundle\Entity\ModuleInstance $moduleInstance = null)
	{
		$this->moduleInstance = $moduleInstance;

		return $this;
	}

	/**
	 * Get moduleInstance
	 *
	 * @return \AppBundle\Entity\ModuleInstance
	 */
	public function getModuleInstance()
	{
		return $this->moduleInstance;
	}

	/**
	 * Set fileImport
	 *
	 * @param \AppBundle\Entity\Workflow $fileImport
	 *
	 * @return Workflow
	 */
	public function setWorkflow(\AppBundle\Entity\Workflow $workflow = null)
	{
		$this->workflow = $workflow;

		return $this;
	}

	/**
	 * Get fileImport
	 *
	 * @return \AppBundle\Entity\Workflow
	 */
	public function getWorkflow()
	{
		return $this->workflow;
	}

	/**
	 * @return int
	 */
	public function getErrorsCount()
	{
		return $this->errorsCount;
	}

	/**
	 * @param int $errorsCount
	 */
	public function setErrorsCount($errorsCount)
	{
		$this->errorsCount = $errorsCount;
	}

	/**
	 * @return int
	 */
	public function getWarningsCount()
	{
		return $this->warningsCount;
	}

	/**
	 * @param int $warningsCount
	 */
	public function setWarningsCount($warningsCount)
	{
		$this->warningsCount = $warningsCount;
	}

    /**
     * @return WorkflowModuleInstance[]
     */
    public function getWorkflowModuleInstanceSettings()
    {
        return $this->workflowModuleInstanceSettings;
    }
}
