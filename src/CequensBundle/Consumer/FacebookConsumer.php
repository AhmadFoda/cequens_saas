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

class FacebookConsumer implements ConsumerInterface
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
            $mm = json_encode($message);
            $this->logger->addInfo($mm . PHP_EOL);
            $job = unserialize($body['data']['command']);
            $job = $this->fixLaravelObj($job);
            $message = (!empty(json_decode($job->messageBody,true))) ? json_decode($job->messageBody,true) : array();

                $this->saveFacebookMessage($message);

            $this->logger->addInfo('FB Message Received => ',$message);

        } catch (\Exception $e) {
            $this->logger->addError(
                'Problem consuming file import status update: ' . $e->getMessage(),
                ['exception_trace' => $e->getTrace()]
            );
            throw new \Exception($e);
        }

    }

    public function fixLaravelObj($object)
    {
        // preg_replace_callback handler. Needed to calculate new key-length.
        $fix_key = create_function(
            '$matches',
            'return ":" . strlen( $matches[1] ) . ":\"" . $matches[1] . "\"";'
        );

        // 1. Serialize the object to a string.
        $dump = serialize( $object );

        // 2. Change class-type to 'stdClass'.
        $dump = preg_replace( '/^O:\d+:"[^"]++"/', 'O:8:"stdClass"', $dump );

        // 3. Make private and protected properties public.
        $dump = preg_replace_callback( '/:\d+:"\0.*?\0([^"]+)"/', $fix_key, $dump );

        // 4. Unserialize the modified object again.
       return unserialize( $dump );
    }

    public function saveFacebookMessage($body)
    {

        $results = array(
            'success' => false,
            'data'    => array(),
            'msg'     => ''
        );

        $this->logger->debug('Before Saving Message =>',$body);
        $from = $body['bot_message_from'];
        $to = $body['bot_message_to'];
        //$body = $body['responseMsgbody'];
        $bodyMetadata = json_decode($body['bot_message_metadata'],true);
        $this->logger->debug('MetaaaaaaaDataaaaa =>',$bodyMetadata);
        $bodyMetadataChannel = (array_key_exists('channel',$bodyMetadata)) ? $bodyMetadata['channel'] : '';
        $bodyMetadataIsFromBot = (array_key_exists('isFromBot',$bodyMetadata)) ? $bodyMetadata['isFromBot'] : true;
        $senderType = (filter_var($bodyMetadataIsFromBot, FILTER_VALIDATE_BOOLEAN)) ? 'FB-Bot' : 'FB-User';
        $this->logger->debug('Sender Type =>'.$senderType);
        $bodyPayload = json_decode($body['bot_message_payload'],true);
        $bodyMessage = (array_key_exists('message',$bodyPayload)) ? $bodyPayload['message'] : array();
        $bodyMessageText = (array_key_exists('text',$bodyMessage)) ? $bodyMessage['text'] : '';
        $bodyMessageQuickReplies = (array_key_exists('quick_replies',$bodyMessage)) ? $bodyMessage['quick_replies'] : array();
        $i=1;
        foreach ($bodyMessageQuickReplies as $messageQuickReply)
        {
            $bodyMessageText = $bodyMessageText.PHP_EOL.$i.'- '.$messageQuickReply['title'];
            $i++;
        }
        $conversationMessage = new ConversationMessage();
        $format = 'Y-m-d H:i:s';
        $date = \DateTime::createFromFormat($format, $body['created_at']);
        $conversationMessage->setSendDate($date);
        $conversationMessage->setMessageType('TEXT');
        $conversationMessage->setMessage($bodyMessageText);
        $conversationMessage->setMetadata('Facebook');
        $conversationMessage->setNlp('empty');
        $conversationMessage->setPageToken('FB');
        $conversationMessage->setPrimaryAppId('--');
        $conversationMessage->setAgentAppId('--');
        $conversationMessage->setRecId($to);
        $conversationMessage->setSenderId($from);
        $conversationMessage->setChannel('FB');
        $conversationMessage->setSenderType($senderType);
        $this->entityManager->persist($conversationMessage);
        $this->entityManager->flush();
        $results['success'] = true;

        return $results;
    }
}