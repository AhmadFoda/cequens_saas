<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 10/15/2017
 * Time: 1:17 PM
 */

namespace AppBundle\Service\sms;


use AppBundle\Entity\Module;
use AppBundle\Entity\ModuleInstance;
use AppBundle\Entity\WorkflowModuleInstance;
use CequensBundle\Service\CacheService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendSmsModule
{
    private $module;
    private $moduleInstance;
    private $em;
    private $container;
    private $collectedInputs;
    private $variables;
    private $sessionId;

    /**
     * PlayURLModule constructor.
     */
    public function __construct(Module $module, ModuleInstance $moduleInstance, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
    {
        $this->module = $module;
        $this->moduleInstance = $moduleInstance;
        $this->em = $entityManager;
        $this->container = $container;
        $this->collectedInputs = $collectedInputs;
        $this->variables = [];
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }


    public function initializeVariables($cacheService=null, $key, $variables=null)
    {
        if(!empty($variables))
        {
            foreach ($variables as $variableKey => $variableValue)
            {
                $result = $cacheService->addCapturedDigitsToCache($key, $variableValue, $variableKey);
            }

        }
        else
        {
            $result = $cacheService->addCapturedDigitsToCache($key);
        }

        $this->container->get('logger')->debug('Inittttttt Variables => ',[$result]);
        $this->variables = (!empty($result['collected_inputs'])) ? $result['collected_inputs'] : [];
    }

    private function populateStringFromVariables($message)
    {
        $this->container->get('logger')->debug('Populating Message', [$message]);
        if (preg_match_all('/{{(.*?)}}/', $message, $matches) >= 1) {
            $this->container->get('logger')->debug('Say Matches => ', $matches);
            array_shift($matches);
            $this->container->get('logger')->debug('Say Matches => ', $matches[0]);
            $this->container->get('logger')->debug('Say Matches Variables => ', $this->variables);
            foreach ($matches[0] as $matchItem) {
                $this->container->get('logger')->debug('Say Matches Seach => ', [$matchItem]);
                if (array_key_exists($matchItem, $this->variables)) {
                    $message = str_replace('{{' . $matchItem . '}}', $this->variables[$matchItem], $message);
                }
            }
        }
        $this->container->get('logger')->debug('Say Matches => ', [$message]);
        return $message;
    }

    public function execute($parameters = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $param = [];
        foreach ($moduleInstanceSettings as $module_instance_setting) {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name) {
                case 'senderName':
                    $value = $this->populateStringFromVariables($value);
                    $param['sender'] = $value;
                    break;
                case 'to':
                    $value = $this->populateStringFromVariables($value);
                    $param['to'] = $value;
                    break;
                case 'message':
                    $value = $this->populateStringFromVariables($value);
                    $param['message'] = $value;
                    break;
            }
        }
        $from = $param['sender'];
        $to = $param['to'];
        $message = $param['message'];
        $method = strtolower($param['message']);
        $authData = ['apiKey' => '61005ca3-eeb0-459b-b998-b7e7d92d881c', 'username' => 'waelnabil'];
        $authDataJson = \Unirest\Request\Body::Json($authData);
        $authDataJson = "{\"apiKey\":\"61005ca3-eeb0-459b-b998-b7e7d92d881c\",\"userName\": \"waelnabil\"}";
        $authHeaders = ['accept' => 'application/json', 'content-type' => 'application/json'];
        $response = \Unirest\Request::post('https://api.cequens.com/cequens/api/v1/signin', $authHeaders, $authDataJson);
        $responsBody = $response->body;
        $this->container->get('logger')->debug('Response Recevied after authenticating send sms apis',[$responsBody]);
        $smsHeaders = array(
            "accept"=>"application/json",
            "authorization"=>"Bearer " . $responsBody->data->access_token,
            "content-type"=>"application/json"
        );
        $smsData = ['messageText' => $message, 'senderName' => $from, 'messageType' => 'text', 'recipients' => $to];
        $smsDataJson = \Unirest\Request\Body::Json($smsData);
        $smsDataJson = "{\"messageText\":\"$message\",\"senderName\":\"$from\",\"messageType\":\"text\",\"recipients\":\"$to\"}";
        $response = \Unirest\Request::post('https://api.cequens.com/cequens/api/v1/messaging', $smsHeaders, $smsDataJson);
        $this->container->get('logger')->debug('Response Recevied after sending SMS',[$response->body]);
        $nextModuleInstanceConnections = $this->moduleInstance->getModuleInstanceConnections();
        foreach ($nextModuleInstanceConnections as $nextModuleInstanceConnection) {
            $nextModule = $nextModuleInstanceConnection->getTargetModuleInstanceId();
        }
        return ['code' => $response->code, 'body' => ['nextModule'=> $nextModule]];
    }

}