<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 27/01/19
 * Time: 12:12 م
 */

namespace AppBundle\Service\sms;


use AppBundle\Entity\Module;
use AppBundle\Entity\ModuleInstance;
use CequensBundle\Service\RestcommService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TriggerWorkflowModule
{
    private $module;
    private $moduleInstance;
    private $rcml;
    private $em;
    private $container;
    private $collectedInputs;
    private $variables;
    private $sessionId;
    private $restcommService;

    /**
     * TriggerWorkflowModule constructor.
     */
    public function __construct(Module $module, ModuleInstance $moduleInstance, EntityManagerInterface $entityManager, ContainerInterface $container, array $collectedInputs = array(), RestcommService $restcommService)
    {
        $this->module = $module;
        $this->moduleInstance = $moduleInstance;
        $this->em = $entityManager;
        $this->container = $container;
        $this->collectedInputs = $collectedInputs;
        $this->variables = [];
        $this->restcommService = $restcommService;
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

    public function initializeVariables($cacheService = null, $key, $variables = null)
    {
        if (!empty($variables)) {
            foreach ($variables as $variableKey => $variableValue) {
                $result = $cacheService->addCapturedDigitsToCache($key, $variableValue, $variableKey);
            }

        } else {
            $result = $cacheService->addCapturedDigitsToCache($key);
        }
        $this->container->get('logger')->debug('['.__CLASS__.'::'.__METHOD__.'] Initializing Variables => ', [$result]);
        $this->variables = (!empty($result['collected_inputs'])) ? $result['collected_inputs'] : [];
    }

    private function populateStringFromVariables($message)
    {
        $this->container->get('logger')->debug('['.__CLASS__.'::'.__METHOD__.'] Populating Message =>', [$message]);
        if (preg_match_all('/{{(.*?)}}/', $message, $matches) >= 1) {
            array_shift($matches);
            $this->container->get('logger')->debug('['.__CLASS__.'::'.__METHOD__.'] Variables Matched => ', $matches);
            $this->container->get('logger')->debug('['.__CLASS__.'::'.__METHOD__.'] Variables => ', $this->variables);
            foreach ($matches[0] as $matchItem) {
                $this->container->get('logger')->debug('Say Matches Seach => ', [$matchItem]);
                if (array_key_exists($matchItem, $this->variables)) {
                    $message = str_replace('{{' . $matchItem . '}}', $this->variables[$matchItem], $message);
                }
            }
        }
        $this->container->get('logger')->debug('['.__CLASS__.'::'.__METHOD__.'] Final Populated Message => ', [$message]);
        return $message;
    }

    public function execute($parameters = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $workflowUrl = null;
        $moduleId = null;
        foreach ($moduleInstanceSettings as $module_instance_setting) {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name)
            {
                case 'moduleId':
                    $moduleId = $value;
                    $workflowUrl = $this->container->getParameter('application_url').'/restcomm/'.$value.'/rcml?rand='.rand(10000,999999);
                    $workflowUrl = htmlentities($workflowUrl);
                    break;
            }
        }

        $from = $parameters['from'];
        $to = $parameters['to'];
        $result = $this->restcommService->triggerCall(
            $to,
            $from,
            $moduleId
        );
        $this->container->get('logger')->debug('CallingRestcommService',[$result]);
        //if (array_key_exists('sid', $result)) {
            //$this->cacheService->addCapturedDigitsToCache($result['sid']);
        //}
        //}
        //}
        //}
        //}

        $nextModuleInstanceConnections = $this->moduleInstance->getModuleInstanceConnections();
        foreach ($nextModuleInstanceConnections as $nextModuleInstanceConnection) {
            $nextModule = $nextModuleInstanceConnection->getTargetModuleInstanceId();
        }
        return ['code' => 200, 'body' => ['nextModule'=> $nextModule]];
    }
}