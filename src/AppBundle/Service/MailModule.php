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
use CequensBundle\Service\CacheService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailModule
{
    private $module;
    private $moduleInstance;
    private $rcml;
    private $em;
    private $container;
    private $collectedInputs;
    private $variables;
    private $sessionId;
    private $mailer;

    /**
     * PlayURLModule constructor.
     */
    public function __construct(Module $module, ModuleInstance $moduleInstance, \SimpleXMLElement $rcml, EntityManager $entityManager, ContainerInterface $container, array $collectedInputs = array())
    {
        $this->module = $module;
        $this->rcml = $rcml;
        $this->moduleInstance = $moduleInstance;
        $this->em = $entityManager;
        $this->container = $container;
        $this->collectedInputs = $collectedInputs;
        $this->variables = [];
        $this->mailer =  $this->container->get('mailer');
    }

    public function getRcml($input = null)
    {
        $moduleInstanceSettings = $this->moduleInstance->getModuleInstanceSettings();
        $node = null;
        foreach ($moduleInstanceSettings as $module_instance_setting) {
            $name = $module_instance_setting->getModuleSetting()->getName();
            $value = $module_instance_setting->getValue();
            switch ($name) {
                case 'from':
                    $value = $this->populateStringFromVariables($value);
                    $serviceDescription['from'] = $value;
                    break;
                case 'to':
                    $value = $this->populateStringFromVariables($value);
                    $serviceDescription['to'] = $value;
                    break;
                case 'body':
                    $value = $this->populateStringFromVariables($value);
                    $serviceDescription['body'] = $value;
                    break;
            }
        }
        if (!empty($serviceDescription) && count($serviceDescription) > 0) {
            $query = http_build_query(array('from' => $serviceDescription['from'], 'to' => $serviceDescription['to'], 'body' => $serviceDescription['body']));
            $url = htmlentities(
                $this->container->getParameter('application_url') .
                '/restcomm/' . $this->moduleInstance->getAdapter()->getAdapterId() . '/custom?sessionId=' . $this->getSessionId() . '&stepId=' . $this->moduleInstance->getModuleInstanceId() .
                '&' . $query
            );
            $node = $this->rcml->addChild('Redirect', $url);
            $node->addAttribute('method', 'GET');
        }

        return $this->rcml;
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

    public function execute($param)
    {
        $from = $param['from'];
        $to = $param['to'];
        $message = $param['body'];
        $emailMessage = \Swift_Message::newInstance()->setSubject('Workflow Email')
            ->setFrom($from)
            ->setTo($to)
            ->setBody($message);
        $response = $this->mailer->send($emailMessage);

        return ['code' => $response, 'body' => []];
    }

}