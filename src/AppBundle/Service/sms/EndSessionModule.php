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
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EndSessionModule
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
	public function __construct(Module $module,ModuleInstance $moduleInstance, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
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

        return ['code' => 200, 'body' => []];
    }

}