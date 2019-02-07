<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TriggeredKeys
 *
 * @ORM\Table(name="triggered_keys")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\TriggeredKeysRepository")
 */
class TriggeredKeys
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
     * @ORM\Column(name="trigger_key", type="string", length=255)
     */
    private $triggerKey;

    /**
     * @var string
     *
     * @ORM\Column(name="trigger_type", type="string", length=255)
     */
    private $triggerType;

    /**
     * @var int
     *
     * @ORM\Column(name="trigger_status", type="integer")
     */
    private $triggerStatus;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set triggerKey.
     *
     * @param string $triggerKey
     *
     * @return TriggeredKeys
     */
    public function setTriggerKey($triggerKey)
    {
        $this->triggerKey = $triggerKey;

        return $this;
    }

    /**
     * Get triggerKey.
     *
     * @return string
     */
    public function getTriggerKey()
    {
        return $this->triggerKey;
    }

    /**
     * Set triggerType.
     *
     * @param string $triggerType
     *
     * @return TriggeredKeys
     */
    public function setTriggerType($triggerType)
    {
        $this->triggerType = $triggerType;

        return $this;
    }

    /**
     * Get triggerType.
     *
     * @return string
     */
    public function getTriggerType()
    {
        return $this->triggerType;
    }

    /**
     * Set triggerStatus.
     *
     * @param int $triggerStatus
     *
     * @return TriggeredKeys
     */
    public function setTriggerStatus($triggerStatus)
    {
        $this->triggerStatus = $triggerStatus;

        return $this;
    }

    /**
     * Get triggerStatus.
     *
     * @return int
     */
    public function getTriggerStatus()
    {
        return $this->triggerStatus;
    }
}
