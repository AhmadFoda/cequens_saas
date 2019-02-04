<?php

namespace AppBundle\Service;

use AppBundle\Entity\ModuleInstanceConnection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Enumeration\EnumModuleSettingType;

// TODO : remove all entityManager from parameters
class AdapterService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * AdapterService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * A function to get the information of a specific adapter
     * @param integer $adapterId
     *
     * @return array
     */
    public function getAdapterInformation($adapterId)
    {
        // Get adapter repository
        $adapterRepository = $this->entityManager->getRepository('AppBundle:Adapter');

        // Get information about the adapter
        $adapter = $adapterRepository->find($adapterId);

        // Convert to array
        $result = $this->convertObjectToArray($adapter);

        // Get adapter export setting
        $adapterSettingRepository = $this->entityManager->getRepository('AppBundle:AdapterSetting');
        $setting = $adapterSettingRepository->findOneBy(['adapter' => $adapter, 'name' => 'export']);

        // Attach export setting to result
        $result['export'] = $setting->getValue();

        return $result;
    }

    /**
     * This function get a list of all module instances of a certain adapter along with each instance's settings
     *
     * @param integer $adapterId
     *
     * @return array
     */
    public function getAdapterModulesInstances($adapterId)
    {
        // Get module repository
        $moduleInstanceRepository = $this->entityManager->getRepository('AppBundle:ModuleInstance');

        // Get the adapter
        $adapter = $this->entityManager->getRepository('AppBundle:Adapter')->find($adapterId);

        // Get the modules instances of the adapter
        $modulesInstances = $moduleInstanceRepository->getAdapterModulesInstances($adapter);
        $modules = [];
        foreach ($modulesInstances as $moduleInstance) {
            $moduleSettingInstanceRepository = $this->entityManager->getRepository('AppBundle:ModuleInstanceSetting');
            $settings = $moduleSettingInstanceRepository->getModuleInstanceSettingsWithInformation($moduleInstance);
            foreach ($settings as $settingId => $attribute) {
                // Add options array to SELECT and MULTISELECT settings
                $typeId = $attribute['type']['id'];
                if ($typeId == EnumModuleSettingType::SELECT
                    || $typeId == EnumModuleSettingType::MULTISELECT) {
                    $optionsRepository = $this->entityManager->getRepository('AppBundle:ModuleSettingOption');
                    $options = $optionsRepository->findBy(array('moduleSetting' => $settingId));
                    $optionsArray = [];
                    foreach ($options as $option) {
                        // Define tmp array to use for populating $optionsArray
                        $tmpOption = [];
                        $tmpOption['value'] = $option->getModuleSettingOptionId();
                        $tmpOption['name'] = $option->getValue();
                        $optionsArray[] = $tmpOption;
                    }
                    $settings[$settingId]['options'] = $optionsArray;
                }
                elseif ($typeId == EnumModuleSettingType::SELECT_MODULE)
                {
                    $adaptersRepository = $this->entityManager->getRepository('CequensBundle:Application');
                    $adapters = $adaptersRepository->findAll();
                    $adaptersArray = [];
                    foreach ($adapters as $adapter) {
                        // Define tmp array to use for populating $optionsArray
                        $tmpOption = [];
                        $tmpOption['value'] = $adapter->getAdapterId();
                        $tmpOption['name'] = $adapter->getApplicationName();
                        $adaptersArray[] = $tmpOption;
                    }
                    $settings[$settingId]['options'] = $adaptersArray;
                }
            }

	        $moduleConnectionInstanceRepository = $this->entityManager->getRepository('AppBundle:ModuleInstanceConnection');
	        $connections = $moduleConnectionInstanceRepository->getModuleInstanceConnectionsWithInformation($moduleInstance);
            // Add to the modules array
            $modules[] = array(
                'instance_id' => $moduleInstance->getModuleInstanceId(),
                'name' => $moduleInstance->getName(),
                'module' => array(
                    'module_id' => $moduleInstance->getModule()->getModuleId(),
                    'module_name' => $moduleInstance->getModule()->getName()
                ),
                'execute_order' => $moduleInstance->getExecuteOrder(),
                'settings' => $settings,
                'connections' => $connections
            );
        }

        return $modules;
    }

    /**
     * This function updates a module instance
     *
     * @param integer $moduleInstanceId
     * @param array $updatedAttributes
     * @param array $settings
     *
     * @return array
     */
    public function updateModuleInstance($moduleInstanceId, $updatedAttributes, $settings)
    {
        // Initialize result array
        $resultArray = array(
            'success' => false,
            'msg' => '',
            'data' => null
        );

        // Get module instance
        $moduleInstanceRepository = $this->entityManager->getRepository('AppBundle:ModuleInstance');
        $moduleInstance = $moduleInstanceRepository->updateModuleInstance($moduleInstanceId, $updatedAttributes);

        // Make sure that the module instance was updated successfully
        if (!$moduleInstance) {
            $resultArray['msg'] = 'An error occurred while updating the module.';
            return $resultArray;
        }

        // Get module instance setting repository
        $moduleInstanceSettingRepository = $this->entityManager->getRepository('AppBundle:ModuleInstanceSetting');

        // Update each setting
        foreach ($settings as $setting) {

            // Make sure the setting has 'id' and 'attributes' fields
            if (!array_key_exists('id', $setting) || !array_key_exists('attributes', $setting)) {
                $resultArray['msg'] = 'Missing \'id\' field or \'attributes\' field of setting.';
                return $resultArray;
            }

            // Extract value(s)
            $result = $this->getModuleInstanceSettingValues($setting);

            // Make sure the data was extracted correctly
            if (!$result['success']) {
                $resultArray['msg'] = $result['msg'];
                return $resultArray;
            }
            $data = $result['data'];

            // Get module setting
            $moduleSettingRepository = $this->entityManager->getRepository('AppBundle:ModuleSetting');
            $moduleSetting = $moduleSettingRepository->find($setting['id']);

            // update setting with the extracted value(s)
            $updated = $moduleInstanceSettingRepository->updateModuleInstanceSetting($moduleInstance, $moduleSetting, $data);

            // return false if failed to update the setting
            if (!$updated) {
                $resultArray['msg'] = 'An error occurred while updating the module setting.';
                return $resultArray;
            }
        }

        // Set data of resultArray
        $resultArray['success'] = true;
        $resultArray['data'] = $moduleInstance;

        // return result
        return $resultArray;
    }

    /**
     * This function extract the value(s) of a setting
     *
     * @param array $setting
     *
     * @return array|string
     */
    public function getModuleInstanceSettingValues($setting)
    {
        // Initialize result array
        $resultArray = array(
            'success' => false,
            'msg' => '',
            'data' => null
        );

        // Get module setting
        $moduleSettingRepository = $this->entityManager->getRepository('AppBundle:ModuleSetting');
        $moduleSetting = $moduleSettingRepository->find($setting['id']);

        // Check for existence of module setting
        if (!$moduleSetting) {
            $resultArray['msg'] = 'No module setting was found with the provided id.';
            return $resultArray;
        }

        // Get module setting type
        $moduleSettingTypeId = $moduleSetting->getModuleSettingType()->getModuleSettingTypeId();

        // Get attributes
        $attributes = $setting['attributes'];

        // Validate attributes
        if ($moduleSettingTypeId == EnumModuleSettingType::VALUE_ARRAY
            || $moduleSettingTypeId == EnumModuleSettingType::NAME_VALUE_ARRAY
            || $moduleSettingTypeId == EnumModuleSettingType::NAME_VALUE_ARRAY_WORKFLOW) {
            if (count($attributes) < 1) {
                $resultArray['msg'] = 'Missing attributes for setting "' . $moduleSetting->getName() . '".';
                return $resultArray;
            }
        } else {
            if (count($attributes) != 1) {
                $resultArray['msg'] = 'Attributes of a Multiselect or Single type setting should be an array of size 1.';
                return $resultArray;
            }
        }

        if ($moduleSettingTypeId == EnumModuleSettingType::VALUE_ARRAY
            || $moduleSettingTypeId == EnumModuleSettingType::NAME_VALUE_ARRAY
            || $moduleSettingTypeId == EnumModuleSettingType::MULTISELECT
            || $moduleSettingTypeId == EnumModuleSettingType::NAME_VALUE_ARRAY_WORKFLOW) {

            // Initialize values array
            $values = [];
            foreach ($attributes as $attribute) {

                // Check for 'value' index existence
                if (!array_key_exists('value', $attribute)) {
                    $resultArray['msg'] = 'Missing value of attribute of setting "' . $moduleSetting->getName() . '".';
                    return $resultArray;
                }
                if ($moduleSettingTypeId == EnumModuleSettingType::VALUE_ARRAY) {
                    $values[] = $attribute['value'];
                } else if ($moduleSettingTypeId == EnumModuleSettingType::NAME_VALUE_ARRAY) {

                    // Check for 'key' index existence
                    if (!array_key_exists('key', $attribute)) {
                        $resultArray['msg'] = 'Missing key of attribute of setting "' . $moduleSetting->getName() . '".';
                        return $resultArray;
                    }
                    $values[] = $attribute['key'] . ':' . $attribute['value'];
                } else if ($moduleSettingTypeId == EnumModuleSettingType::NAME_VALUE_ARRAY_WORKFLOW) {

                    // Check for 'key' index existence
                    if (!array_key_exists('key', $attribute)) {
                        $resultArray['msg'] = 'Missing key of attribute of setting "' . $moduleSetting->getName() . '".';
                        return $resultArray;
                    }
                    $values[] = $attribute['key'] . ':' . $attribute['value'];
                } else {

                    // Loop over the array of values of the multiselect
                    foreach ($attribute['value'] as $value) {
                        $values[] = $value;
                    }
                }
            }

            // Set data of resultArray
            $resultArray['success'] = true;
            $resultArray['data'] = $values;
        } else {
            $value = null;
            foreach ($attributes as $attribute) {
                // Check for 'value' index existence
                if (!array_key_exists('value', $attribute)) {
                    $resultArray['msg'] = 'Missing value of attribute of setting "' . $moduleSetting->getName() . '".';
                    return $resultArray;
                }
                $value = $attribute['value'];
            }

            // Set data of resultArray
            $resultArray['success'] = true;
            $resultArray['data'] = $value;
        }

        // return result
        return $resultArray;
    }

    /**
     * This function updates an adapter
     *
     * @param integer $adapterId
     * @param array $updatedAttributes
     * @param integer $adapterTypeId
     * @param integer $userId
     * @param array $deleted
     * @param array $modules
     *
     * @return array
     */
    public function updateAdapter($adapterId, $updatedAttributes, $adapterTypeId, $userId, $export, $deleted, $modules)
    {
        // Initialize result array
        $resultArray = array(
            'success' => false,
            'msg' => '',
            'data' => null
        );

        // Get new adapter type
        $adapterTypeRepository = $this->entityManager->getRepository('AppBundle:AdapterType');
        $adapterType = $adapterTypeRepository->findOneBy(array('adapterTypeId' => $adapterTypeId));
        if (!$adapterType) {
            $resultArray['msg'] = 'No adapter type was found with the provided id.';
            return $resultArray;
        }
        $updatedAttributes['adapterType'] = $adapterType;

        // Get new adapter user
        $userRepository = $this->entityManager->getRepository('AppBundle:User');
        $user = $userRepository->findOneBy(array('id' => $userId));
        if (!$user) {
            $resultArray['msg'] = 'No user was found with the provided id.';
            return $resultArray;
        }
        $updatedAttributes['user'] = $user;

        // Update the adapter's information
        $adapterRepository = $this->entityManager->getRepository('AppBundle:Adapter');
        $adapter = $adapterRepository->updateAdapter($adapterId, $updatedAttributes);

        // Make sure that adapter is updated successfully
        if (!$adapter) {
            $resultArray['msg'] = 'An error occurred while updating the adapter.';
            return $resultArray;
        }

        // Update the adapter's settings
        $adapterSettingRepository = $this->entityManager->getRepository('AppBundle:AdapterSetting');
        $exportSetting = $adapterSettingRepository->updateAdapterSettings($adapter, ['export' => $export]);

        // Make sure the setting was updated
        if (!$exportSetting) {
            $resultArray['msg'] = 'An error occurred while updating the adapter\' settings.';
            return $resultArray;
        }

        // Delete modules
        $deleted = $this->deleteModuleInstances($deleted);
        if (!$deleted) {
            $resultArray['msg'] = 'An error occurred while deleting the provided modules.';
            return $resultArray;
        }

        $order = 1;
        $moduleInstanceRepository = $this->entityManager->getRepository('AppBundle:ModuleInstance');
        foreach ($modules as $module) {
            if (array_key_exists('instance_id', $module)) {

                // Just edit the order
                $moduleInstance = $moduleInstanceRepository->updateModuleInstance(
                    $module['instance_id'],
                    array(
                        'executeOrder' => $order
                    )
                );

                // Make sure the module was updated successfully
                if (!$moduleInstance) {
                    $resultArray['msg'] = 'An error occurred while updating the module\'s order.';
                    return $resultArray;
                }
            } else {

                // Insert new module instance
                $moduleInstance = $moduleInstanceRepository->saveModuleInstance(
                    $adapter,
                    $module['id'],
                    $order,
                    $module['name'],
                    $this->entityManager
                );

                // Make sure the module instance was created successfully
                if (!$moduleInstance) {
                    $resultArray['msg'] = 'An error occurred while creating the module "' . $module['name'] . '".';
                    return $resultArray;
                }

                if (array_key_exists('settings', $module)) {
                    $settings = $module['settings'];
                    foreach ($settings as $setting) {
                        $attributes = $setting['attributes'];
                        $moduleInstanceSettingRepository = $this->entityManager->getRepository('AppBundle:ModuleInstanceSetting');
                        $moduleInstanceSetting = $moduleInstanceSettingRepository->saveMultipleModuleInstanceSetting(
                            $moduleInstance,
                            $setting['id'],
                            $attributes,
                            $this->entityManager
                        );

                        // Make sure the module instance settings were created successfully
                        if (!$moduleInstanceSetting) {
                            $resultArray['msg'] = 'An error occurred while creating the settings of the module "' . $module['name'] . '".';
                            return $resultArray;
                        }
                    }
                }
            }
            $order++;
        }

        // Set data of resultArray
        $resultArray['success'] = true;
        $resultArray['data'] = $adapter;

        // return result
        return $resultArray;
    }

    /**
     * A function to get all active adapters from the database.
     * @param string $sortBy
     * @param string $orderBy
     * @param integer $page
     * @param integer $limit
     *
     * @return array
     */
    public function getAllAdapters($sortBy, $orderBy, $page, $limit, $user = null)
    {
        // Initialize sort by map
        $sortByMap = array(
            'name' => 'name',
            'adapter_name' => 'name',
            'adapterName' => 'name',
            'type' => 'type',
            'adapter_type' => 'type',
            'adapterType' => 'type',
            'description' => 'description',
            'adapter_description' => 'description',
            'adapterDescription' => 'description',
            'user' => 'user',
            'adapter_user' => 'user',
            'adapterUser' => 'user',
            'createdAt' => 'createdAt',
            'created_at' => 'createdAt',
            'creation_time' => 'createdAt',
            'creationTime' => 'createdAt'
        );

        // Map the 'sortBy' input to the correct value
        if (array_key_exists($sortBy, $sortByMap)) {
            $sortBy = $sortByMap[$sortBy];
        } else {
            $sortBy = 'adapterId';
            $orderBy = 'ASC';
        }

        // Fetch the adapters from the database
        $adaptersResults = $this->entityManager->getRepository('AppBundle:Adapter')->fetchAllAdapters($sortBy, $orderBy, $page, $limit, $user);

        // Convert every adapter from object to array
        $adapters = [];
        foreach ($adaptersResults as $adapter) {
            $adapters[] = $this->convertObjectToArray($adapter);
        }

        // Get the number of adapters in the database
        $count = $adaptersResults->count();

        return array(
            'data' => $adapters,
            'total_count' => $count
        );
    }

    /**
     * A function to delete multiple module instances.
     *
     * @param array $modulesInstances
     */
    public function deleteModuleInstances($modulesInstances)
    {
        $moduleInstanceRepository = $this->entityManager->getRepository('AppBundle:ModuleInstance');
        foreach ($modulesInstances as $moduleInstanceId) {
            $deleted = $moduleInstanceRepository->deleteModuleInstance($moduleInstanceId);

            // Make sure the module instance was deleted
            if (!$deleted) {
                return false;
            }
        }

        // Save changes
        $this->entityManager->flush();
        return true;
    }

    /**
     * A function to create an adapter with its modules.
     *
     * @param string $adapterName
     * @param string $description
     * @param integer $userId
     * @param integer $adapterTypeID
     * @param array $modules
     *
     * @return array
     */
    public function saveAdapter($adapterName, $description, $userId, $adapterTypeID, $modules, $export, $jsonArray = null)
    {
    	 //print_r($modules);exit;
        // Initialize result array
        $resultArray = array(
            'success' => false,
            'msg' => '',
            'data' => null
        );

        // Get user
        $userRepository = $this->entityManager->getRepository('CequensBundle:User');
        $user = $userRepository->findOneBy(array('id' => $userId));
        if (!$user) {
            $resultArray['msg'] = 'No user was found with the provided id.';
            return $resultArray;
        }

        // Get adapter type
        $adapterRepository = $this->entityManager->getRepository('AppBundle:AdapterType');
        $adapterType = $adapterRepository->findOneBy(array('adapterTypeId' => $adapterTypeID));
        if (!$adapterType) {
            $resultArray['msg'] = 'No adapter type was found with the provided id.';
            return $resultArray;
        }

        // Check if adapter with the same name for this user exists
        $adapterRepository = $this->entityManager->getRepository('AppBundle:Adapter');
        $adapter = $adapterRepository->findOneBy(array('user' => $user, 'name' => $adapterName));
        if ($adapter) {
            $resultArray['msg'] = 'An adapter with the same name already exists for the same user.';
            return $resultArray;
        }

        // Create adapter
        $adapter = $adapterRepository->createAdapter($adapterName, $description, $user, $adapterType, $export);
        if (!$adapter) {
            $resultArray['msg'] = 'An error occurred while creating the adapter.';
            return $resultArray;
        }

        // Initialize execute order
        $order = 1;

        // Get module instance repository
        $moduleInstanceRepository = $this->entityManager->getRepository('AppBundle:ModuleInstance');

        // Get module instance setting repository
        $moduleInstanceSettingRepository = $this->entityManager->getRepository('AppBundle:ModuleInstanceSetting');

        //Get Module Instance Connection Repository
	    $moduleInstanceConnectionRepository = $this->entityManager->getRepository('AppBundle:ModuleInstanceConnection');
	    $moduleInstancesNewIds = array();

        // Iterate through modules
        foreach ($modules as $module) {

            // Check for 'id' field and 'name' field in the module
            if (!array_key_exists('id', $module) || !array_key_exists('name', $module)) {
                $resultArray['msg'] = 'Missing \'id\' field or \'name\' field of module.';
                return $resultArray;
            }

            // Create module instance
            $moduleInstance = $moduleInstanceRepository->saveModuleInstance(
                $adapter,
                $module['id'],
                $order,
                $module['name'],
                $this->entityManager
            );

            // Make sure that the module instance was successfully created
            if (!$moduleInstance) {
                $resultArray['msg'] = 'An error occurred while creating module "' . $module['name'] . '".';
                return $resultArray;
            }
            $moduleInstancesNewIds[$module['config']['divId']]['newId'] = $moduleInstance->getModuleInstanceId();
	        $moduleInstancesNewIds[$module['config']['divId']]['connections'] = $module['connections'];
	        $type = '';
	        if($moduleInstance->getModule()->getUiName()==='collect')
            {
                $type = 'collect';
            }
	        else if($moduleInstance->getModule()->getUiName()==='branch')
            {
                $type = 'branch';
            }
            $moduleInstancesNewIds[$module['config']['divId']]['type'] = $type;

            // Check if the module has settings that should be created
            if (array_key_exists('settings', $module)) {
                $settings = $module['settings'];

                // Iterate through settings
                foreach ($settings as $setting) {

                    // Check for 'id' field and 'attributes' field in each setting
                    if (!array_key_exists('id', $setting) || !array_key_exists('attributes', $setting)) {
                        $resultArray['msg'] = 'Missing \'id\' field or \'attributes\' field of setting.';
                        return $resultArray;
                    }
                    $attributes = $setting['attributes'];

                    // Create module instance setting
                    $moduleInstanceSetting = $moduleInstanceSettingRepository->saveMultipleModuleInstanceSetting(
                        $moduleInstance,
                        $setting['id'],
                        $attributes,
                        $this->entityManager
                    );

                    // Make sure that the module instance setting was successfully created
                    if (!$moduleInstanceSetting) {
                        $resultArray['msg'] = 'An error occurred while creating a module setting in module "' . $module['name'] . '".';
                        return $resultArray;
                    }
                }
            }


	        // Increment order
            $order++;
        }

	    foreach ($moduleInstancesNewIds as $moduleInstancesNewIdKey => $moduleInstancesNewIdValue) {
        	$modInst = $moduleInstanceRepository->find($moduleInstancesNewIdValue['newId']);
		    foreach ($moduleInstancesNewIdValue['connections'] as $itemConnection) {
		    	$modInstConn = new ModuleInstanceConnection();
		    	$modInstConn->setModuleInstance($modInst);
		    	if(array_key_exists('inputValue',$itemConnection))
			    {
				    $modInstConn->setConditionType('conditional');
				    $modInstConn->setConditionKey($moduleInstancesNewIdValue['type']);
				    $modInstConn->setConditionValue($itemConnection['inputValue']);
				    $modInstConn->setTargetModuleInstanceId($moduleInstancesNewIds[$itemConnection['targetDivId']]['newId']);
			    }
			    else
			    {
				    $modInstConn->setConditionType('none');
				    $modInstConn->setConditionKey('none');
				    $modInstConn->setConditionValue('');
				    $modInstConn->setTargetModuleInstanceId($moduleInstancesNewIds[$itemConnection['targetDivId']]['newId']);
			    }

		    	$modInstConn->setCreatedAt(new \DateTime('now'));
		    	$modInstConn->setUpdatedAt(new \DateTime('now'));
		    	$this->entityManager->persist($modInstConn);
		    	$this->entityManager->flush();
        	}
        }
        //print_r($moduleInstancesNewIds);exit;

        // Set data of resultArray
        $resultArray['success'] = true;
        $resultArray['data'] = $adapter;

        if(!empty($jsonArray))
        {
        	$adapter->setAdapterJson(json_encode($jsonArray));
        	$this->entityManager->persist($adapter);
        	$this->entityManager->flush();
        }

        // return result
        return $resultArray;
    }

    /**
     * A function to duplicate an adapter.
     *
     * @param integer $adapterId
     *
     * @return array
     */
    public function duplicateAdapter($adapterId)
    {
        try {
            $resultArray = array(
                'success' => false,
                'msg' => '',
                'data' => null
            );

            // Get adapter
            $adapterRepository = $this->entityManager->getRepository('AppBundle:Adapter');
            $adapter = $adapterRepository->find($adapterId);

            // return false if adapter was not found
            if (!$adapter) {
                $resultArray['msg'] = 'No adapter was found for the provided id.';
                return $resultArray;
            }

            // Clone the adapter
            $newAdapter = clone $adapter;
            $date = new \DateTime();
            $newName = $adapter->getName() . '_' . $date->format('d-m-Y H:i:s');
            $newAdapter->setName($newName);
            $newAdapter->setCreatedAt($date);
            $this->entityManager->persist($newAdapter);

            // Get adapter settings
            $adapterSettingRepository = $this->entityManager->getRepository('AppBundle:AdapterSetting');
            $adapterSettings = $adapterSettingRepository->findBy(['adapter' => $adapter]);

            // Clone each adapter setting
            foreach ($adapterSettings as $setting) {
                $newAdapterSetting = clone $setting;
                $newAdapterSetting->setAdapter($newAdapter);
                $this->entityManager->persist($newAdapterSetting);
            }

            // Get module instance setting repository
            $moduleIntanceSettingRepository = $this->entityManager->getRepository('AppBundle:ModuleInstanceSetting');

            // Clone each module instance
            foreach ($adapter->getModuleInstances() as $moduleInstance) {
                $newModuleInstance = clone $moduleInstance;
                $newModuleInstance->setAdapter($newAdapter);
                $this->entityManager->persist($newModuleInstance);

                // Get module instance settings
                $moduleInstanceSettings = $moduleIntanceSettingRepository->findBy(['moduleInstance' => $moduleInstance]);

                // Clone each module instance setting
                foreach ($moduleInstanceSettings as $moduleInstanceSetting) {
                    $newModuleInstanceSetting = clone $moduleInstanceSetting;
                    $newModuleInstanceSetting->setModuleInstance($newModuleInstance);
                    $newModuleInstanceSetting->setCreatedAt($date);
                    $this->entityManager->persist($newModuleInstanceSetting);
                }
            }
            $this->entityManager->flush();

            // Set data of resultArray
            $resultArray['success'] = true;
            $resultArray['data'] = $newAdapter;
        } catch (\Exception $e) {
            $resultArray['msg'] = 'An error occurred while duplicating the adapter.';
        }

        // return result
        return $resultArray;
    }

    /**
     * A function to convert an Object to an Array.
     * @param object $object
     *
     * @return array
     */
    public function convertObjectToArray($object)
    {
        return array(
            'adapter_id' => $object->getAdapterId(),
            'adapter_name' => $object->getName(),
            'adapter_type' => array(
                'type_id' => $object->getAdapterType()->getAdapterTypeId(),
                'type_name' => $object->getAdapterType()->getTypeName(),
            ),
            'adapter_description' => $object->getDescription(),
            'adapter_user' => array(
                'user_id' => $object->getUser()->getId(),
                'user_name' => $object->getUser()->getUsername()
            ),
            'created_at' => $object->getCreatedAt()->format('d/m/Y H:i:s'),
            'updated_at' => $object->getUpdatedAt()
        );
    }
}
