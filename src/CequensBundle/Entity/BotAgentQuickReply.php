<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BotAgentQuickReply
 *
 * @ORM\Table(name="bot_agent_quick_reply")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\BotAgentQuickReplyRepository")
 */
class BotAgentQuickReply
{
    /**
     * @var int
     *
     * @ORM\Column(name="bot_agent_quick_reply_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="bot_id", type="integer")
     */
    private $botId;

    /**
     * @var string
     *
     * @ORM\Column(name="bot_agent_quick_reply_tag", type="string", length=255)
     */
    private $botAgentQuickReplyTag;

    /**
     * @var string
     *
     * @ORM\Column(name="bot_agent_quick_reply_text", type="text")
     */
    private $botAgentQuickReplyText;


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
     * Set botId.
     *
     * @param int $botId
     *
     * @return BotAgentQuickReply
     */
    public function setBotId($botId)
    {
        $this->botId = $botId;

        return $this;
    }

    /**
     * Get botId.
     *
     * @return int
     */
    public function getBotId()
    {
        return $this->botId;
    }

    /**
     * Set botAgentQuickReplyTag.
     *
     * @param string $botAgentQuickReplyTag
     *
     * @return BotAgentQuickReply
     */
    public function setBotAgentQuickReplyTag($botAgentQuickReplyTag)
    {
        $this->botAgentQuickReplyTag = $botAgentQuickReplyTag;

        return $this;
    }

    /**
     * Get botAgentQuickReplyTag.
     *
     * @return string
     */
    public function getBotAgentQuickReplyTag()
    {
        return $this->botAgentQuickReplyTag;
    }

    /**
     * Set botAgentQuickReplyText.
     *
     * @param string $botAgentQuickReplyText
     *
     * @return BotAgentQuickReply
     */
    public function setBotAgentQuickReplyText($botAgentQuickReplyText)
    {
        $this->botAgentQuickReplyText = $botAgentQuickReplyText;

        return $this;
    }

    /**
     * Get botAgentQuickReplyText.
     *
     * @return string
     */
    public function getBotAgentQuickReplyText()
    {
        return $this->botAgentQuickReplyText;
    }
}
