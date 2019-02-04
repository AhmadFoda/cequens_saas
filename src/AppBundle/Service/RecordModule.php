<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 10/15/2017
 * Time: 1:17 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Module;
use AppBundle\Entity\WorkflowModuleInstance;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RecordModule
{
    private $module;
    private $moduleInstance;
    private $rcml;
    private $em;
    private $container;

    /**
     * PlayURLModule constructor.
     */
    public function __construct(Module $module,WorkflowModuleInstance $moduleInstance,\SimpleXMLElement $rcml, EntityManager $entityManager, ContainerInterface $container)
    {
        $this->module = $module;
        $this->rcml = $rcml;
        $this->moduleInstance = $moduleInstance;
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function getRcml($input = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getWorkflowModuleInstanceSettings();
        $node = $this->rcml->addChild('Record','');
        foreach ($moduleInstanceSettings as $module_instance_setting)
        {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name)
            {
                case 'maxLength':
                    $node->addAttribute('maxLength',$value);
                    break;
                case 'finishOnKey':
                    $moduleInstanceSettingOption = $this->em->getRepository('AppBundle:ModuleSettingOption')->find($value);
                    $node->addAttribute('finishOnKey',$moduleInstanceSettingOption->getValue());
                    break;
            }
        }
        $node->addAttribute(
            'action',
            htmlentities($this->container->getParameter('application_url').'/restcomm/'.$this->moduleInstance->getWorkflow()->getWorkflowId().'/recording?stepId='.$this->moduleInstance->getWorkflowModuleInstanceId())
        );
        $node->addAttribute('method','GET');
	    $node = $this->rcml->addChild('Redirect',htmlentities($this->container->getParameter('application_url').'/restcomm/'.$this->moduleInstance->getAdapter()->getAdapterId().'/getNext?stepId=' . $this->moduleInstance->getModuleInstanceId().'&rand='.rand(100,99999)));
        return $this->rcml;
    }

}