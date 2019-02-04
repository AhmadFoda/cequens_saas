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
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PlayAudioModule
{
    private $module;
    private $moduleInstance;
    private $rcml;
    private $em;
    private $container;

    /**
     * PlayURLModule constructor.
     */
    public function __construct(Module $module,ModuleInstance $moduleInstance,\SimpleXMLElement $rcml, EntityManager $entityManager, ContainerInterface $container)
    {
        $this->module = $module;
        $this->rcml = $rcml;
        $this->moduleInstance = $moduleInstance;
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function getRcml($input = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $node = null;
        foreach ($moduleInstanceSettings as $module_instance_setting)
        {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name)
            {
                case 'audioFileId':
                    $node = $this->rcml->addChild('Play',htmlentities($this->container->getParameter('application_url').'/restcomm/playfileid?voice=male&file='.$value));
                    break;
                case 'loop':
                    if(intval($value)>0)
                    {
                        $node->addAttribute($name,$value);
                    }
                    break;
            }
        }
	    $node = $this->rcml->addChild('Redirect',htmlentities($this->container->getParameter('application_url').'/restcomm/'.$this->moduleInstance->getAdapter()->getAdapterId().'/getNext?stepId=' . $this->moduleInstance->getModuleInstanceId()));
        return $this->rcml;
    }

}