<?php

namespace CequensBundle\Controller;

use CequensBundle\Service\FacebookService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CequensBundle\Service\BotService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class BotController extends Controller
{


    /**
     * @var BotService
     */
    protected $botService;

    protected $logger;

    protected $fb;


    /**
     * BotController constructor.
     * @param BotService $botService
     */
    public function __construct(BotService $botService, LoggerInterface $logger, FacebookService $fb)
    {
        $this->botService = $botService;
        $this->logger = $logger;
        $this->fb = $fb;
    }

    /**
     * @Route("/",name="bot_list_all")
     */
    public function indexAction()
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $filters['user_id'] = $userId;
        $audioFiles = $this->botService->getAllBots($userId);
        return $this->render('@Cequens/Admin/pages/Bot/list_bot.html.twig', array('bots' => $audioFiles['data']));
    }

    /**
     * @Route("/app/new",name="bot_create_app")
     */
    public function newBotAppAction(Request $request)
    {
        $filters = array();
        //$submittedToken     = $request->request->get('input_verify_create_app_token');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $filters['user_id'] = $userId;
        $result = array('data' => array());
        $properties_options = [];

        return $this->render(
            '@Cequens/Admin/pages/Bot/create_bot_app.html.twig',
            array('applications' => [], 'properties' => [])
        );
    }

    /**
     * @Route("/doCreateBotApplication",name="bot_do_create_application")
     * @Method({"POST"})
     */
    public function doCreateAction(Request $request)
    {
        $params = $request->request->all();
        $this->logger->debug('Captured Params =>', array($params));
        $submittedToken = $request->request->get('input_workflow_create_app_token');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $returnArray = ['success' => false, 'url' => ''];
        $result = $this->botService->createNewBot($userId, $params['bot_name'], $params['bot_description'], $params['bot_type']);
        $this->logger->debug('BotCreation Result => ', $result);
        if ($result['success']) {
            $bot_id = $result['data']['bot_id'];
            $resultConfig = $this->botService->createNewBotConfig($bot_id, $params['bot_config']);
            $this->logger->debug('BotConfigCreation Result => ', $resultConfig);
            if ($resultConfig['success']) {

                $rr = $this->fb->subscribePageToApp($params['bot_config']['FACEBOOK_PAGE_ID'],$params['bot_config']['FACEBOOK_PAGE_TOKEN']);
                $this->logger->debug('Subscribe App To Page Result ',array($rr));
                if($rr['success'])
                {
                    $returnArray['success'] = true;
                    $returnArray['url'] = $this->generateUrl('bot_list_all');
                }
                else
                {
                    return new JsonResponse(array('success' => false, 'errors' => '', 'msg' => 'Failed to Subscribe App To Page'));
                }


            } else {

                return new JsonResponse(array('success' => false, 'errors' => '', 'msg' => 'Validation Error'));
            }
        } else {

            return new JsonResponse(array('success' => false, 'errors' => '', 'msg' => 'Validation Error'));
        }

        return new JsonResponse($returnArray);

    }
}
