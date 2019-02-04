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
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExternalServiceModule
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


    public function getRcml()
    {
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $node = null;
        $serviceDescription = [];
        foreach ($moduleInstanceSettings as $module_instance_setting) {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name) {
                case 'httpUrl':
                    $serviceDescription['url'] = $value;
                    break;
                case 'httpMethod':
                    $moduleInstanceSettingOption = $this->em->getRepository('AppBundle:ModuleSettingOption')->find($value);
                    $serviceDescription['method'] = $moduleInstanceSettingOption->getValue();
                    break;
                case 'httpHeaders':
                    $headers = explode(":", $value);
                    $serviceDescription['headers'][$headers[0]] = $headers[1];
                    break;
                case 'httpParamMap':
                    $parameters = explode(":", $value);
                    $parameterValue = $this->populateStringFromVariables($parameters[1]);
                    $serviceDescription['param'][$parameters[0]] = $parameterValue;
                    break;
            }
        }
        if (!empty($serviceDescription) && count($serviceDescription) > 0) {
            $query = http_build_query(array('headers' => $serviceDescription['headers'], 'params' => $serviceDescription['param'], 'method' => $serviceDescription['method']));
            $url = htmlentities(
                $this->container->getParameter('application_url') .
                '/restcomm/' . $this->moduleInstance->getAdapter()->getAdapterId() . '/custom?sessionId='.$this->getSessionId().'&stepId=' . $this->moduleInstance->getModuleInstanceId() .
                '&serviceUrl=' . urlencode($serviceDescription['url']) .
                '&' . $query
            );
            $node = $this->rcml->addChild('Redirect', $url);
            $node->addAttribute('method', 'GET');
        }

        return $this->rcml;
    }

    public function execute($parameters = null)
    {
        $param = [];
        $serviceUrl = $param['serviceUrl'];
        $headers = $param['headers'];
        $data = $param['params'];
        $method = strtolower($param['method']);
        if ($method == 'post') {
            $response = \Unirest\Request::post($serviceUrl, $headers, $data);
        } else {
            $response = \Unirest\Request::get($serviceUrl, $headers, $data);
        }

        return ['code' => $response->code, 'body' => $response->body];
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

}