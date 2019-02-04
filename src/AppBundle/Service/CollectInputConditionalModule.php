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
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CollectInputConditionalModule
{
    private $module;
    private $moduleInstance;
    private $rcml;
    private $em;
    private $container;
	private $collectedInputs;

    /**
     * PlayURLModule constructor.
     */
    public function __construct(Module $module,WorkflowModuleInstance $moduleInstance,\SimpleXMLElement $rcml, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
    {
        $this->module = $module;
        $this->rcml = $rcml;
        $this->moduleInstance = $moduleInstance;
        $this->em = $entityManager;
        $this->container = $container;
        $this->collectedInputs = $collectedInputs;
    }

    public function getRcml($input = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getWorkflowModuleInstanceSettings();
        $node = $this->rcml->addChild('Gather', '');
        $continueToWorkflowId = '';
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
                case 'continueTo':
                    $continueToWorkflowId = $value;
            }
        }
        $node->addAttribute('action', htmlentities($this->container->getParameter('application_url').'/restcomm/' . $this->moduleInstance->getWorkflow()->getWorkflowId(). '/collectInput?stepId=' . $this->moduleInstance->getWorkflowModuleInstanceId()));
        $node->addAttribute('method', 'GET');
        if(!empty($continueToWorkflowId))
        {
            $continueToWorkflowUrl = $this->container->getParameter('application_url').'/restcomm/'.$continueToWorkflowId.'/rcml?rand='.rand(10000,999999);
            $this->rcml->addChild('Redirect', htmlentities($continueToWorkflowUrl));
        }
        return $this->rcml;
    }

}