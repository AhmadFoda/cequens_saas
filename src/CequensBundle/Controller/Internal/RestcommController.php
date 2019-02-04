<?php

namespace CequensBundle\Controller\Internal;

use CequensBundle\Service\CacheService;
use CequensBundle\Service\RestcommService;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestcommController extends Controller
{
    protected $restcommService;

    protected $logger;

    protected $cacheService;

    /**
     * RestcommController constructor.
     */
    public function __construct(RestcommService $restcommService, LoggerInterface $logger, CacheService $cacheService)
    {
        $this->restcommService = $restcommService;
        $this->logger = $logger;
        $this->cacheService = $cacheService;
    }

    /**
     * @Route("/{adapterId}/getNext",name="get")
     * @Method({"GET"})
     */
    public function indexAction(Request $request, $adapterId)
    {
        $moduleInstanceId = $request->get('stepId', '');
        $conditionValue = $request->get('Digits', null);
        $callId = $request->get('CallSid', '');
        $rcml = new \SimpleXMLElement("<Response></Response>");
        $currentModuleInstance =
            $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceId);
        $moduleInstanceConnections = $currentModuleInstance->getModuleInstanceConnections();
        $moduleInstanceSettings = $currentModuleInstance->getModuleInstanceSettings();
        $variableName = null;
        foreach ($moduleInstanceSettings as $moduleInsSetting) {
            $modSetting = $moduleInsSetting->getModuleSetting();
            if ($modSetting->getName() == 'collectedInputVariableName') {
                $variableName = $moduleInsSetting->getValue();
                $this->cacheService->addCapturedDigitsToCache($callId, $conditionValue, $variableName);
            }
        }
        if (count($moduleInstanceConnections) > 1) {
            foreach ($moduleInstanceConnections as $moduleInstanceConnection) {
                if ($moduleInstanceConnection->getConditionValue() == $conditionValue) {
                    $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                        $moduleInstanceConnection->getTargetModuleInstanceId()
                    );
                    $module = $nextModuleInstance->getModule();
                    $moduleClassName = $module->getProcessorClassName();
                    $moduleClass = new $moduleClassName(
                        $module,
                        $nextModuleInstance,
                        $rcml,
                        $this->getDoctrine()->getManager(),
                        $this->container
                    );
                    $moduleClass->setSessionId($callId);
                    $moduleClass->initializeVariables($this->cacheService, $callId);
                    $rcml = $moduleClass->getRcml();

                    $nextModuleInstanceConnections = $nextModuleInstance->getModuleInstanceConnections();
                    foreach ($nextModuleInstanceConnections as $nextModuleInstanceConnection) {
                        $nextModuleInstanceConnectionInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                            $nextModuleInstanceConnection->getTargetModuleInstanceId()
                        );
                        $module = $nextModuleInstanceConnectionInstance->getModule();
                        $moduleClassName = $module->getProcessorClassName();
                        $moduleClass = new $moduleClassName(
                            $module,
                            $nextModuleInstanceConnectionInstance,
                            $rcml,
                            $this->getDoctrine()->getManager(),
                            $this->container
                        );
                        $moduleClass->setSessionId($callId);
                        $moduleClass->initializeVariables($this->cacheService, $callId);
                        $rcml = $moduleClass->getRcml();
                    }
                    break;
                }
            }
        } elseif (count($moduleInstanceConnections) == 1) {
            $moduleInstanceConnection = $moduleInstanceConnections[0];
            $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                $moduleInstanceConnection->getTargetModuleInstanceId()
            );
            $module = $nextModuleInstance->getModule();
            $moduleClassName = $module->getProcessorClassName();
            $moduleClass = new $moduleClassName(
                $module,
                $nextModuleInstance,
                $rcml,
                $this->getDoctrine()->getManager(),
                $this->container
            );
            $moduleClass->setSessionId($callId);
            $moduleClass->initializeVariables($this->cacheService, $callId);
            $rcml = $moduleClass->getRcml();

            $nextModuleInstanceConnections = $nextModuleInstance->getModuleInstanceConnections();
            foreach ($nextModuleInstanceConnections as $nextModuleInstanceConnection) {
                $nextModuleInstanceConnectionInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                    $nextModuleInstanceConnection->getTargetModuleInstanceId()
                );
                $module = $nextModuleInstanceConnectionInstance->getModule();
                $moduleClassName = $module->getProcessorClassName();
                $moduleClass = new $moduleClassName(
                    $module,
                    $nextModuleInstanceConnectionInstance,
                    $rcml,
                    $this->getDoctrine()->getManager(),
                    $this->container
                );
                $moduleClass->setSessionId($callId);
                $moduleClass->initializeVariables($this->cacheService, $callId);
                $rcml = $moduleClass->getRcml();
            }
        }


        $response = new Response($rcml->asXML());
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * @Route("/{adapterId}/getNext",name="postNext")
     * @Method({"POST"})
     */
    public function postIndexAction(Request $request, $adapterId)
    {
        $moduleInstanceId = $request->request->get('stepId', '');
        $conditionValue = $request->request->get('Digits', null);
        $callId = $request->request->get('CallSid', '');
        $rcml = new \SimpleXMLElement("<Response></Response>");
        $currentModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceId);
        $moduleInstanceConnections = $currentModuleInstance->getModuleInstanceConnections();
        $moduleInstanceSettings = $currentModuleInstance->getModuleInstanceSettings();
        $variableName = null;
        foreach ($moduleInstanceSettings as $moduleInsSetting) {
            $modSetting = $moduleInsSetting->getModuleSetting();
            if ($modSetting->getName() == 'collectedInputVariableName') {
                $variableName = $moduleInsSetting->getValue();
                $this->cacheService->addCapturedDigitsToCache($callId, $conditionValue, $variableName);
            }
        }
        if (count($moduleInstanceConnections) > 1) {
            foreach ($moduleInstanceConnections as $moduleInstanceConnection) {
                if ($moduleInstanceConnection->getConditionValue() == $conditionValue) {
                    $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                        $moduleInstanceConnection->getTargetModuleInstanceId()
                    );
                    $module = $nextModuleInstance->getModule();
                    $moduleClassName = $module->getProcessorClassName();
                    $moduleClass = new $moduleClassName(
                        $module,
                        $nextModuleInstance,
                        $rcml,
                        $this->getDoctrine()->getManager(),
                        $this->container
                    );
                    $moduleClass->setSessionId($callId);
                    $moduleClass->initializeVariables($this->cacheService, $callId);
                    $rcml = $moduleClass->getRcml();
                    break;
                }
            }
        } elseif (count($moduleInstanceConnections) == 1) {
            $moduleInstanceConnection = $moduleInstanceConnections[0];
            $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                $moduleInstanceConnection->getTargetModuleInstanceId()
            );
            $module = $nextModuleInstance->getModule();
            $moduleClassName = $module->getProcessorClassName();
            $moduleClass = new $moduleClassName(
                $module,
                $nextModuleInstance,
                $rcml,
                $this->getDoctrine()->getManager(),
                $this->container
            );
            $moduleClass->setSessionId($callId);
            $moduleClass->initializeVariables($this->cacheService, $callId);
            $rcml = $moduleClass->getRcml();
        }

        $response = new Response($rcml->asXML());
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * Get Workflow RCML
     *
     * @Route("/{adapterId}/rcml", name="get_post_restcomm_rcml")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWorkflowRcml(Request $request, $adapterId)
    {
        $this->logger->debug('RETURNNNN RCML POST Request => ', $request->request->all());
        $this->logger->debug('RETURNNNN RCML GET Request => ', $request->query->all());
        $callSid = $request->request->get('CallSid');
        $callTo = $request->request->get('To');
        $sysTemVariables = ['sys.to'=>$callTo];
        $callSid = (!empty($callSid)) ? $callSid : $request->get('CallSid');
        $rcml = new \SimpleXMLElement("<Response></Response>");
        $campaign =
            $this->getDoctrine()->getRepository('AppBundle:Adapter')->findOneBy(array('adapterId' => $adapterId));
        if ($campaign) {
            if ($campaign->getIsActive()) {
                $campaignModuleInstances = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->findBy(
                    array('adapter' => $campaign)
                );
                foreach ($campaignModuleInstances as $campaign_module_instance) {
                    $module = $campaign_module_instance->getModule();
                    $moduleClassName = $module->getProcessorClassName();
                    $moduleClass = new $moduleClassName(
                        $module,
                        $campaign_module_instance,
                        $rcml,
                        $this->getDoctrine()->getManager(),
                        $this->container
                    );
                    $moduleClass->setSessionId($callSid);
                    $moduleClass->initializeVariables($this->cacheService, $callSid, $sysTemVariables);
                    $rcml = $moduleClass->getRcml();
                }
            }
        }
        $response = new Response($rcml->asXML());
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * Get Workflow RCML
     *
     * @Route("/{adapterId}/start",options={"expose"=true}, name="start_adapter_call")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testCallAction(Request $request, $adapterId)
    {
        $result = $this->restcommService->triggerCall(
            $request->get('from', '201020737333'),
            $request->get('destination', ''),
            $adapterId
        );
        if (array_key_exists('sid', $result)) {
            $this->cacheService->addCapturedDigitsToCache($result['sid']);
        }
        return new JsonResponse(array($result));
    }

    /**
     * @Route("/playfileid",  options={"expose"=true}, name="play_file_id_route")
     * @param Request $request
     */
    public function playfileidAction()
    {
        $parameter1 = $_GET['file'];
        $em = $this->getDoctrine()->getManager();
        $audioFile = $em->getRepository('CequensBundle:File')->find($parameter1);
        if ($audioFile) {
            //$audioFileObj= $em->getRepository('CequensBundle:File')->find($audioFile->getFileId());
            //$parameter1 = str_replace($this->container->getParameter('application_url').'/playfile?voice=male&file=','',$audioFileObj->getFileName());
            //$root = str_replace('/app', '/', $this->container->getParameter('kernel.root_dir'));
            //$destination_name_ulaw = $root . '/web/' . 'digits/' . str_replace('__', '', $parameter1 . '.wav');
            $action = 'play';
            //$file = $destination_name_ulaw;
            $file = $audioFile->getFilePath();
            $caller = '/play';

            if (empty ($action) || empty ($file)) {
                die ("Something went wrong");
            }

            // Open the file

            $fileName = $file;
            $fd = @fopen($file, "rb");

            if ($fd === false) {
                Header("Location: " . $caller . ".php?error=1");
                die ();
            }

            // Send the headers to the browser

            $len = filesize($fileName);

            Header("Accept-Ranges: bytes");
            Header("Content-Length: $len");
            Header("Keep-Alive: timeout=10, max=100");
            Header("Connection: Keep-Alive");
            Header("Content-Type: audio/x-wav");

            if ($action == "download") {
                Header("Content-Disposition: attachment; filename=\"$fileName\"");
                Header("Content-Description: File Transfer");
            }

            // Transmit the file in 8K blocks

            while (!feof($fd) && (connection_status() == 0)) {
                set_time_limit(0);
                print (fread($fd, 1024 * 8));
                flush();
            }

            fclose($fd);
            die;
        }
    }

    /**
     * @Route("/playfile",  options={"expose"=true}, name="play_file_route")
     * @param Request $request
     */
    public function playfileAction()
    {
        $parameter1 = $_GET['file'];
        $root = str_replace('/app', '/', $this->container->getParameter('kernel.root_dir'));
        $destination_name_ulaw = $root . '/web/' . 'digits/' . str_replace('__', '', $parameter1 . '.wav');
        $action = 'play';
        $file = $destination_name_ulaw;
        $caller = '/play';

        if (empty ($action) || empty ($file)) {
            die ("Something went wrong");
        }

        // Open the file

        $fileName = $file;
        $fd = @fopen($file, "rb");

        if ($fd === false) {
            Header("Location: " . $caller . ".php?error=1");
            die ();
        }

        // Send the headers to the browser

        $len = filesize($fileName);

        Header("Accept-Ranges: bytes");
        Header("Content-Length: $len");
        Header("Keep-Alive: timeout=10, max=100");
        Header("Connection: Keep-Alive");
        Header("Content-Type: audio/x-wav");

        if ($action == "download") {
            Header("Content-Disposition: attachment; filename=\"$fileName\"");
            Header("Content-Description: File Transfer");
        }

        // Transmit the file in 8K blocks

        while (!feof($fd) && (connection_status() == 0)) {
            set_time_limit(0);
            print (fread($fd, 1024 * 8));
            flush();
        }

        fclose($fd);
        die;
    }

    /**
     * Workflow Custom Widget
     *
     * @Route("/{adapterId}/custom", name="get_custom_widget_rcml")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function workflowCustomWidgetGetAction(Request $request, $adapterId)
    {
        $this->logger->debug('Captured Custom Query String', $request->query->all());
        $callSid = $request->query->get('sessionId');
        $serviceParam = $request->query->all();
        $moduleInstanceId = $request->get('stepId', '');
        $rcml = new \SimpleXMLElement("<Response></Response>");
        $conditionValue = '';
        $currentModuleInstance =
            $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceId);

        $currentModule = $currentModuleInstance->getModule();
        $currentModuleClassName = $currentModule->getProcessorClassName();
        $currentModuleClass = new $currentModuleClassName(
            $currentModule,
            $currentModuleInstance,
            $rcml,
            $this->getDoctrine()->getManager(),
            $this->container
        );
        if (method_exists($currentModuleClass, 'execute')) {
            $response = $currentModuleClass->execute($serviceParam);
            $this->logger->debug('External Service Response => ', $response);
            $moduleInstanceSettings = $currentModuleInstance->getModuleInstanceSettings();
            $variableName = null;
            foreach ($moduleInstanceSettings as $moduleInsSetting) {
                $modSetting = $moduleInsSetting->getModuleSetting();
                if ($modSetting->getName() == 'collectedInputVariableName') {
                    $variableName = $moduleInsSetting->getValue();
                    if($response['code']==200)
                    {
                        foreach ($response['body'] as $responseKey => $responseValue) {
                            $this->cacheService->addCapturedDigitsToCache($callSid, $responseValue, $variableName . '.' . $responseKey);
                        }
                    }
                }
            }

        }
        $moduleInstanceConnections = $currentModuleInstance->getModuleInstanceConnections();
        if (count($moduleInstanceConnections) > 1) {
            foreach ($moduleInstanceConnections as $moduleInstanceConnection) {
                if ($moduleInstanceConnection->getConditionValue() == $conditionValue) {
                    $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                        $moduleInstanceConnection->getTargetModuleInstanceId()
                    );
                    $module = $nextModuleInstance->getModule();
                    $moduleClassName = $module->getProcessorClassName();
                    $moduleClass = new $moduleClassName(
                        $module,
                        $nextModuleInstance,
                        $rcml,
                        $this->getDoctrine()->getManager(),
                        $this->container
                    );
                    $moduleClass->setSessionId($callSid);
                    $moduleClass->initializeVariables($this->cacheService, $callSid);
                    $rcml = $moduleClass->getRcml();

                    $nextModuleInstanceConnections = $nextModuleInstance->getModuleInstanceConnections();
                    foreach ($nextModuleInstanceConnections as $nextModuleInstanceConnection) {
                        $nextModuleInstanceConnectionInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                            $nextModuleInstanceConnection->getTargetModuleInstanceId()
                        );
                        $module = $nextModuleInstanceConnectionInstance->getModule();
                        $moduleClassName = $module->getProcessorClassName();
                        $moduleClass = new $moduleClassName(
                            $module,
                            $nextModuleInstanceConnectionInstance,
                            $rcml,
                            $this->getDoctrine()->getManager(),
                            $this->container
                        );
                        $moduleClass->setSessionId($callSid);
                        $moduleClass->initializeVariables($this->cacheService, $callSid);
                        $rcml = $moduleClass->getRcml();
                    }
                    break;
                }
            }
        } elseif (count($moduleInstanceConnections) == 1) {
            $moduleInstanceConnection = $moduleInstanceConnections[0];
            $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                $moduleInstanceConnection->getTargetModuleInstanceId()
            );
            $module = $nextModuleInstance->getModule();
            $moduleClassName = $module->getProcessorClassName();
            $moduleClass = new $moduleClassName(
                $module,
                $nextModuleInstance,
                $rcml,
                $this->getDoctrine()->getManager(),
                $this->container
            );
            $moduleClass->setSessionId($callSid);
            $moduleClass->initializeVariables($this->cacheService, $callSid);
            $rcml = $moduleClass->getRcml();

            $nextModuleInstanceConnections = $nextModuleInstance->getModuleInstanceConnections();
            foreach ($nextModuleInstanceConnections as $nextModuleInstanceConnection) {
                $nextModuleInstanceConnectionInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                    $nextModuleInstanceConnection->getTargetModuleInstanceId()
                );
                $module = $nextModuleInstanceConnectionInstance->getModule();
                $moduleClassName = $module->getProcessorClassName();
                $moduleClass = new $moduleClassName(
                    $module,
                    $nextModuleInstanceConnectionInstance,
                    $rcml,
                    $this->getDoctrine()->getManager(),
                    $this->container
                );
                $moduleClass->setSessionId($callSid);
                $moduleClass->initializeVariables($this->cacheService, $callSid);
                $rcml = $moduleClass->getRcml();
            }

        }


        $response = new Response($rcml->asXML());
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * Workflow Custom Widget
     *
     * @Route("/{adapterId}/custom", name="post_custom_widget_rcml")
     * @Method({"POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function workflowCustomWidgetPostAction(Request $request, $adapterId)
    {
        $this->logger->debug('Captured Custom Query String', $request->query->all());
        $serviceParam = $request->query->all();
        $moduleInstanceId = $request->request->get('stepId', '');
        $conditionValue = $rcml = new \SimpleXMLElement("<Response></Response>");
        $currentModuleInstance =
            $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceId);

        $currentModule = $currentModuleInstance->getModule();
        $currentModuleClassName = $currentModule->getProcessorClassName();
        $currentModuleClass = new $currentModuleClassName(
            $currentModule,
            $currentModuleInstance,
            $rcml,
            $this->getDoctrine()->getManager(),
            $this->container
        );
        if (method_exists($currentModuleClass, 'execute')) {
            $response = $currentModuleClass->execute($serviceParam);
            $this->logger->debug('External Service Response => ', $response);
        }
        $moduleInstanceConnections = $currentModuleInstance->getModuleInstanceConnections();
        if (count($moduleInstanceConnections) > 1) {
            foreach ($moduleInstanceConnections as $moduleInstanceConnection) {
                if ($moduleInstanceConnection->getConditionValue() == $conditionValue) {
                    $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                        $moduleInstanceConnection->getTargetModuleInstanceId()
                    );
                    $module = $nextModuleInstance->getModule();
                    $moduleClassName = $module->getProcessorClassName();
                    $moduleClass = new $moduleClassName(
                        $module,
                        $nextModuleInstance,
                        $rcml,
                        $this->getDoctrine()->getManager(),
                        $this->container
                    );
                    $moduleClass->setSessionId($callSid);
                    $moduleClass->initializeVariables(null, $callSid);
                    $rcml = $moduleClass->getRcml();
                    break;
                }
            }
        } elseif (count($moduleInstanceConnections) == 1) {
            $moduleInstanceConnection = $moduleInstanceConnections[0];
            $nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find(
                $moduleInstanceConnection->getTargetModuleInstanceId()
            );
            $module = $nextModuleInstance->getModule();
            $moduleClassName = $module->getProcessorClassName();
            $moduleClass = new $moduleClassName(
                $module,
                $nextModuleInstance,
                $rcml,
                $this->getDoctrine()->getManager(),
                $this->container
            );
            $moduleClass->setSessionId($callSid);
            $moduleClass->initializeVariables(null, $callSid);
            $rcml = $moduleClass->getRcml();

        }


        $response = new Response($rcml->asXML());
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    /**
     * Workflow Custom Widget
     *
     * @Route("/ping", name="ping_rcml")
     * @Method({"POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pingAction(Request $request)
    {
        $this->logger->debug('PING Request Param => ', $request->request->all());
        $userId = $request->request->get('customer_id');
        $users =
            [
                '1234' => ['message'=>'your card has been activated successfully. thank you for using cequens banking services','name' => 'Karim Mohamed', 'id' => '1 2 3 4 5 6 7 8 9', 'credit' => '2456 Egyptian Pounds', 'mobile' => '201020737333'],
                '5555' => ['message'=>'your card has been activated successfully. thank you for using cequens banking services','name' => 'Amr Adel', 'id' => '1 2 3 4 5', 'credit' => '2456 Egyptian Pounds', 'mobile' => '201006761487'],
            ];

        if(!array_key_exists($userId,$users))
        {
            $message = ['message'=>'Sorry your card number and id is not correct. please contact our call center for further support'];
        }
        else
        {
            $message = $users[$userId];
        }

        return new JsonResponse($message, 200);
    }
}
