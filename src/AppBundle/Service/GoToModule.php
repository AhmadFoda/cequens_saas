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
use AppBundle\Entity\Workflow;
use AppBundle\Entity\WorkflowModuleInstance;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GoToModule
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
        $node = null;
        foreach ($moduleInstanceSettings as $module_instance_setting)
        {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name)
            {
                case 'moduleId':
                    $workflowUrl = $this->container->getParameter('application_url').'/restcomm/'.$value.'/rcml?rand='.rand(10000,999999);
                    $node = $this->rcml->addChild('Redirect',htmlentities($workflowUrl));
                    break;
            }
        }
        return $this->rcml;
    }

}