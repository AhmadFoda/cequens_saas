<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 4/16/2018
 * Time: 12:49 AM
 */

namespace CequensBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class ApiBaseController
 *
 * @package CequensBundle\Controller\Api
 */
class ApiBaseController extends FOSRestController
{

	/**
	 * ApiBaseController constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * @return array
	 */
	protected function getUserData()
	{
		$tokenManager = $this->container->get('fos_oauth_server.access_token_manager.default');
		$accessToken = $tokenManager->findTokenByToken(
			$this->container->get('security.token_storage')->getToken()->getToken()
		);
		$userObject = $accessToken->getClient()->getUser();
		$userData = array(
			'user_id' => $userObject->getId(),
			'username' => $userObject->getUsername(),
			'email' => $userObject->getEmail()
		);
		return $userData;
	}

	/**
	 * @return array
	 */
	protected function getUserId()
	{
		$tokenManager = $this->container->get('fos_oauth_server.access_token_manager.default');
		$accessToken = $tokenManager->findTokenByToken(
			$this->container->get('security.token_storage')->getToken()->getToken()
		);
		$userObject = $accessToken->getClient()->getUser();
		return $userObject->getId();
	}
}