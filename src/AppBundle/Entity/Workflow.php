<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Enumeration\EnumExecutionStatus;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * FileImport
 *
 * @ORM\Table(name="workflow")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkflowRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Workflow
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="workflow_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $workflowId;

	/**
	 * @var Adapter
	 *
	 * @ORM\ManyToOne(targetEntity="Adapter", inversedBy="workflows")
	 * @ORM\JoinColumn(name="adapter_id", referencedColumnName="adapter_id")
	 */
	protected $adapter;


	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="uploaded_at", type="datetime")
	 */
	protected $uploadedAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="started_at", type="datetime", nullable=true)
	 */
	protected $startedAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="finished_at", type="datetime", nullable=true)
	 */
	protected $finishedAt;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="execution_status", type="integer")
	 */
	protected $executionStatus = EnumExecutionStatus::NEW_UPLOAD;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="adapter_log_path", type="string", length=255, nullable=true)
	 */
	protected $adapterLogPath;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="adapter_result_path", type="string", length=255, nullable=true)
	 */
	protected $adapterResultPath;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="is_active", type="boolean", options={"default" : 1})
	 */
	protected $isActive = 1;

	/**
	 * @var FileImportStatistic[]
	 */
	protected $fileImportStats;

	/**
	 * @var Admin
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="workflows")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $admin;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_from", type="string", length=255, nullable=true)
     */
    protected $workflowFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_to", type="string", length=255, nullable=true)
     */
    protected $workflowTo;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_description", type="string", length=255, nullable=true)
     */
    protected $workflowDescription;

    /**
     * @var int
     *
     * @ORM\Column(name="is_parent", type="integer")
     */
    protected $isParent;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    protected $partentId;

    /**
     * @var int
     *
     * @ORM\Column(name="workflow_contact_batch_id", type="integer", nullable=true)
     */
    protected $workflowContactBatchId;



	/**
	 * FileImport constructor.
	 */
	public function __construct()
	{
		$this->fileImportStats = new ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getWorkflowId()
	{
		return $this->workflowId;
	}

    /**
     * Get id
     *
     * @return int
     */
    public function setWorkflowId($workflowId)
    {
        $this->workflowId = $workflowId;
        return $this;
    }

	/**
	 * Set adapterId
	 *
	 * @param Adapter $adapter
	 *
	 * @return Workflow
	 */
	public function setAdapter($adapter)
	{
		$adapter->assignedToFileImport($this);
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
	 * Set uploadedAt
	 *
	 * @param \DateTime $uploadedAt
	 *
	 * @return Workflow
	 */
	public function setUploadedAt($uploadedAt)
	{
		$this->uploadedAt = $uploadedAt;

		return $this;
	}

	/**
	 * Get uploadedAt
	 *
	 * @return \DateTime
	 */
	public function getUploadedAt()
	{
		return $this->uploadedAt;
	}

	/**
	 * Set startedAt
	 *
	 * @param \DateTime $startedAt
	 *
	 * @return Workflow
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
	 * @return Workflow
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
	 * @param int $executionStatus
	 */
	public function setExecutionStatus($executionStatus)
	{
		$this->executionStatus = $executionStatus;
	}

	/**
	 * @return int $executionStatus
	 */
	public function getExecutionStatus()
	{
		return $this->executionStatus;
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
	 * @return Admin
	 */
	public function getAdmin()
	{
		return $this->admin;
	}

	/**
	 * @param Admin $admin
	 */
	public function setAdmin($admin)
	{
		$admin->assignedToFileImport($this);
		$this->admin = $admin;
	}

	/**
	 * @return string
	 */
	public function getAdapterLogPath()
	{
		return $this->adapterLogPath;
	}

	/**
	 * @param string $adapterLogPath
	 */
	public function setAdapterLogPath($adapterLogPath)
	{
		$this->adapterLogPath = $adapterLogPath;
	}

	/**
	 * @return string
	 */
	public function getAdapterResultPath()
	{
		return $this->adapterResultPath;
	}

	/**
	 * @param string $adapterResultPath
	 */
	public function setAdapterResultPath($adapterResultPath)
	{
		$this->adapterResultPath = $adapterResultPath;
	}

	/**
	 * @ORM\PrePersist
	 */
	public function onPrePersist()
	{
		$this->setUploadedAt(new \DateTime());
	}

    /**
     * @return string
     */
    public function getWorkflowFrom()
    {
        return $this->workflowFrom;
    }

    /**
     * @param string $workflowFrom
     */
    public function setWorkflowFrom($workflowFrom)
    {
        $this->workflowFrom = $workflowFrom;
    }

    /**
     * @return string
     */
    public function getWorkflowTo()
    {
        return $this->workflowTo;
    }

    /**
     * @param string $workflowTo
     */
    public function setWorkflowTo($workflowTo)
    {
        $this->workflowTo = $workflowTo;
    }

    /**
     * @return string
     */
    public function getWorkflowDescription()
    {
        return $this->workflowDescription;
    }

    /**
     * @param string $workflowDescription
     */
    public function setWorkflowDescription($workflowDescription)
    {
        $this->workflowDescription = $workflowDescription;
    }

    /**
     * @return int
     */
    public function getisParent()
    {
        return $this->isParent;
    }

    /**
     * @param int $isParent
     */
    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;
        return $this;
    }

    /**
     * @return int
     */
    public function getPartentId()
    {
        return $this->partentId;
    }

    /**
     * @param int $partentId
     */
    public function setPartentId($partentId)
    {
        $this->partentId = $partentId;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkflowContactBatchId()
    {
        return $this->workflowContactBatchId;
    }

    /**
     * @param int $workflowContactBatchId
     */
    public function setWorkflowContactBatchId($workflowContactBatchId)
    {
        $this->workflowContactBatchId = $workflowContactBatchId;
        return $this;
    }

}

