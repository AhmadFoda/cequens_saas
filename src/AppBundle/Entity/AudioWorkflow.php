<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 10/18/2017
 * Time: 4:08 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorkflowInput
 *
 * @ORM\Table(name="workflow_audio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AudioWorkflowRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AudioWorkflow
{

    /**
     * @var int
     *
     * @ORM\Column(name="audio_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $audioId;

    /**
     * @var string
     *
     * @ORM\Column(name="audio_name", type="string", length=255)
     */
    protected $audioName;

    /**
     * @var string
     *
     * @ORM\Column(name="audio_safe_name", type="string", length=255)
     */
    protected $audioSafeName;

    /**
     * @return string
     */
    public function getAudioName()
    {
        return $this->audioName;
    }

    /**
     * @param string $audioName
     */
    public function setAudioName($audioName)
    {
        $this->audioName = $audioName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAudioSafeName()
    {
        return $this->audioSafeName;
    }

    /**
     * @param string $audioSafeName
     */
    public function setAudioSafeName($audioSafeName)
    {
        $this->audioSafeName = $audioSafeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAudioId()
    {
        return $this->audioId;
    }


}