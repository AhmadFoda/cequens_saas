<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkflowContactBatch
 *
 * @ORM\Table(name="workflow_contact_batch")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkflowContactBatchRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class WorkflowContactBatch
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
     * @ORM\Column(name="WorkflowContactBatchFile", type="string", length=255)
     */
    private $workflowContactBatchFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="WorkflowContactBatchCreatedOn", type="datetime")
     */
    private $workflowContactBatchCreatedOn;


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
     * Set workflowContactBatchFile
     *
     * @param string $workflowContactBatchFile
     *
     * @return WorkflowContactBatch
     */
    public function setWorkflowContactBatchFile($workflowContactBatchFile)
    {
        $this->workflowContactBatchFile = $workflowContactBatchFile;

        return $this;
    }

    /**
     * Get workflowContactBatchFile
     *
     * @return string
     */
    public function getWorkflowContactBatchFile()
    {
        return $this->workflowContactBatchFile;
    }

    /**
     * Set workflowContactBatchCreatedOn
     *
     * @param \DateTime $workflowContactBatchCreatedOn
     *
     * @return WorkflowContactBatch
     */
    public function setWorkflowContactBatchCreatedOn($workflowContactBatchCreatedOn)
    {
        $this->workflowContactBatchCreatedOn = $workflowContactBatchCreatedOn;

        return $this;
    }

    /**
     * Get workflowContactBatchCreatedOn
     *
     * @return \DateTime
     */
    public function getWorkflowContactBatchCreatedOn()
    {
        return $this->workflowContactBatchCreatedOn;
    }
}

