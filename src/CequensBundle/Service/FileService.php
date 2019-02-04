<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 4/23/2018
 * Time: 11:07 AM
 */

namespace CequensBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileService
{
	protected $entityManager;
	protected $validator;
	protected $container;

	/**
	 * FileService constructor.
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

	/**
	 * @param \CequensBundle\Entity\File $file
	 *
	 * @return array
	 */
	public function addFile(\CequensBundle\Entity\File $file)
	{
		$this->entityManager->persist($file);
		$this->entityManager->flush($file);
		return ['success'=>true,'data'=>$file];
	}


}