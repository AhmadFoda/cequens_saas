<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkflowRecording
 *
 * @ORM\Table(name="workflow_recording")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkflowRecordingRepository")
 */
class WorkflowRecording
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
     * @ORM\Column(name="recordingUrl", type="string", length=255)
     */
    private $recordingUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="recordingDuration", type="string", length=255)
     */
    private $recordingDuration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdOn", type="datetime")
     */
    private $createdOn;

    /**
     * @var int
     *
     * @ORM\Column(name="module_instance_id", type="integer", nullable=true)
     */
    protected $moduleInstanceId;

    /**
     * @var int
     *
     * @ORM\Column(name="adapter_id", type="integer", nullable=true)
     */
    protected $adapterId;


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
     * Set recordingUrl
     *
     * @param string $recordingUrl
     *
     * @return WorkflowRecording
     */
    public function setRecordingUrl($recordingUrl)
    {
        $this->recordingUrl = $recordingUrl;

        return $this;
    }

    /**
     * Get recordingUrl
     *
     * @return string
     */
    public function getRecordingUrl()
    {
        return $this->recordingUrl;
    }

    /**
     * Set recordingDuration
     *
     * @param string $recordingDuration
     *
     * @return WorkflowRecording
     */
    public function setRecordingDuration($recordingDuration)
    {
        $this->recordingDuration = $recordingDuration;

        return $this;
    }

    /**
     * Get recordingDuration
     *
     * @return string
     */
    public function getRecordingDuration()
    {
        return $this->recordingDuration;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return WorkflowRecording
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return int
     */
    public function getModuleInstanceId()
    {
        return $this->moduleInstanceId;
    }

    /**
     * @param int $moduleInstanceId
     */
    public function setModuleInstanceId($moduleInstanceId)
    {
        $this->moduleInstanceId = $moduleInstanceId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdapterId()
    {
        return $this->adapterId;
    }

    /**
     * @param int $adapterId
     */
    public function setAdapterId($adapterId)
    {
        $this->adapterId = $adapterId;
        return $this;
    }

}

