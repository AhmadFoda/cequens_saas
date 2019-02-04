<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 4/1/2018
 * Time: 12:27 PM
 */

namespace CequensBundle\Service;

use CequensBundle\Entity\Application;
use CequensBundle\Entity\ApplicationProperty;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VerifyService
{
	protected $entityManager;
	protected $validator;
	protected $container;

	/**
	 * VerifyService constructor.
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

	/**
	 * @param $userId
	 * @param $params
	 *
	 * @return array
	 */
	public function createNewVerifyApplication($userId, $params)
	{
		$returnArray    = array('success' => false, 'msg' => '', 'data' => '');
		$appName        = (array_key_exists(
			'input_application_name',
			$params
		)) ? $params['input_application_name'] : null;
		$appDescription = (array_key_exists(
			'input_application_description',
			$params
		)) ? $params['input_application_description'] : null;
		$appType        = 1;
		$appToken       = bin2hex(openssl_random_pseudo_bytes(16));

		$application    = new Application();
		$application->setApplicationName($appName);
		$application->setApplicationDescription($appDescription);
		$application->setApplicationType($appType);
		$application->setApplicationToken($appToken);
		$application->setCreatedAt(new \DateTime('now'));
		$application->setUpdatedAt(new \DateTime('now'));
		$application->setUserId($userId);
		$errors = $this->validator->validate($application);
		if (count($errors) > 0) {
			$returnArray['success'] = false;
			$returnArray['errors']  = $errors;
		} else {
			$this->entityManager->persist($application);
			$this->entityManager->flush();
			$propertyGroups = $this->entityManager->getRepository('CequensBundle:PropertyGroup')->findBy(
				array('propertyGroupType' => 'verify')
			)
			;
			foreach ($propertyGroups as $propertyGroup) {
				$properties = $this->entityManager->getRepository(
					'CequensBundle:Property'
				)->findBy(
					array('propertyGroupId' => $propertyGroup->getId())
				)
				;
				foreach ($properties as $property) {
					if(array_key_exists($property->getPropertyName(),$params))
					{
						$appProperty = new ApplicationProperty();
						$appProperty->setApplicationId($application->getId());
						$appProperty->setPropertyId($property->getId());
						$appProperty->setPropertyValue($params[$property->getPropertyName()]);
						$appProperty->setCreatedAt(new \DateTime('now'));
						$appProperty->setUpdatedAt(new \DateTime('now'));
						$this->entityManager->persist($appProperty);
					}
				}
				$this->entityManager->flush();
				$returnArray['success'] = true;
			}
		}

		return $returnArray;
	}

	/**
	 * @param $userId
	 * @param $params
	 *
	 * @return array
	 */
	public function updateVerifyApplication($userId, $params)
	{
		$returnArray    = array('success' => false, 'msg' => '', 'data' => '');
		$appId = (array_key_exists(
			'hidden_verify_app_id',
			$params
		)) ? $params['hidden_verify_app_id'] : null;
		$appName        = (array_key_exists(
			'input_application_name',
			$params
		)) ? $params['input_application_name'] : null;
		$appDescription = (array_key_exists(
			'input_application_description',
			$params
		)) ? $params['input_application_description'] : null;

		if(empty($appId))
		{
			$returnArray['msg'] = 'Application ID does not exist or you do not have permissions to accesss this resource';
			return $returnArray;
		}
		$application = $this->entityManager->getRepository('CequensBundle:Application')->findOneBy(array('userId'=>$userId,'id'=>$appId));
		if(empty($application))
		{
			$returnArray['msg'] = 'Application ID does not exist or you do not have permissions to accesss this resource';
			return $returnArray;
		}

		$application->setApplicationName($appName);
		$application->setApplicationDescription($appDescription);
		$application->setUpdatedAt(new \DateTime('now'));
		$errors = $this->validator->validate($application);
		if (count($errors) > 0) {
			$returnArray['success'] = false;
			$returnArray['errors']  = $errors;
		} else {
			$this->entityManager->flush();
			$propertyGroups = $this->entityManager->getRepository('CequensBundle:PropertyGroup')->findBy(
				array('propertyGroupType' => 'verify')
			)
			;
			foreach ($propertyGroups as $propertyGroup) {
				$properties = $this->entityManager->getRepository(
					'CequensBundle:Property'
				)->findBy(
					array('propertyGroupId' => $propertyGroup->getId())
				)
				;
				foreach ($properties as $property) {
					$appProperty = $this->entityManager->getRepository('CequensBundle:ApplicationProperty')->findOneBy(array('propertyId'=>$property->getId(),'applicationId'=>$application->getId()));
					if(!empty($appProperty) && array_key_exists($property->getPropertyName(),$params))
					{
						$appProperty->setPropertyValue($params[$property->getPropertyName()]);
						$appProperty->setUpdatedAt(new \DateTime('now'));
						$this->entityManager->flush();
					}
				}
				$this->entityManager->flush();
				$returnArray['success'] = true;
			}
		}

		return $returnArray;
	}

	/**
	 * @param array $filters
	 *
	 * @return array
	 */
	public function listAllApplications($filters = array())
	{
		$return_result = array('success' => false, 'data' => array());
		$queryFilters = array();
		if(array_key_exists('user_id',$filters))
		{
			$queryFilters['userId'] = $filters['user_id'];
		}
		if(array_key_exists('id',$filters))
		{
			$queryFilters['id'] = $filters['id'];
		}
		$queryFilters['applicationType'] = 1;
		$applications  = $this->entityManager->getRepository('CequensBundle:Application')->findBy(
			$queryFilters
		)
		;
		if (count($applications) > 0) {
			$data_array = array();
			foreach ($applications as $application) {
				$data_array[] = $application;
			}
			$return_result['success'] = true;
			$return_result['data']    = $data_array;
		} else {
			$return_result = array('success' => true, 'data' => array());
		}

		return $return_result;
	}

	/**
	 * @param $userId
	 * @param $id
	 */
	public function getApplication($userId, $id)
	{
		$return_result = array('success' => false, 'data' => array());
		$application   = $this->entityManager->getRepository('CequensBundle:Application')->findOneBy(
			array('userId' => $userId, 'id' => $id)
		)
		;
		if (!$application) {
			$return_result['msg'] = 'Application Not Found';
		} else {
			$applicationProperties      = $this->entityManager->getRepository('CequensBundle:ApplicationProperty')
				->findBy(
					array('applicationId' => $application->getId())
				)
			;
			$applicationPropertiesArray = array();
			foreach ($applicationProperties as $applicationProperty) {

			}
			$return_result['success']             = true;
			$return_result['data']['application'] = $application;
		}

		return $return_result;
	}

	public function getPropertiesAndOptions()
	{
		$return_data    = array('success' => true, 'data' => array());
		$propertyGroups = $this->entityManager->getRepository('CequensBundle:PropertyGroup')->findBy(
			array('propertyGroupType' => 'verify')
		)
		;
		foreach ($propertyGroups as $propertyGroup) {
			$properties                                                 = $this->entityManager->getRepository(
				'CequensBundle:Property'
			)->findBy(
				array('propertyGroupId' => $propertyGroup->getId())
			)
			;
			$return_data['data'][$propertyGroup->getId()]['group']      = $propertyGroup;
			$return_data['data'][$propertyGroup->getId()]['properties'] = array();
			foreach ($properties as $property) {
				$propertyOptions                                              = $this->entityManager->getRepository(
					'CequensBundle:PropertyOption'
				)->findBy(
					array('propertyId' => $property->getId())
				)
				;
				$return_data['data'][$propertyGroup->getId()]['properties'][] = array(
					'property' => $property,
					'options'  => $propertyOptions,
				);
			}
		}

		return $return_data;

	}

	public function getApplicationPropertiesAndOptions($applicationId)
	{
		$return_data    = array('success' => true, 'data' => array());
		$propertyGroups = $this->entityManager->getRepository('CequensBundle:PropertyGroup')->findBy(
			array('propertyGroupType' => 'verify')
		)
		;
		foreach ($propertyGroups as $propertyGroup) {
			$properties                                                 = $this->entityManager->getRepository(
				'CequensBundle:Property'
			)->findBy(
				array('propertyGroupId' => $propertyGroup->getId())
			)
			;
			$return_data['data'][$propertyGroup->getId()]['group']      = $propertyGroup;
			$return_data['data'][$propertyGroup->getId()]['properties'] = array();
			foreach ($properties as $property) {
				$propertyOptions                                              = $this->entityManager->getRepository(
					'CequensBundle:PropertyOption'
				)->findBy(
					array('propertyId' => $property->getId())
				)
				;
				$propertiesArray = array(
					'property' => $property,
					'options'  => $propertyOptions,
					'property_value' => ''
				);
				$applicationProperty = $this->entityManager->getRepository('CequensBundle:ApplicationProperty')->findOneBy(array('applicationId'=>$applicationId,'propertyId'=>$property->getId()));
				if($applicationProperty)
				{
					$propertiesArray['property_value'] = $applicationProperty->getPropertyValue();
				}
				$return_data['data'][$propertyGroup->getId()]['properties'][] = $propertiesArray;
			}
		}

		return $return_data;
	}

}