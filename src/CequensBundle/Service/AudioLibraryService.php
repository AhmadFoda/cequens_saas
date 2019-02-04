<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 4/16/2018
 * Time: 9:44 AM
 */

namespace CequensBundle\Service;

use CequensBundle\CequensBundle;
use CequensBundle\Entity\AudioFile;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AudioLibraryService
{
	protected $entityManager;
	protected $validator;
	protected $container;
	protected $fileService;

	/**
	 * AudioLibraryService constructor.
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		ValidatorInterface $validator,
		ContainerInterface $container,
		FileService $fileService
	)
	{
		$this->entityManager = $entityManager;
		$this->validator     = $validator;
		$this->container     = $container;
		$this->fileService   = $fileService;
	}

	public function uploadNewAudioFile(UploadedFile $file)
	{
		// generate a new filename (safer, better approach)
		// To use original filename, $fileName = $this->file->getClientOriginalName();
		$OriginalfileName = $file->getClientOriginalName();
		$fileName         = md5(uniqid()) . '.' . $file->guessExtension();
		$fileSize = $file->getSize();
		$fileType = $file->getMimeType();

		// set your uploads directory
		$uploadDir = $this->container->get('kernel')->getRootDir() . '/../web/uploads/audio/';
		if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
			mkdir($uploadDir, 0775, true);
		}
		if ($file->move($uploadDir, $fileName)) {
			$duration  = $this->wavDur($uploadDir . $fileName);
			$file = new \CequensBundle\Entity\File();
			$file->setFileName($OriginalfileName);
			$file->setFileType(1);
			$file->setFilePath($uploadDir.$fileName);
			$file->setFileSafeName($fileName);
			$file->setFileSize($fileSize);
			$file->setFileStatus(1);
			$file->setFileVersion(1);
			$result = $this->fileService->addFile($file);
			if($result['success'])
			{
				$file = $result['data'];
				$audioFile = new AudioFile();
				$audioFile->setAudioFileDuration($duration);
				$audioFile->setAudioFileName($OriginalfileName);
				$audioFile->setFileType($fileType);
				$audioFile->setAudioFileSize($fileSize);
				$audioFile->setLanguageId(0);
				$audioFile->setFileId($file->getId());
				$audioFile->setUserId(1);
				$audioFile->setFileIsPackage(0);
				$this->entityManager->persist($audioFile);
				$this->entityManager->flush();
			}
			$output['duration'] = $duration;
			$output['uploaded'] = true;
			$output['fileName'] = $fileName;
		}

		return $output;

	}

	public function getAllAudioFiles($filters)
	{
		$criteria = array();
		$return_result = array('success' => false, 'data' => array());
		if(array_key_exists('user_id',$filters))
		{
			$criteria['userId'] = $filters['user_id'];
		}
		$audioFiles = $this->entityManager->getRepository('CequensBundle:AudioFile')->findBy($criteria);
		if (count($audioFiles) > 0) {
			$data_array = array();
			foreach ($audioFiles as $audioFile) {
				$data_array[] = $audioFile;
			}
			$return_result['success'] = true;
			$return_result['data']    = $data_array;
		} else {
			$return_result = array('success' => true, 'data' => array());
		}

		return $return_result;
	}

	private function wavDur($file)
	{
		$fp = fopen($file, 'r');
		if (fread($fp, 4) == "RIFF") {
			fseek($fp, 20);
			$rawheader = fread($fp, 16);
			$header    = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits', $rawheader);
			$pos       = ftell($fp);
			while (fread($fp, 4) != "data" && !feof($fp)) {
				$pos++;
				fseek($fp, $pos);
			}
			$rawheader = fread($fp, 4);
			$data      = unpack('Vdatasize', $rawheader);
			$sec       = $data['datasize'] / $header['bytespersec'];
			$minutes   = intval(($sec / 60) % 60);
			$seconds   = intval($sec % 60);

			return str_pad($minutes, 2, "0", STR_PAD_LEFT) . "." . str_pad($seconds, 2, "0", STR_PAD_LEFT);
		}
	}
}