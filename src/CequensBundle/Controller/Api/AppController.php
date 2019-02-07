<?php

namespace CequensBundle\Controller\Api;

use CequensBundle\Service\CacheService;
use CequensBundle\Service\RestcommService;
use OAuth2\OAuth2Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends ApiBaseController
{
    protected $logger;
    protected $cacheService;

    public function __construct(LoggerInterface $logger, CacheService $cacheService)
    {
        $this->logger = $logger;
        $this->cacheService = $cacheService;
    }

    /**
     * @Route("/{appToken}",name="trigger_api_app")
     * @Method({"POST"})
     */
    public function triggerAppAction(Request $request, $appToken)
    {
        $result = ['success' => false, 'data' => '', 'msg' => ''];
        $param = $request->request->all();
        $application = $this->getDoctrine()->getRepository('CequensBundle:Application')->findOneBy(['applicationToken' => $appToken, 'userId' => $this->getUserId()]);
        if (!empty($application)) {
            $from = $param['from'];
            $to = $param['to'];
            $msg = $param['msg'];
            $sysTemVariables = [
                'sys.to' => $to,
                'sys.from' => $from,
                'sys.msg' => $msg
            ];
            $adapterId = $application->getAdapterId();
            $campaign = $this->getDoctrine()->getRepository('AppBundle:Adapter')->findOneBy(array('adapterId' => $adapterId));
            $this->cacheService->setPrefix('sms');
            $cacheObj = $this->cacheService->getDataArrayFromCache($from . '-' . $to);
            $currentStep = '';
            if ($campaign) {
                if ($campaign->getIsActive()) {
                    if (!empty($cacheObj)) {
                        $currentStep = (array_key_exists('currentStep', $cacheObj)) ? $cacheObj['currentStep'] : '';
                    }

                    $continueExecution = true;
                    if (empty($currentStep)) {
                        $this->logger->debug('CurrentStep Not Found in cache');
                        $campaignModuleInstances = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->getNextAdapters($adapterId, $currentStep);
                        $nextModuleInstance = $campaignModuleInstances[0];

                    } else {
                        $this->logger->debug('CurrentStep Found in cache with id => ', [$currentStep]);
                        $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->findOneBy(['moduleInstanceId' => $currentStep]);
                    }
                    $this->cacheService->setSubKey($from . '-' . $to, 'from', $from);
                    $this->cacheService->setSubKey($from . '-' . $to, 'to', $to);
                    $this->cacheService->setSubKey($from . '-' . $to, 'currentStep', $nextModuleInstance->getModuleInstanceId());
                    do {
                        $campaign_module_instance = $nextModuleInstance;
                        $module = $campaign_module_instance->getModule();
                        $moduleClassName = $module->getProcessorClassName();
                        $moduleClass = new $moduleClassName(
                            $module,
                            $campaign_module_instance,
                            $this->getDoctrine()->getManager(),
                            $this->container,
                            [],
                            $this->get('CequensBundle\Service\RestcommService')
                        );
                        $moduleClass->setSessionId($from . '-' . $to);
                        $this->logger->debug('[AppController] executing application ' . $application->getId() . ' Step ' . $campaign_module_instance->getModuleInstanceId());
                        $this->logger->debug('[AppController] executing application ' . $application->getId() . ' Step Name' . $module->getName());
                        $this->cacheService->setSubKey($from . '-' . $to, 'currentStep', $campaign_module_instance->getModuleInstanceId());
                        if ($module->getUiName() == 'waitForResponse') {
                            $sysTemVariables['currentStep'] = $campaign_module_instance->getModuleInstanceId();
                            $currentStepSettings = $campaign_module_instance->getModuleInstanceSettings();
                            $currentStepSettingsArray = [];
                            foreach ($currentStepSettings as $currentStepSetting) {
                                $name = $currentStepSetting->getModuleSetting()->getName();
                                $value = $currentStepSetting->getValue();
                                switch ($name)
                                {
                                    case 'expireAfter':
                                        $currentStepSettingsArray['expireAfter'] = $value;
                                        break;
                                    case 'moduleId' :
                                        $currentStepSettingsArray['expireFallback'] = $value;
                                        break;
                                }
                            }
                            $connectionsCurrentStep = $campaign_module_instance->getModuleInstanceConnections();
                            foreach ($connectionsCurrentStep as $connectionCurrentStep) {
                                $nextModule = $connectionCurrentStep->getTargetModuleInstanceId();
                            }
                            $this->logger->debug('Wait For Response Next Module ', [$nextModule]);
                            $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->findOneBy(['moduleInstanceId' => $nextModule]);
                            $this->cacheService->setSubKey($from . '-' . $to, 'currentStep', $nextModuleInstance->getModuleInstanceId());
                            $expire_stamp = date('Y-m-d H:i:s', strtotime("+".$currentStepSettingsArray['expireAfter']." second"));
                            $this->cacheService->setSubKey($from . '-' . $to, 'expireAfter', $expire_stamp);
                            $this->cacheService->setSubKey($from . '-' . $to, 'expireFallback', $currentStepSettingsArray['expireFallback']);
                            $result = ['success' => true, 'msg' => 'Flow executed successfully Entering Wait Response State', 'data' => []];
                            $continueExecution = false;
                            break;
                        }
                        $moduleClass->initializeVariables($this->cacheService, $from . '-' . $to, $sysTemVariables);
                        $resultExecute = $moduleClass->execute($param);
                        if ($module->getUiName() == 'endSession') {
                            $continueExecution = false;
                            $result = ['success' => true, 'msg' => 'Flow executed successfully ending session', 'data' => []];
                            $this->cacheService->removeKey($from . '-' . $to);
                            //$this->cacheService->removeKey();
                        }
                        $this->logger->debug('[AppController] Execute Response ', $resultExecute);
                        if ($continueExecution) {
                            $nextModuleInstances = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->getNextAdapters($adapterId, $campaign_module_instance->getModuleInstanceId());
                            $nextModuleInstance = (!empty($nextModuleInstances)) ? $nextModuleInstances[0] : null;
                            if (array_key_exists('nextModule', $resultExecute['body'])) {
                                $this->logger->debug('[AppController] Jumping to Step  ', [$resultExecute['body']['nextModule']]);
                                $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->findOneBy(['moduleInstanceId' => $resultExecute['body']['nextModule']]);
                            }
                        } else {

                        }
                    } while ($continueExecution);

                } else {
                    $result = ['success' => false, 'msg' => 'Workflow is not active', 'data' => []];
                }
            } else {
                $result = ['success' => false, 'msg' => 'Wrong workflow ID', 'data' => []];
            }
        } else {
            $result = ['success' => false, 'msg' => 'Wrong Application Token'];
        }

        return new JsonResponse($result);
    }

}
