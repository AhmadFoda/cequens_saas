<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkflowContact
 *
 * @ORM\Table(name="workflow_contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkflowContactRepository")
 * @ORM\HasLifecycleCallbacks
 */
class WorkflowContact
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="WorkflowContactNumber", type="string", length=255)
     */
    private $workflowContactNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="WorkflowContactNumberCallId", type="string", length=255)
     */
    private $workflowContactNumberCallId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="WorkflowContactRestCommSid", type="string", length=255, nullable=true)
	 */
	private $workflowContactRestCommSid;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="WorkflowContactRestCommStatus", type="string", length=255, nullable=true)
	 */
	private $workflowContactRestCommStatus;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="WorkflowContactRestCommRecordUrl", type="string", length=255, nullable=true)
	 */
	private $workflowContactRestCommRecordUrl;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="WorkflowContactRestCommDuration", type="string", length=255, nullable=true)
	 */
	private $workflowContactRestCommDuration;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="WorkflowContactRestCommRingDuration", type="string", length=255, nullable=true)
	 */
	private $workflowContactRestCommRingDuration;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="WorkflowContactRestCommStartedAt", type="string", length=255, nullable=true)
	 */
	private $workflowContactRestCommStartedAt;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="WorkflowContactRestCommFinishedAt", type="string", length=255, nullable=true)
	 */
	private $workflowContactRestCommFinishedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="WorkflowContactBatchId", type="integer")
     */
    private $workflowContactBatchId;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="WorkflowRequestCallBack", type="integer", nullable=true)
	 */
	private $workflowRequestCallBack;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set workflowContactNumber
     *
     * @param string $workflowContactNumber
     *
     * @return WorkflowContact
     */
    public function setWorkflowContactNumber($workflowContactNumber)
    {
        $this->workflowContactNumber = $workflowContactNumber;

        return $this;
    }

    /**
     * Get workflowContactNumber
     *
     * @return string
     */
    public function getWorkflowContactNumber()
    {
        return $this->workflowContactNumber;
    }

    /**
     * Set workflowContactBatchId
     *
     * @param integer $workflowContactBatchId
     *
     * @return WorkflowContact
     */
    public function setWorkflowContactBatchId($workflowContactBatchId)
    {
        $this->workflowContactBatchId = $workflowContactBatchId;

        return $this;
    }

    /**
     * Get workflowContactBatchId
     *
     * @return int
     */
    public function getWorkflowContactBatchId()
    {
        return $this->workflowContactBatchId;
    }

    /**
     * @return string
     */
    public function getWorkflowContactNumberCallId()
    {
        return $this->workflowContactNumberCallId;
    }

    /**
     * @param string $workflowContactNumberCallId
     */
    public function setWorkflowContactNumberCallId($workflowContactNumberCallId)
    {
        $this->workflowContactNumberCallId = $workflowContactNumberCallId;
        return $this;
    }

	/**
	 * @return string
	 */
	public function getWorkflowContactRestCommSid()
	{
		return $this->workflowContactRestCommSid;
	}

	/**
	 * @param string $workflowContactRestCommSid
	 */
	public function setWorkflowContactRestCommSid($workflowContactRestCommSid)
	{
		$this->workflowContactRestCommSid = $workflowContactRestCommSid;
	}

	/**
	 * @return string
	 */
	public function getWorkflowContactRestCommStatus()
	{
		return $this->workflowContactRestCommStatus;
	}

	/**
	 * @param string $workflowContactRestCommStatus
	 */
	public function setWorkflowContactRestCommStatus($workflowContactRestCommStatus)
	{
		$this->workflowContactRestCommStatus = $workflowContactRestCommStatus;
	}

	/**
	 * @return string
	 */
	public function getWorkflowContactRestCommRecordUrl()
	{
		return $this->workflowContactRestCommRecordUrl;
	}

	/**
	 * @param string $workflowContactRestCommRecordUrl
	 */
	public function setWorkflowContactRestCommRecordUrl($workflowContactRestCommRecordUrl)
	{
		$this->workflowContactRestCommRecordUrl = $workflowContactRestCommRecordUrl;
	}

	/**
	 * @return string
	 */
	public function getWorkflowContactRestCommDuration()
	{
		return $this->workflowContactRestCommDuration;
	}

	/**
	 * @param string $workflowContactRestCommDuration
	 */
	public function setWorkflowContactRestCommDuration($workflowContactRestCommDuration)
	{
		$this->workflowContactRestCommDuration = $workflowContactRestCommDuration;
	}

	/**
	 * @return string
	 */
	public function getWorkflowContactRestCommRingDuration()
	{
		return $this->workflowContactRestCommRingDuration;
	}

	/**
	 * @param string $workflowContactRestCommRingDuration
	 */
	public function setWorkflowContactRestCommRingDuration($workflowContactRestCommRingDuration)
	{
		$this->workflowContactRestCommRingDuration = $workflowContactRestCommRingDuration;
	}

	/**
	 * @return string
	 */
	public function getWorkflowContactRestCommStartedAt()
	{
		return $this->workflowContactRestCommStartedAt;
	}

	/**
	 * @param string $workflowContactRestCommStartedAt
	 */
	public function setWorkflowContactRestCommStartedAt($workflowContactRestCommStartedAt)
	{
		$this->workflowContactRestCommStartedAt = $workflowContactRestCommStartedAt;
	}

	/**
	 * @return string
	 */
	public function getWorkflowContactRestCommFinishedAt()
	{
		return $this->workflowContactRestCommFinishedAt;
	}

	/**
	 * @param string $workflowContactRestCommFinishedAt
	 */
	public function setWorkflowContactRestCommFinishedAt($workflowContactRestCommFinishedAt)
	{
		$this->workflowContactRestCommFinishedAt = $workflowContactRestCommFinishedAt;
	}

	/**
	 * @return int
	 */
	public function getWorkflowRequestCallBack()
	{
		return $this->workflowRequestCallBack;
	}

	/**
	 * @param int $workflowRequestCallBack
	 */
	public function setWorkflowRequestCallBack($workflowRequestCallBack)
	{
		$this->workflowRequestCallBack = $workflowRequestCallBack;
	}


}

