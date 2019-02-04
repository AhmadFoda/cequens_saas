<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 10/17/2017
 * Time: 1:22 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorkflowInput
 *
 * @ORM\Table(name="workflow_input")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkflowInputRepository")
 * @ORM\HasLifecycleCallbacks
 */
class WorkflowInput
{
    /**
     * @var int
     *
     * @ORM\Column(name="input_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $inputId;

    /**
     * @var int
     *
     * @ORM\Column(name="adapter_id", type="integer", nullable=true, unique=true)
     */
    protected $adapterId;

    /**
     * @var int
     *
     * @ORM\Column(name="module_instance_id", type="integer", nullable=true, unique=true)
     */
    protected $moduleInstanceId;

    /**
     * @var string
     *
     * @ORM\Column(name="input_value", type="string", length=255)
     */
    protected $inputValue;

    /**
     * @var string
     *
     * @ORM\Column(name="input_from", type="string", length=255)
     */
    protected $inputFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="input_to", type="string", length=255)
     */
    protected $inputTo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="input_creation_date", type="datetime")
     */
    protected $inputCreationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="call_sid", type="string", length=255)
     */
    protected $callSid;

    /**
     * @return string
     */
    public function getInputTo()
    {
        return $this->inputTo;
    }

    /**
     * @param string $inputTo
     */
    public function setInputTo($inputTo)
    {
        $this->inputTo = $inputTo;
        return $this;
    }



    /**
     * @return string
     */
    public function getCallSid()
    {
        return $this->callSid;
    }

    /**
     * @param string $callSid
     */
    public function setCallSid($callSid)
    {
        $this->callSid = $callSid;
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
     * @return string
     */
    public function getInputValue()
    {
        return $this->inputValue;
    }

    /**
     * @param string $inputValue
     */
    public function setInputValue($inputValue)
    {
        $this->inputValue = $inputValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputFrom()
    {
        return $this->inputFrom;
    }

    /**
     * @param string $inputFrom
     */
    public function setInputFrom($inputFrom)
    {
        $this->inputFrom = $inputFrom;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInputCreationDate()
    {
        return $this->inputCreationDate;
    }

    /**
     * @param \DateTime $inputCreationDate
     */
    public function setInputCreationDate($inputCreationDate)
    {
        $this->inputCreationDate = $inputCreationDate;
        return $this;
    }



}