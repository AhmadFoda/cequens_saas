<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 4/16/2018
 * Time: 12:02 AM
 */

namespace CequensBundle\Controller\Api;

use CequensBundle\Service\VerifyService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VerifyController extends ApiBaseController
{

	protected $verifyService;

	/**
	 * VerifyController constructor.
	 *
	 * @param $verifyService
	 */
	public function __construct(VerifyService $verifyService)
	{
		$this->verifyService = $verifyService;
	}

	/**
	 *
	 * @Rest\Post("/{applicationToken}/sms/")
	 * @Rest\View()
	 *
	 */
	public function postVerifySMSAction(Request $request,$applicationToken)
	{
		return array('ok');
	}

	/**
	 *
	 * @Rest\Route("/{applicationToken}/voice/",methods={"POST"})
	 * @Rest\View()
	 *
	 */
	public function postVerifyVoiceAction(Request $request,$applicationToken)
	{
		return array('ok');
	}

	/**
	 *
	 * @Rest\Get("/application/")
     * @Rest\View()
	 *
	 */
	public function cgetVerifyApplicationAction(Request $request)
	{
		$userData = $this->getUserData();
		$filters = array('user_id'=>$userData['user_id']);
		$applications = $this->verifyService->listAllApplications($filters);
		return $applications;
	}

	/**
	 *
	 * @Rest\Get("/application/{id}/",requirements={"id" = "\d+"}   )
	 * @Rest\View()
	 *
	 */
	public function getVerifyApplicationsAction(Request $request,$id)
	{
		$userData = $this->getUserData();
		$filters = array('user_id'=>$userData['user_id'],'id'=>$id);
		$applications = $this->verifyService->listAllApplications($filters);
		return $applications;
	}


}