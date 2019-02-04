<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 10/15/2017
 * Time: 1:18 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Module;
use AppBundle\Entity\ModuleInstance;
use AppBundle\Entity\WorkflowModuleInstance;
use CequensBundle\Service\CacheService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CollectInputModule
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
	public function __construct(Module $module,ModuleInstance $moduleInstance,\SimpleXMLElement $rcml, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
	{
		$this->module = $module;
		$this->rcml = $rcml;
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



    public function getRcml($input = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $node = $this->rcml->addChild('Gather', '');
        foreach ($moduleInstanceSettings as $module_instance_setting) {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name) {
                case 'finishOnKey':
                    $moduleInstanceSettingOption = $this->em->getRepository('AppBundle:ModuleSettingOption')->find($value);
                    $node->addAttribute('finishOnKey', $moduleInstanceSettingOption->getValue());
                    break;
                case 'timeout':
                    $node->addAttribute('timeout', $value);
                    break;
            }
        }
        $node->addAttribute('action', htmlentities($this->container->getParameter('application_url').'/restcomm/'.$this->moduleInstance->getAdapter()->getAdapterId().'/getNext?sessionId='.$this->getSessionId()).'&'.htmlentities('stepId=' . $this->moduleInstance->getModuleInstanceId().'&rand='.rand(100,99999)));
	    //$node->addAttribute('action', htmlentities($this->container->getParameter('application_url').'/restcomm/' . $this->moduleInstance->getAdapter()->getAdapterId(). '/collectInput?stepId=' . $this->moduleInstance->getModuleInstanceId()));
        $node->addAttribute('method', 'GET');
        return $this->rcml;
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

}