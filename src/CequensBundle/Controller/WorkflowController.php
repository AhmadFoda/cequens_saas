<?php

namespace CequensBundle\Controller;

use AppBundle\Service\AdapterService;
use CequensBundle\Service\WorkflowService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class WorkflowController extends Controller
{
    protected $workflowService;

    protected $logger;

    protected $adapterService;

    /**
     * WorkflowController constructor.
     *\
     * @param $workflowService
     */
    public function __construct(WorkflowService $workflowService, LoggerInterface $logger, AdapterService $adapterService)
    {
        $this->workflowService = $workflowService;
        $this->logger = $logger;
        $this->adapterService = $adapterService;
    }

    /**
     * @Route("/",name="workflow_list_applications")
     */
    public function indexAction(Request $request)
    {
        $filters = array();
        $submittedToken = $request->request->get('input_workflow_create_app_token');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $filters['user_id'] = $userId;
        $result = $this->workflowService->listAllApplications($filters);

        return $this->render(
            '@Cequens/Admin/pages/Workflow/list_workflows.html.twig',
            array('applications' => $result['data'])
        );
    }

    /**
     * @Route("/builder",name="workflow_create")
     */
    public function workflowBuilderAction(Request $request)
    {
        $filters = array();
        $submittedToken = $request->request->get('input_verify_create_app_token');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $filters['user_id'] = $userId;
        $result = array('data' => array());
        $properties_options = $this->workflowService->getPropertiesAndOptions();

        return $this->render(
            '@Cequens/Admin/pages/Workflow/create_workflows.html.twig',
            array('applications' => $result['data'], 'properties' => $properties_options['data'])
        );
    }

    /**
     * @Route("/builder/{id}",name="workflow_builder",requirements={"id" = "\d+"})
     */
    public function workflowLoadBuilderAction($id, Request $request)
    {
        $filters = array();
        $submittedToken = $request->request->get('input_verify_create_app_token');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $filters['user_id'] = $userId;
        $filters['id'] = $id;
        $result = $this->workflowService->listAllApplications($filters);
        if (empty($result['data'])) {
            return $this->redirectToRoute('workflow_list_applications');
        }
        $result = array('data' => $result['data'][0]);

        return $this->render(
            '@Cequens/Admin/pages/Workflow/workflows_builder.html.twig',
            array('applications' => $result['data'])
        );
    }

    /**
     * @Route("/doCreateApplication",name="workflow_do_create_application")
     * @Method({"POST"})
     */
    public function doCreateAction(Request $request)
    {
        $params = $request->request->all();
        $submittedToken = $request->request->get('input_workflow_create_app_token');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        if ($this->isCsrfTokenValid('workflow_create_app_token', $submittedToken)) {
            $result = $this->workflowService->createNewWorkflowApplication($userId, $params);
            if ($result['success']) {
                $returnArray = array('success' => true, 'url' => $this->generateUrl('workflow_list_applications'));
            } else {
                $returnArray = array('success' => false, 'errors' => $result['errors'], 'msg' => 'Validation Error');
            }
        } else {
            $returnArray = array('success' => false, 'msg' => 'Something went wrong please refresh the page');
        }

        return new JsonResponse($returnArray);

    }

    /**
     * @Route("/doUpdateApplication",name="workflow_do_update_application")
     * @Method({"POST"})
     */
    public function doUpdateAction(Request $request)
    {
        $params = $request->request->all();
        $submittedToken = $request->request->get('input_workflow_update_app_token');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        if ($this->isCsrfTokenValid('workflow_update_app_token', $submittedToken)) {
            $result = $this->workflowService->updateWorkflowApplication($userId, $params);
            if ($result['success']) {
                $returnArray = array('success' => true, 'url' => '/portal/workflow');
            } else {
                $returnArray = array('success' => false, 'errors' => $result['errors'], 'msg' => 'Validation Error');
            }
        } else {
            $returnArray = array('success' => false, 'msg' => 'Something went wrong please refresh the page');
        }

        return new JsonResponse($returnArray);

    }

    /**
     * @Route("/builder/save",name="workflow_builder_save")
     * @Method({"POST"})
     */
    public function saveWorkflowAction(Request $request)
    {
        $returnArray = array();
        $this->logger->debug('Request=>', $request->request->all());
        $data = $request->request->all();
        $applicationId = $data['applicationId'];
        $application = $this->getDoctrine()->getRepository('CequensBundle:Application')->findOneBy(['id' => $applicationId]);
        $returnArray['containers'] = $data['wirings']['containers'];
        $returnArray['connections'] = $data['wirings']['connections'];
        $returnArray['modules'] = array();
        $jsonArray = array();
        foreach ($returnArray['containers'] as $container) {
            $config = $container['config'];
            $jsonArray['containers'][] = [
                'module' => $config['module'],
                'label' => $config['module'],
                'position' => ['top' => (int)$config['position']['top'], 'left' => (int)$config['position']['left']]
            ];
            $settings = (array_key_exists('containerDetails', $container)) ? $container['containerDetails'] : null;
            if (!empty($settings)) {
                $settings['settings'] = (array_key_exists('settings', $settings)) ? $settings['settings'] : array();
                $settings['connections'] = array();
                $settings['config'] = $config;
                $jsonArray['connections'] = array();
                foreach ($returnArray['connections'] as $connection) {
                    $labelString = (!empty($connection['config']['label'])) ? $connection['config']['label'] : '';
                    $jsonArray['connections'][] = ['sourceId' => (int)$connection['config']['sourceId'], 'targetId' => (int)$connection['config']['targetId'], 'label' => $labelString];
                    if ($config['divId'] == $connection['config']['sourceDivId']) {
                        $newArray = array();
                        if (array_key_exists('connectionDetails', $connection)) {
                            $newArray = array_merge($connection['config'], $connection['connectionDetails']);
                        } else {
                            $newArray = $connection['config'];
                        }
                        $settings['connections'][] = $newArray;
                    }
                }

            }
            $returnArray['modules'][] = $settings;

        }
        $result = $this->adapterService->saveAdapter('TestWorkflowBuilder44' . rand(10000, 9999999), 'ssss', 1, 1, $returnArray['modules'], 'false', $jsonArray);
        if ($result['success']) {
            $adapter = $result['data'];
            if (!empty($application)) {
                $application->setAdapterId(($result['data'])->getAdapterId());
                $this->getDoctrine()->getManager()->persist($application);
                $this->getDoctrine()->getManager()->flush();
            }
            $result['data'] = ['adapterId' => $adapter->getAdapterId()];
        }
        $result['test'] = $jsonArray;
        $result['saved'] = $returnArray['modules'];
        return new JsonResponse($result);

    }

    /**
     * @Route("/modules/{moduleId}/settings", name="listModuleSettings")
     * @param int $moduleId
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listModuleSettingsAction($moduleId)
    {
        if (!empty($moduleId)) {
            $moduleRepository = $this->getDoctrine()->getRepository('AppBundle:Module');
            $module = $moduleRepository->find($moduleId);
            $moduleSettingRepository = $this->getDoctrine()->getRepository('AppBundle:ModuleSetting');
            $moduleSetting = $moduleSettingRepository->getModuleSettingsWithType($module);
            $settingsArray = [];
            foreach ($moduleSetting as $settingId => $settingAttributes) {
                $label = array_keys($settingAttributes)[0];
                $settingsArray[] = array(
                    'id' => $settingId,
                    'label' => $label,
                    'type' => $settingAttributes['type'],
                    'value' => $settingAttributes[$label],
                    'options' => $settingAttributes['options'],
                    'is_required' => $settingAttributes['is_required'],
                    'has_unique_keys' => 0//$settingAttributes['has_unique_keys']
                );
            }
            return new JsonResponse($settingsArray);
        } else {
            return new JsonResponse(['error' => 'ERROR: moduleId must not be empty.'], 400);
        }
    }

    /**
     * List all modules for a specific adapter
     *
     * @Route("/adapters/{adapterId}/modules", name="listAdapterModules")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAdapterModulesAction($adapterId)
    {
        // Get the modules instances of the adapter
        $modules = $this->adapterService->getAdapterModulesInstances($adapterId);
        return new JsonResponse($modules);
    }

    /**
     * @param int $appId
     *
     * @Route("/app/{appId}/steps", name="getWorkflowBuilderStepDetails", requirements={"id" = "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWorkflowAppStepsAction($appId)
    {
        $steps = [];
        $application = $this->getDoctrine()->getRepository('CequensBundle:Application')->find($appId);
        if ($application) {
            $applicationPropertiesArray = [];
            $applicationProperties = $this->getDoctrine()->getRepository('CequensBundle:ApplicationProperty')
                ->findBy(['applicationId' => $appId]);
            foreach ($applicationProperties as $applicationProperty) {
                $applicationPropertyBase = $this->getDoctrine()->getRepository('CequensBundle:Property')->find($applicationProperty->getPropertyId());
                $applicationPropertiesArray[$applicationPropertyBase->getPropertyName()] = $applicationProperty->getPropertyValue();
            }

            $modules = $this->getDoctrine()->getRepository('AppBundle:Module')->findBy(['moduleType' => $application->getApplicationType()]);
            foreach ($modules as $module) {
                if (!empty($module->getUiName())) {
                    $steps[$module->getUiName()] = json_decode($module->getUiSettings(), true);
                }
            }
        }
        //$json = json_encode($steps,JSON_UNESCAPED_SLASHES);
        return new JsonResponse($steps, 200);
    }

    /**
     * List all modules for a specific adapter
     *
     * @Route("/app/{appId}/modules", name="listAppAdaptersModules")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAppAdapterModulesAction($appId)
    {
        // Get the modules instances of the adapter
        $names = [
            'Say Text' => 'say',
            'Menu' => 'menu',
            'Collect Input Internally' => 'collect',
            'Hangup' => 'hang',
            'Play Audio' => 'play',
            'Play URL' => 'playUrl',
            'External Service (beta)' => 'httpRequest',
            'Dial Number' => 'dial',
            'Record' => 'record',
            'SMS' => 'sms',
            'Email' => 'email',
            'Branch' => 'branch',
            'End Session' => 'endSession',
            'Wait for a response' => 'waitForResponse',
            'Execute Workflow' => 'executeWorkflow'
        ];
        $result = ['containers' => [], 'connections' => []];
        $application = $this->getDoctrine()->getRepository('CequensBundle:Application')->find($appId);
        $resultConnectionItems = [];
        if (!empty($application->getAdapterId())) {
            $modules = $this->adapterService->getAdapterModulesInstances($application->getAdapterId());
            $i = 0;
            $tempArray = [];
            $resultConnectionItems = [];
            $left = 261;
            $top = 98;
            foreach ($modules as $module) {
                //print_r($module);exit;
                if (($i % 2) == 0) {
                    $top = 98;
                } else {
                    $top = 98 + 190;
                }
                $resultItem = [];
                $resultItem['id'] = $module['module']['module_id'];
                $resultItem['module'] = $names[$module['module']['module_name']];
                $resultItem['label'] = $names[$module['module']['module_name']];
                $resultItem['instance_id'] = $module['instance_id'];
                $resultItem['settings'] = [];
                $resultItem['position'] = ['left' => $left, 'top' => $top];
                $resultItemSettings = [];
                $tempArray[$module['instance_id']] = $i;
                foreach ($module['settings'] as $moduleSettingKey => $moduleSettingValue) {
                    $settingOption = (array_key_exists('options', $moduleSettingValue)) ? $moduleSettingValue['options'] : [];
                    $attributes = [];
                    foreach ($moduleSettingValue['value'] as $itemKey => $itemValue) {
                        $attributes[] = ['value' => $itemValue];
                    }
                    $resultItemSettings[] = ['id' => $moduleSettingKey, 'display_name' => $moduleSettingValue['display_name'], 'type' => $moduleSettingValue['type'], 'value' => $moduleSettingValue['value'], 'attributes' => $attributes, 'options' => $settingOption];
                }
                $resultItem['settings'] = $resultItemSettings;
                $result['containers'][] = $resultItem;

                if (array_key_exists('target', $module['connections'])) {

                    foreach ($module['connections']['target'] as $connectionTarget) {
                        $resultConnectionItems[] = ['sourceId' => $i, 'targetId' => $connectionTarget['conditionTarget'], 'label' => $connectionTarget['conditionValue']];
                    }
                }
                $left = $left + 190;
                $i++;
            }
            $resultConnectionItemss = [];
            foreach ($resultConnectionItems as $resultConnectionItem) {
                $resultConnectionItem['targetId'] = $tempArray[$resultConnectionItem['targetId']];
                $resultConnectionItemss[] = ['sourceId' => $resultConnectionItem['sourceId'], 'targetId' => $resultConnectionItem['targetId'], 'label' => $resultConnectionItem['label']];
            }
            $result['connections'] = $resultConnectionItemss;
        } else {
            $result['containers'] = [];
            $result['connections'] = [];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/app/{appId}/test", name="testWorkflowApp", requirements={"id" = "\d+"})
     * @param int $appId
     * @Method("POST")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAppAction(Request $request, $appId)
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        if (!empty($appId)) {
            $application = $this->getDoctrine()->getRepository('CequensBundle:Application')->findOneBy(['id' => $appId]);
            if ($application) {
                $mobileNumber = $request->request->get('destination');
                $result = $this->forward('CequensBundle:Internal\Restcomm:testCall', ['adapterId' => $application->getAdapterId()], ['destination' => $mobileNumber]);
            }
            return new JsonResponse([$result]);
        } else {
            return new JsonResponse(['error' => 'ERROR: appId must not be empty.'], 400);
        }
    }

    /**
     * @Route("/app/{appId}", name="getWorkflowBuilderDetails", requirements={"id" = "\d+"})
     * @param int $appId
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWorkflowAppDetailsAction($appId)
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        if (!empty($appId)) {
            $returnResult = $this->workflowService->getWorkflowDetails($userId, $appId);
            return new JsonResponse($returnResult);
        } else {
            return new JsonResponse(['error' => 'ERROR: appId must not be empty.'], 400);
        }
    }
}
