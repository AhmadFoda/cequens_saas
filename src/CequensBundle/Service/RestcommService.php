<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 23/06/18
 * Time: 08:02 م
 */

namespace CequensBundle\Service;

use CequensBundle\Entity\Application;
use CequensBundle\Entity\ApplicationProperty;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestcommService
{
	protected $entityManager;
	protected $validator;
	protected $container;

	/**
	 * WorkflowService constructor.
	 *
	 * @param $entityManager
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		ValidatorInterface $validator,
		ContainerInterface $container
	)
	{
		$this->entityManager = $entityManager;
		$this->validator     = $validator;
		$this->container     = $container;
	}

	public function triggerCall($from, $to, $workflowId)
	{
		$params = array(
			'From' => $from,
			'To' => $to,
			'Url' => $this->container->getParameter('application_url') . '/restcomm/' . $workflowId . '/rcml',
			'csurl'=>'https://voice.cequens.net:8443/restcomm/2012-04-24/Accounts/AC1320c1e0a13743a30e0479e95fd1d42d/Calls.json',
			//'csurl'=>'https://voice.cequens.net:8443/restcomm/2012-04-24/Accounts/AC1320c1e0a13743a30e0479e95fd1d42d/Calls.json'
		);
        $this->container->get('logger')->debug('RESTCOMM PARAM => ',[$params]);
		$response = $this->postRestcomm($params['csurl'], $params);
		$this->container->get('logger')->debug('RESTCOMM RESPONSE => ',[$response]);
		return $response;

	}

	private function postRestcomm($url, $param, $get = false)
	{
		$ch = curl_init();
		unset($param['csurl']);
		$param['tsrand'] = rand(1, 9999999);
		$curl_options    = array(
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0,
		);
		$fields_string   = '';
		foreach ($param as $key => $value) {
			$fields_string .= $key . '=' . urlencode($value) . '&';
		}
		rtrim($fields_string, '&');
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		if (!$get) {
			curl_setopt($ch, CURLOPT_POST, count($param));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		}
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$auth              = 'k.mohamed@cequens.com:mySafeP@ssw0rd!';
		$bas_auth          = base64_encode($auth);
		$request_headers[] = "Authorization: Basic " . $bas_auth;
		curl_setopt($ch, CURLOPT_USERPWD, $auth);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt_array($ch, $curl_options);
		//execute post
		$result = curl_exec($ch);
		//close connection
		curl_close($ch);

		//list($response_headers, $response_content) = preg_split('/(\r\n){2}/', $result, 2);
		return $data = json_decode($result, TRUE);

	}

}