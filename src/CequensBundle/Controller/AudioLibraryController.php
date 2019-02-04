<?php

namespace CequensBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CequensBundle\Service\AudioLibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class AudioLibraryController extends Controller
{
	protected $audioLibraryServices;

	/**
	 * AudioLibraryController constructor.
	 */
	public function __construct(AudioLibraryService $audioLibraryService)
	{
		$this->audioLibraryServices = $audioLibraryService;
	}

	/**
	 * @Route("/",name="audiolib_list_all")
	 */
	public function indexAction()
	{
		$filter = array();
		$userId             = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$filters['user_id'] = $userId;
		$audioFiles         = $this->audioLibraryServices->getAllAudioFiles($filters);
		return $this->render('@Cequens/Admin/pages/AudioLibrary/list_files.html.twig', array('audios' => $audioFiles['data']));
	}

	/**
	 * @Route("/uploadFile",name="audiolib_upload_file",methods={"POST"}, requirements={"_format"="json"})
	 */
	public function uploadFileAction(Request $request)
	{
		$output = array('uploaded' => false);

		// get the file from the request object
		$file   = $request->files->get('file');
		$result = $this->audioLibraryServices->uploadNewAudioFile($file);

		return new JsonResponse($result);
	}
}
