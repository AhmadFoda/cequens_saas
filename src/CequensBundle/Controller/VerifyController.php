<?php

namespace CequensBundle\Controller;

use CequensBundle\Service\VerifyService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class VerifyController extends Controller
{
	protected $verifService;

	/**
	 * VerifyController constructor.
	 *
	 * @param $verifService
	 */
	public function __construct(VerifyService $verifService)
	{
		$this->verifService = $verifService;
	}

	/**
	 * @Route("/",name="verify_list_applications")
	 */
	public function indexAction(Request $request)
	{
		$filters            = array();
		$submittedToken     = $request->request->get('input_verify_create_app_token');
		$userId             = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$filters['user_id'] = $userId;
		$result             = $this->verifService->listAllApplications($filters);

		return $this->render(
			'@Cequens/Admin/pages/Verify/list_applications.html.twig',
			array('applications' => $result['data'])
		);
	}

	/**
	 * @Route("/new",name="verify_create_applications")
	 */
	public function createAction()
	{

		$properties_options = $this->verifService->getPropertiesAndOptions();

		return $this->render(
			'@Cequens/Admin/pages/Verify/create_applications.html.twig',
			array('properties' => $properties_options['data'])
		);
	}

	/**
	 * @Route("/usage",name="verify_usage_applications")
	 */
	public function usageApplicationAction(Request $request)
	{
		$userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$appId  = $request->get('appId', '');

		return $this->render('@Cequens/Admin/pages/Verify/usage_applications.html.twig', array());
	}

	/**
	 * @Route("/doCreateApplication",name="verify_do_create_application")
	 * @Method({"POST"})
	 */
	public function doCreateAction(Request $request)
	{
		$params         = $request->request->all();
		$submittedToken = $request->request->get('input_verify_create_app_token');
		$userId         = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ($this->isCsrfTokenValid('verify_create_app_token', $submittedToken)) {
			$result = $this->verifService->createNewVerifyApplication($userId, $params);
			if ($result['success']) {
				$returnArray = array('success' => true, 'url' => '/portal/verify');
			} else {
				$returnArray = array('success' => false, 'errors' => $result['errors'], 'msg' => 'Validation Error');
			}
		} else {
			$returnArray = array('success' => false, 'msg' => 'Something went wrong please refresh the page');
		}

		return new JsonResponse($returnArray);

	}

	/**
	 * @Route("/doUpdateApplication",name="verify_do_update_application")
	 * @Method({"POST"})
	 */
	public function doUpdateAction(Request $request)
	{
		$params         = $request->request->all();
		$submittedToken = $request->request->get('input_verify_update_app_token');
		$userId         = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ($this->isCsrfTokenValid('verify_update_app_token', $submittedToken)) {
			$result = $this->verifService->updateVerifyApplication($userId, $params);
			if ($result['success']) {
				$returnArray = array('success' => true, 'url' => '/portal/verify');
			} else {
				$returnArray = array('success' => false, 'errors' => $result['errors'], 'msg' => 'Validation Error');
			}
		} else {
			$returnArray = array('success' => false, 'msg' => 'Something went wrong please refresh the page');
		}

		return new JsonResponse($returnArray);

	}

	/**
	 * @Route("/{id}",name="verify_view_applications", requirements={"id"="\d+"})
	 */
	public function viewApplicationAction($id, Request $request)
	{
		$userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$result = $this->verifService->getApplication($userId, $id);
		if ($result['success']) {
			$application_properties_options = $this->verifService->getApplicationPropertiesAndOptions($id);
			$data                           = array(
				'application' => $result['data']['application'],
				'properties'  => $application_properties_options['data'],
			);
		} else {
			return $this->redirectToRoute('verify_list_applications');
		}

		return $this->render('@Cequens/Admin/pages/Verify/update_applications.html.twig', $data);
	}
}
