<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 5/16/2018
 * Time: 3:33 PM
 */

namespace CequensBundle\Service;

use AppBundle\Service\AdapterService;
use CequensBundle\CequensBundle;
use CequensBundle\Entity\Application;
use CequensBundle\Entity\ApplicationProperty;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkflowService
{
	protected $entityManager;
	protected $validator;
	protected $container;
	protected $adapterService;

	/**
	 * WorkflowService constructor.
	 *
	 * @param $entityManager
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		ValidatorInterface $validator,
		ContainerInterface $container,
        AdapterService $adapterService
	)
	{
		$this->entityManager = $entityManager;
		$this->validator     = $validator;
		$this->container     = $container;
		$this->adapterService = $adapterService;
	}

	public function getPropertiesAndOptions()
	{
		$return_data    = array('success' => true, 'data' => array());
		$propertyGroups = $this->entityManager->getRepository('CequensBundle:PropertyGroup')->findBy(
			array('propertyGroupType' => 'workflow')
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
			array('propertyGroupType' => 'workflow')
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
				$propertyOptions     = $this->entityManager->getRepository(
					'CequensBundle:PropertyOption'
				)->findBy(
					array('propertyId' => $property->getId())
				)
				;
				$propertiesArray     = array(
					'property'       => $property,
					'options'        => $propertyOptions,
					'property_value' => '',
				);
				$applicationProperty = $this->entityManager->getRepository('CequensBundle:ApplicationProperty')
					->findOneBy(array('applicationId' => $applicationId, 'propertyId' => $property->getId()))
				;
				if ($applicationProperty) {
					$propertiesArray['property_value'] = $applicationProperty->getPropertyValue();
				}
				$return_data['data'][$propertyGroup->getId()]['properties'][] = $propertiesArray;
			}
		}

		return $return_data;
	}

	public function createNewWorkflowApplication($userId, $params)
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
		$appType        = 2;
		switch ($params['trigger_type'])
        {
            case 'incomingSms':
                $appType = 3;
                break;
            case 'webhook':
                $appType = 2;
                break;
            case 'incomingCall':
                $appType = 2;
                break;
        }
		$appToken       = bin2hex(openssl_random_pseudo_bytes(16));

		$application = new Application();
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
				array('propertyGroupType' => 'workflow')
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
					if (array_key_exists($property->getPropertyName(), $params)) {
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

			}
			$returnArray['success'] = true;
		}

		return $returnArray;
	}

	/**
	 * @param $userId
	 * @param $params
	 *
	 * @return array
	 */
	public function updateWorkflowApplication($userId, $params)
	{
		$returnArray    = array('success' => false, 'msg' => '', 'data' => '');
		$appId          = (array_key_exists(
			'hidden_workflow_app_id',
			$params
		)) ? $params['hidden_workflow_app_id'] : null;
		$appName        = (array_key_exists(
			'input_application_name',
			$params
		)) ? $params['input_application_name'] : null;
		$appDescription = (array_key_exists(
			'input_application_description',
			$params
		)) ? $params['input_application_description'] : null;

		if (empty($appId)) {
			$returnArray['msg'] = 'Application ID does not exist or you do not have permissions to accesss this resource';

			return $returnArray;
		}
		$application = $this->entityManager->getRepository('CequensBundle:Application')->findOneBy(
			array('userId' => $userId, 'id' => $appId)
		)
		;
		if (empty($application)) {
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
				array('propertyGroupType' => 'workflow')
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
					$appProperty = $this->entityManager->getRepository('CequensBundle:ApplicationProperty')->findOneBy(
						array('propertyId' => $property->getId(), 'applicationId' => $application->getId())
					)
					;
					if (!empty($appProperty) && array_key_exists($property->getPropertyName(), $params)) {
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
		//$queryFilters['applicationType'] = 2;
		$applications  = $this->entityManager->getRepository('CequensBundle:Application')->findBy(
			$queryFilters
		)
		;
		if (count($applications) > 0) {
			$data_array = array();
			foreach ($applications as $application) {
			    $application->applicationTypeName = CequensBundle::applicationTypes[$application->getApplicationType()];
				$data_array[] = $application;
			}
			$return_result['success'] = true;
			$return_result['data']    = $data_array;
		} else {
			$return_result = array('success' => true, 'data' => array());
		}

		return $return_result;
	}


	public function getWorkflowDetails($userId, $applicationId)
	{
		$returnResult = ['success'=>true, 'data'=>[]];
		$application = $this->entityManager->getRepository('CequensBundle:Application')->findOneBy(['userId'=>$userId, 'id'=>$applicationId]);
		$adapter = $this->entityManager->getRepository('AppBundle:Adapter')->findOneBy(['adapterId'=>$application->getAdapterId()]);
		if($adapter)
		{
			$modules = $this->adapterService->getAdapterModulesInstances($adapter->getAdapterId());
			$result = [];
			$returnResult = json_decode($adapter->getAdapterJson(),true, 512, JSON_NUMERIC_CHECK);
			/*foreach ($modules as $module)
			{
				$dataArray = [];
				$dataArray['module'] = $this->_getModuleShortName($module['module']['module_name']);
				$dataArray['label'] = strtoupper($module['module']['module_name']);
				$returnResult['data'][]=$dataArray;
			}*/
		}
		return $returnResult;
	}

	private function _getModuleShortName($moduleName)
	{
		$moduleShortNames = [
			'Play URL' => 'playUrl',
			'Say Text' => 'say',
			'Collect Input Internally' => 'collect',
			'Dial Number' => 'dial',
			'Play Audio' => 'play',
			'Record' => 'record',
			'External Service (beta)' => 'httpRequest',
			'Got To Workflow' => '',
			'Collect Input Conditional' => 'collect',
			'Hangup' => 'hang'
		];

		return $moduleShortNames[$moduleName];
	}
}