<?php

namespace CequensBundle\Controller\Internal;

use CequensBundle\Service\RestcommService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WhatsAppController extends Controller
{

	protected $whatsappService;

	/**
	 * RestcommController constructor.
	 */
	public function __construct(RestcommService $restcommService)
	{
		$this->restcommService = $restcommService;
	}

	/**
	 * @Route("/{adapterId}/getNext",name="get")
	 * @Method({"GET"})
	 */
	public function indexAction(Request $request, $adapterId)
	{
		$moduleInstanceId = $request->get('stepId','');
		$conditionValue = $request->get('Digits',null);
		$rcml = new \SimpleXMLElement("<Response></Response>");
		$currentModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceId);
		$moduleInstanceConnections = $currentModuleInstance->getModuleInstanceConnections();
		if(count($moduleInstanceConnections) > 1)
		{
			foreach ($moduleInstanceConnections as $moduleInstanceConnection)
			{
				if($moduleInstanceConnection->getConditionValue() == $conditionValue)
				{
					$nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceConnection->getTargetModuleInstanceId());
					$module = $nextModuleInstance->getModule();
					$moduleClassName = $module->getProcessorClassName();
					$moduleClass = new $moduleClassName($module, $nextModuleInstance, $rcml, $this->getDoctrine()->getManager(), $this->container);
					$rcml = $moduleClass->getRcml();
					break;
				}

			}
		}
		else if(count($moduleInstanceConnections)==1)
		{
			$moduleInstanceConnection = $moduleInstanceConnections[0];
			$nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceConnection->getTargetModuleInstanceId());
			$module = $nextModuleInstance->getModule();
			$moduleClassName = $module->getProcessorClassName();
			$moduleClass = new $moduleClassName($module, $nextModuleInstance, $rcml, $this->getDoctrine()->getManager(), $this->container);
			$rcml = $moduleClass->getRcml();
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
		$moduleInstanceId = $request->request->get('stepId','');
		$conditionValue = $request->request->get('Digits',null);
		$conditionValue =
		$rcml = new \SimpleXMLElement("<Response></Response>");
		$currentModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceId);
		$moduleInstanceConnections = $currentModuleInstance->getModuleInstanceConnections();
		if(count($moduleInstanceConnections) > 1)
		{
			foreach ($moduleInstanceConnections as $moduleInstanceConnection)
			{
				if($moduleInstanceConnection->getConditionValue() == $conditionValue)
				{
					$nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceConnection->getTargetModuleInstanceId());
					$module = $nextModuleInstance->getModule();
					$moduleClassName = $module->getProcessorClassName();
					$moduleClass = new $moduleClassName($module, $nextModuleInstance, $rcml, $this->getDoctrine()->getManager(), $this->container);
					$rcml = $moduleClass->getRcml();
					break;
				}

			}
		}
		else if(count($moduleInstanceConnections)==1)
		{
			$moduleInstanceConnection = $moduleInstanceConnections[0];
			$nextModuleInstance = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->find($moduleInstanceConnection->getTargetModuleInstanceId());
			$module = $nextModuleInstance->getModule();
			$moduleClassName = $module->getProcessorClassName();
			$moduleClass = new $moduleClassName($module, $nextModuleInstance, $rcml, $this->getDoctrine()->getManager(), $this->container);
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
	public function getWorkflowRcml($adapterId)
	{
		$rcml = new \SimpleXMLElement("<Response></Response>");
		$campaign = $this->getDoctrine()->getRepository('AppBundle:Adapter')->findOneBy(array('adapterId' => $adapterId));
		if ($campaign) {
			if ($campaign->getIsActive()) {
				$campaignModuleInstances = $this->getDoctrine()->getRepository('AppBundle:ModuleInstance')->findBy(array('adapter' => $campaign));
				foreach ($campaignModuleInstances as $campaign_module_instance) {
					$module = $campaign_module_instance->getModule();
					$moduleClassName = $module->getProcessorClassName();
					$moduleClass = new $moduleClassName($module, $campaign_module_instance, $rcml, $this->getDoctrine()->getManager(), $this->container);
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
	public function testCallAction(Request $request,$adapterId)
	{
		$result = $this->restcommService->triggerCall(
		    $request->get('from','201020737333'),
            $request->get('destination',''),
            $adapterId
        );
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
		if($audioFile)
		{
			//$audioFileObj= $em->getRepository('CequensBundle:File')->find($audioFile->getFileId());
			//$parameter1 = str_replace($this->container->getParameter('application_url').'/playfile?voice=male&file=','',$audioFileObj->getFileName());
			//$root = str_replace('/app', '/', $this->container->getParameter('kernel.root_dir'));
			//$destination_name_ulaw = $root . '/web/' . 'digits/' . str_replace('__', '', $parameter1 . '.wav');
			$action = 'play';
			//$file = $destination_name_ulaw;
			$file = $audioFile->getFilePath();
			$caller = '/play';

			if (empty ($action) || empty ($file))
				die ("Something went wrong");

			// Open the file

			$fileName = $file;
			$fd = @fopen($file, "rb");

			if ($fd === FALSE) {
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

		if (empty ($action) || empty ($file))
			die ("Something went wrong");

		// Open the file

		$fileName = $file;
		$fd = @fopen($file, "rb");

		if ($fd === FALSE) {
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
