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
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HangupModule
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
        $node = null;
	    $node = $this->rcml->addChild('Hangup');
        return $this->rcml;
    }

    public function initializeVariables($cacheService=null, $key)
    {
        $result = $cacheService->addCapturedDigitsToCache($key);
        $this->variables = $result['collected_inputs'];
    }

}