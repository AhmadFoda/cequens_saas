<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 18/10/18
 * Time: 02:10 م
 */

namespace CequensBundle\Consumer;

use CequensBundle\Entity\ConversationMessage;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class WhatsappConsumer implements ConsumerInterface
{
    protected $logger;
    private $entityManager;

    /**
     * ImportStatusConsumer constructor.
     *
     * @param Logger        $logger
     * @param EntityManager $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger 			= $logger;
        $this->entityManager 	= $entityManager;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return mixed|void
     */
    public function execute(AMQPMessage $message)
    {
        $body = json_decode($message->body, true);
        try {
            $this->logger->addInfo(json_encode($message) . PHP_EOL);
            $this->saveWhatAppMessage($body);

        } catch (\Exception $e) {
            $this->logger->addError(
                'Problem consuming file import status update: ' . $e->getMessage(),
                ['exception_trace' => $e->getTrace()]
            );
        }

    }

    public function saveWhatAppMessage($body)
    {

        $results = array(
            'success' => false,
            'data'    => array(),
            'msg'     => ''
        );

        $this->logger->debug('Before Saving Message =>',$body);
        $from = $body['mobileNumber'];
        $body = $body['responseMsgbody'];
        $conversationMessage = new ConversationMessage();
        $conversationMessage->setSendDate(new \DateTime('now'));
        $conversationMessage->setMessageType('TEXT');
        $conversationMessage->setMessage($body);
        $conversationMessage->setMetadata('WhatsApp');
        $conversationMessage->setNlp('empty');
        $conversationMessage->setPageToken('WhatsApp');
        $conversationMessage->setPrimaryAppId('--');
        $conversationMessage->setAgentAppId('--');
        $conversationMessage->setRecId('+201063660411');
        $conversationMessage->setSenderId($from);
        $conversationMessage->setChannel('WhatsApp');
        $conversationMessage->setSenderType('WhatsApp-User');
        $this->entityManager->persist($conversationMessage);
        $this->entityManager->flush();
        $results['success'] = true;

        return $results;
    }
}