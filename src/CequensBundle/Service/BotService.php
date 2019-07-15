<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 22/11/18
 * Time: 09:48 ص
 */

namespace CequensBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BotService
{
    protected $entityManager;
    protected $validator;
    protected $container;
    protected $logger ;
    /**
     * WorkflowService constructor.
     *
     * @param $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ContainerInterface $container,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function createNewBot($userId, $botName, $botDescription, $botType)
    {
        $result = $this->postToApis(
            $this->container->getParameter('bot_service_url') . '/internal/bot',
            [
                'bot_name' => $botName,
                'bot_description' => $botDescription,
                'bot_type' => $botType,
                'user_id' => $userId
            ],
            false
        );
        $result = json_decode($result, true);
        $result['data'] = json_decode($result['data'], true);
        return $result;

    }

    public function createNewBotConfig($botId, $botConfig)
    {
        $botConfigs = [];
        foreach ($botConfig as $itemKey => $itemValue) {
            $botConfigs[] = ['key' => $itemKey, 'value' => $itemValue];
        }
        $botConfigs[] = ['key'=>'FACEBOOK_APP_ID','value'=>$this->container->getParameter('cequens_fb_app_id')];
        $botConfigs[] = ['key'=>'FACEBOOK_APP_SECRET','value'=>$this->container->getParameter('cequens_fb_app_secret')];
        $botConfigs[] = ['key'=>'BOT_TOKEN','value'=>$this->container->getParameter('cequens_fb_app_secret')];
        $botConfigs[] = ['key'=>'FACEBOOK_VERIFICATION','value'=>'bot_nodered'];
        $result = $this->postToApis(
            $this->container->getParameter('bot_service_url') . '/internal/bot/' . $botId . '/config',
            [
                'bot_config' => $botConfigs
            ],
            false
        );

        return json_decode($result, true);

    }

    public function sendMessage($botId, $to, $message)
    {
        $this->logger->debug('SENDING new message action');
        $this->postToApis(
            $this->container->getParameter('bot_service_url') . '/internal/message',
            [
                'bot_id' => $botId,
                'message_to' => $to,
                'message' => $message,
                'persona_id' => 313556895915771
            ],
            false
        );

    }

    public function triggerWorkflow($to, $workflowId, $botId)
    {

        $this->postToApis(
            $this->container->getParameter('bot_service_url') . '/internal/' . $workflowId,
            [
                'message_to' => $to,
                'bot_id' =>$botId
            ],
            false
        );

    }

    private function postToApis($url, $param, $isGet = false)
    {
        $ch = curl_init();
        $curl_options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        );
        /*$fields_string = '';
        foreach ($param as $key => $value) {
            $fields_string .= $key . '=' . urlencode($value) . '&';
        }
        rtrim($fields_string, '&');*/
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!$isGet) {
            $payload = json_encode($param);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            // Set HTTP Header for POST request
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($payload))
            );
        }
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, $curl_options);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);

        //list($response_headers, $response_content) = preg_split('/(\r\n){2}/', $result, 2);
        return $result;
    }

    public function getAllBots($userId)
    {
        $return = ['success'=>true,'data'=>[]];
        $bots = $this->entityManager->getRepository('CequensBundle:Bot')->findBy(['userId'=>$userId]);
        foreach ($bots as $bot) {
            $return['data'][]=[
              'name'=>$bot->getBotName(),
              'creationDate'=>$bot->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }
        return $return;
    }

    public function triggerIsTyping($botId, $userId, $roomId)
    {
        $response = $this->postToApis(
            $this->container->getParameter('bot_service_url') . '/internal/triggerIsTyping',
            [
                'bot_id' => $botId,
                'user_id' => $userId,
                'room_id' => $roomId
            ],
            false
        );

        return $response;
    }


}