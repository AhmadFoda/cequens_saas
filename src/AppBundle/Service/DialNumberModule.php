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
use AppBundle\Repository\WorkflowModuleInstanceRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DialNumberModule
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
    public function __construct(Module $module,ModuleInstance $moduleInstance,\SimpleXMLElement $rcml, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
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
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $node = null;
        $logger = $this->container->get('logger');
        $logger->debug('Inside Dial collected inputs is => ',array($this->collectedInputs));
        foreach ($moduleInstanceSettings as $module_instance_setting)
        {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name)
            {
                case 'dialNumber':
                	if(is_numeric($value))
	                {

	                }
	                else
	                {
	                	if(array_key_exists('collected_inputs',$this->collectedInputs))
		                {
		                	if(array_key_exists($value,$this->collectedInputs['collected_inputs']))
			                {
			                	$value = $this->collectedInputs['collected_inputs'][$value];
			                }
		                }
	                }
                    $node = $this->rcml->addChild('Dial',$value);
                    break;
                case 'dialRecord':
                    $moduleInstanceSettingOption = $this->em->getRepository('AppBundle:ModuleSettingOption')->find($value);
                    if($moduleInstanceSettingOption->getValue()=='Yes')
                    {
                        $node->addAttribute('record', 'true');
                    }
                    else
                    {
	                    $node->addAttribute('record', 'false');
                    }
                    break;
                case 'dialCallerId':
	                if(is_numeric($value))
	                {

	                }
	                else
	                {
		                if(array_key_exists('collected_inputs',$this->collectedInputs))
		                {
			                if(array_key_exists($value,$this->collectedInputs['collected_inputs']))
			                {
				                $value = $this->collectedInputs['collected_inputs'][$value];
			                }
		                }
	                }
                	$node->addAttribute('callerId',$value);
                    break;
            }

        }
        $node->addAttribute(
            'action',
            htmlentities($this->container->getParameter('application_url').'/restcomm/'.$this->moduleInstance->getAdapter()->getAdapterId().'/recording?stepId='.$this->moduleInstance->getModuleInstanceId())
        );
        $node->addAttribute('method','POST');
        return $this->rcml;
    }

}