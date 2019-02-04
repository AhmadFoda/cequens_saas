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
use AppBundle\Entity\WorkflowModuleInstance;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BranchModule
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
     * BranchModule constructor.
     */
    public function __construct(Module $module, ModuleInstance $moduleInstance, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
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

    public function initializeVariables($cacheService = null, $key, $variables = null)
    {
        if (!empty($variables)) {
            foreach ($variables as $variableKey => $variableValue) {
                $result = $cacheService->addCapturedDigitsToCache($key, $variableValue, $variableKey);
            }

        } else {
            $result = $cacheService->addCapturedDigitsToCache($key);
        }

        $this->container->get('logger')->debug('Inittttttt Variables => ', [$result]);
        $this->variables = (!empty($result['collected_inputs'])) ? $result['collected_inputs'] : [];
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
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $param = [];
        foreach ($moduleInstanceSettings as $module_instance_setting) {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name) {
                case 'conditionVariable':
                    $value = $this->populateStringFromVariables($value);
                    $param['condition_variable'] = $value;
                    break;
                case 'conditionType':
                    $moduleInstanceSettingOption = $this->em->getRepository('AppBundle:ModuleSettingOption')->find($value);
                    $param['condition_type'] = $moduleInstanceSettingOption->getValue();
                    break;
            }
        }
        $validation = false;
        $nextModule = null;
        $exitLoop = false;
        $nextModuleInstanceConnections = $this->moduleInstance->getModuleInstanceConnections();
        foreach ($nextModuleInstanceConnections as $nextModuleInstanceConnection) {
            $nextModule = $nextModuleInstanceConnection->getTargetModuleInstanceId();
            switch ($param['condition_type']) {
                case 'Contains':
                    $this->container->get('logger')->debug('[BranchModule] Condition Type is contains');
                    if (strpos($param['condition_variable'], $nextModuleInstanceConnection->getConditionValue()) !== false) {
                        $validation = true;
                        $exitLoop = true;
                    }
                    break;
                case 'Starts With':
                    if(substr($nextModuleInstanceConnection->getConditionValue(), 0, strlen($param['condition_variable'])) === $param['condition_variable'])
                    {
                        $validation = true;
                        $exitLoop = true;
                        continue;
                    }
                    break;
                case 'Equals':
                    if ($nextModuleInstanceConnection->getConditionValue() === $param['condition_variable']) {
                        $validation = true;
                        $exitLoop = true;
                        continue;
                    }
                    break;
            }
            if($exitLoop)
            {
                break;
            }
        }
        return ['code' => 200, 'body' => ['validation'=>$validation, 'nextModule'=> $nextModule]];
    }
}