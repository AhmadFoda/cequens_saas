<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConversationMessage
 *
 * @ORM\Table(name="conversation_monitor_message")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\ConversationMessageRepository")
 */
class ConversationMessage
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
     * @var \DateTime
     *
     * @ORM\Column(name="send_date", type="datetime")
     */
    private $sendDate;

    /**
     * @var string
     *
     * @ORM\Column(name="message_type", type="string", length=255)
     */
    private $messageType;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="metadata", type="text")
     */
    private $metadata;

    /**
     * @var string
     *
     * @ORM\Column(name="nlp", type="text")
     */
    private $nlp;

    /**
     * @var string
     *
     * @ORM\Column(name="page_token", type="string", length=255)
     */
    private $pageToken;

    /**
     * @var string
     *
     * @ORM\Column(name="primary_app_id", type="string", length=255)
     */
    private $primaryAppId;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_app_id", type="string", length=255)
     */
    private $agentAppId;

    /**
     * @var string
     *
     * @ORM\Column(name="rec_id", type="text")
     */
    private $recId;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_id", type="text")
     */
    private $senderId;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="text")
     */
    private $channel;

    /**
     * @var string
     *
     * @ORM\Column(name="sender", type="text")
     */
    private $senderType;


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
     * Set sendDate.
     *
     * @param \DateTime $sendDate
     *
     * @return ConversationMessage
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * Get sendDate.
     *
     * @return \DateTime
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * Set messageType.
     *
     * @param string $messageType
     *
     * @return ConversationMessage
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;

        return $this;
    }

    /**
     * Get messageType.
     *
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return ConversationMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set metadata.
     *
     * @param string $metadata
     *
     * @return ConversationMessage
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get metadata.
     *
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set nlp.
     *
     * @param string $nlp
     *
     * @return ConversationMessage
     */
    public function setNlp($nlp)
    {
        $this->nlp = $nlp;

        return $this;
    }

    /**
     * Get nlp.
     *
     * @return string
     */
    public function getNlp()
    {
        return $this->nlp;
    }

    /**
     * Set pageToken.
     *
     * @param string $pageToken
     *
     * @return ConversationMessage
     */
    public function setPageToken($pageToken)
    {
        $this->pageToken = $pageToken;

        return $this;
    }

    /**
     * Get pageToken.
     *
     * @return string
     */
    public function getPageToken()
    {
        return $this->pageToken;
    }

    /**
     * Set primaryAppId.
     *
     * @param string $primaryAppId
     *
     * @return ConversationMessage
     */
    public function setPrimaryAppId($primaryAppId)
    {
        $this->primaryAppId = $primaryAppId;

        return $this;
    }

    /**
     * Get primaryAppId.
     *
     * @return string
     */
    public function getPrimaryAppId()
    {
        return $this->primaryAppId;
    }

    /**
     * Set agentAppId.
     *
     * @param string $agentAppId
     *
     * @return ConversationMessage
     */
    public function setAgentAppId($agentAppId)
    {
        $this->agentAppId = $agentAppId;

        return $this;
    }

    /**
     * Get agentAppId.
     *
     * @return string
     */
    public function getAgentAppId()
    {
        return $this->agentAppId;
    }

    /**
     * Set recId.
     *
     * @param string $recId
     *
     * @return ConversationMessage
     */
    public function setRecId($recId)
    {
        $this->recId = $recId;

        return $this;
    }

    /**
     * Get recId.
     *
     * @return string
     */
    public function getRecId()
    {
        return $this->recId;
    }

    /**
     * @return string
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param string $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderType()
    {
        return $this->senderType;
    }

    /**
     * @param string $senderType
     */
    public function setSenderType($senderType)
    {
        $this->senderType = $senderType;

        return $this;
    }


}
