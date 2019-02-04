<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 10/15/2017
 * Time: 1:17 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Module;
use AppBundle\Entity\ModuleInstance;
use AppBundle\Entity\WorkflowModuleInstance;
use CequensBundle\Service\CacheService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SayTextModule
{
    private $module;
    private $moduleInstance;
    private $rcml;
    private $em;
    private $container;
    private $collectedInputs;
    private $variables;
    private $sessionId;

    /**
     * PlayURLModule constructor.
     */
    public function __construct(Module $module, ModuleInstance $moduleInstance, \SimpleXMLElement $rcml, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
    {
        $this->module = $module;
        $this->rcml = $rcml;
        $this->moduleInstance = $moduleInstance;
        $this->em = $entityManager;
        $this->container = $container;
        $this->collectedInputs = $collectedInputs;
        $this->variables = [];
    }

    public function getRcml($input = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $node = null;
        foreach ($moduleInstanceSettings as $module_instance_setting) {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name) {
                case 'ttsMesssage':
                    $value = $this->populateStringFromVariables($value);
                    $node = $this->rcml->addChild('Say', $value);
                    break;
                case 'ttsVoice':
                    $moduleInstanceSettingOption = $this->em->getRepository('AppBundle:ModuleSettingOption')->find($value);
                    $node->addAttribute('voice', $moduleInstanceSettingOption->getValue());
                    break;
                case 'ttsLanguage':
                    $moduleInstanceSettingOption = $this->em->getRepository('AppBundle:ModuleSettingOption')->find($value);
                    $node->addAttribute('language', $moduleInstanceSettingOption->getValue());
                    break;

            }
        }
        $connections = $this->moduleInstance->getModuleInstanceConnections();
        if (count($connections) > 1) {
            $node = $this->rcml->addChild('Redirect', htmlentities($this->container->getParameter('application_url') . '/restcomm/' . $this->moduleInstance->getAdapter()->getAdapterId() . '/getNext?stepId=' . $this->moduleInstance->getModuleInstanceId() . '&rand=' . rand(100, 99999)));
        } else if (count($connections) == 1) {

        }
        return $this->rcml;
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
        $this->container->get('logger')->debug('Populating Message',[$message]);
        if(preg_match_all('/{{(.*?)}}/',$message, $matches)>=1)
        {
            $this->container->get('logger')->debug('Say Matches => ',$matches);
            array_shift($matches);
            $this->container->get('logger')->debug('Say Matches => ',$matches[0]);
            $this->container->get('logger')->debug('Say Matches Variables => ',$this->variables);
            foreach ($matches[0] as $matchItem)
            {
                $this->container->get('logger')->debug('Say Matches Seach => ',[$matchItem]);
                if(array_key_exists($matchItem,$this->variables))
                {
                    $message = str_replace('{{'.$matchItem.'}}',$this->variables[$matchItem], $message);
                }
            }
        }
        $this->container->get('logger')->debug('Say Matches => ',[$message]);
        return $message;
    }

}