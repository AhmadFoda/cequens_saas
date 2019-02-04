<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 6/4/17
 * Time: 5:55 PM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\ModuleInstance;
use AppBundle\Entity\ModuleInstanceSetting;
use AppBundle\Entity\ModuleSetting;
use AppBundle\Enumeration\EnumModuleSettingType;

class ModuleInstanceSettingRepository extends EntityRepository
{
	/**
	 * This function receives the id of one module instance and returns all its loaded settings in the database
	 * as an array, which might have a nested array depending on data repeatability
	 *
	 * @param ModuleInstance $moduleInstance
	 *
	 * @return array
	 */
	public function getModuleInstanceSettings($moduleInstance)
	{
		$result   = [];
		$settings = $this->findBy(['moduleInstance' => $moduleInstance]);

		/**
		 * @var ModuleInstanceSetting[] $settings
		 */
		foreach ($settings as $setting) {
			$moduleSetting = $setting->getModuleSetting();
			$key           = $moduleSetting->getName();
			$value         = $setting->getValue();
			if (array_key_exists($key, $result)) {
				if (is_array($result[$key])) {
					$result[$key][] = $value;
				} else {
					$result[$key]   = [$result[$key]];
					$result[$key][] = $value;
				}
			} else {
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * This function gets the module instance settings of the provided module, along with the settings
	 * information of each setting
	 *
	 * @param ModuleInstance $moduleInstance
	 *
	 * @return array
	 * [
	 *	{
	 *		ModuleSettingId: {
	 *							type: {
	 *									id: integer,
	 *									name: string
	 *								  }
	 *							is_required: boolean
	 *							display_name: string
	 *							value: [integer]
	 *	 					}
	 *	}, {...}, {...}, ...
	 * ]
	 */
	public function getModuleInstanceSettingsWithInformation($moduleInstance)
	{
		$result = [];
		$instanceSettings = $this->findBy(['moduleInstance' => $moduleInstance]);

		// Loop over instance settings
		foreach ($instanceSettings as $setting) {
			$moduleSetting = $setting->getModuleSetting();
			$key = $moduleSetting->getModuleSettingId();
			$value = $setting->getValue();
			if (array_key_exists($key, $result)) {
				// The setting was added before but it has multiple values
				$result[$key]['value'][] = $value;
			} else {
				// Add a new setting with its setting information
				$result[$key]['type'] 			= array(
					'id'	=> $moduleSetting->getModuleSettingType()->getModuleSettingTypeId(),
					'name'	=> $moduleSetting->getModuleSettingType()->getTypeName()
				);
				$result[$key]['is_required']	= $moduleSetting->isRequired();
				$result[$key]['display_name'] 	= $moduleSetting->getDisplayName();
				$result[$key]['value']        	= [$value];
			}
		}

		return $result;
	}

	/**
	 * Save module instance settings
	 *
	 * @param ModuleInstance $moduleInstance
	 * @param $moduleSettingId
	 * @param $attributes
	 * @param EntityManager $entityManager
	 *
	 * @return ModuleInstanceSetting
	 */
	public function saveMultipleModuleInstanceSetting(ModuleInstance $moduleInstance, $moduleSettingId, $attributes, EntityManager $entityManager)
	{
		try {
			$moduleSettingRepository = $this->getEntityManager()->getRepository('AppBundle:ModuleSetting');
			$moduleSetting           = $moduleSettingRepository->findOneBy(array('moduleSettingId' => $moduleSettingId));

			$moduleSettingTypeId = $moduleSetting->getModuleSettingType()->getModuleSettingTypeId();
			if (in_array($moduleSettingTypeId, array(EnumModuleSettingType::MULTISELECT))) {
				foreach ($attributes as $attribute) {
					foreach ($attribute['value'] as $value) {
						$moduleInstanceSetting = $this->saveModuleInstanceSetting(
							$moduleInstance,
							$moduleSettingId,
							$value,
							$entityManager,
							true
						);
					}
				}
			} else {
				foreach ($attributes as $attribute) {
					$moduleInstanceSetting = $this->saveModuleInstanceSetting(
						$moduleInstance,
						$moduleSettingId,
						$attribute,
						$entityManager,
						false
					);
				}
			}

			return $moduleInstanceSetting;
		} catch (Exception $e) {
			$this->logger->addError(
				'Problem saving moduleInstanceSetting: ' . $e->getMessage(),
				['exception_trace' => $e->getTrace()]
			);
			return false;
		}
	}

	/**
	 * Save single module instance setting
	 *
	 * @param ModuleInstance $moduleInstance
	 * @param                $moduleSettingId
	 * @param                $attribute
	 * @param EntityManager  $entityManager
	 * @param                $isSelect
	 *
	 * @return ModuleInstanceSetting
	 */
	public function saveModuleInstanceSetting(ModuleInstance $moduleInstance, $moduleSettingId, $attribute, EntityManager $entityManager, $isSelect)
	{
		$moduleInstanceSetting      = new ModuleInstanceSetting();
		$moduleInstanceRepository   = $this->getEntityManager()->getRepository('AppBundle:ModuleInstance');
		$moduleInstanceObject       = $moduleInstanceRepository->findOneBy(array('moduleInstanceId' => $moduleInstance->getModuleInstanceId()));

		// Set ModuleInstance
		$moduleInstanceSetting->setModuleInstance($moduleInstanceObject);
		$moduleSettingRepository    = $this->getEntityManager()->getRepository('AppBundle:ModuleSetting');
		$moduleSettingObject        = $moduleSettingRepository->findOneBy(
			array('moduleSettingId' => $moduleSettingId)
		);

		// Set ModuleSetting
		$moduleInstanceSetting->setModuleSetting($moduleSettingObject);

		// Handle different structure of array for SELECT, Multiselect, and NameValueArray
		if ($isSelect) {
			$moduleInstanceSetting->setValue($attribute);
		} else {
			if (array_key_exists('key', $attribute)) {
				$value = $attribute['key'] . ':' . $attribute['value'];
				$moduleInstanceSetting->setValue($value);
			} else {
				$moduleInstanceSetting->setValue($attribute['value']);
			}
		}
		$moduleInstanceSetting->setCreatedAt(new \DateTime());
		$moduleInstanceSetting->setUpdatedAt(new \DateTime());

		//Persist Module Instance Setting Object.
		$entityManager->persist($moduleInstanceSetting);
		$entityManager->flush();
		return $moduleInstanceSetting;
	}


    /**
     * Delete all module instance setting of a setting type for a module instance
     *
     * @param ModuleSetting  $moduleSetting
     * @param ModuleInstance $moduleInstance
     *
     * @return boolean
     */
    public function deleteModuleInstanceSettingValues(ModuleSetting $moduleSetting, ModuleInstance $moduleInstance)
    {
        try {
            $query = $this->createQueryBuilder('settings');
            $query->delete();
            $query->andWhere('settings.moduleSetting = :moduleSetting')
                ->setParameter('moduleSetting', $moduleSetting->getModuleSettingId());
            $query->andWhere('settings.moduleInstance = :moduleInstance')
                ->setParameter('moduleInstance', $moduleInstance);
            $query = $query->getQuery();
            $query->getResult();
            return true;
        } catch (\Exception $e) {
            $this->logger->addError(
                'Problem deleting moduleInstanceSetting: ' . $e->getMessage(),
                ['exception_trace' => $e->getTrace()]
            );
            return false;
        }
    }

	/**
	 * This function updates a module instance setting value(s)
	 *
	 * @param ModuleInstance $moduleInstance
	 * @param ModuleSetting  $moduleSetting
	 * @param array|string   $data
	 *
	 * @return boolean
	 */
	public function updateModuleInstanceSetting($moduleInstance, $moduleSetting, $data)
	{
		if (is_array($data)) {

            // The setting is multiselect, value array, or name value array
            // Delete all settings of this type for this module instance
            $deleted = $this->deleteModuleInstanceSettingValues($moduleSetting, $moduleInstance);

            // Check that they are deleted
            if (!$deleted) {
                return false;
            }

			// Loop over all values
			foreach ($data as $value) {
				$moduleInstanceSetting = $this->addModuleInstanceSetting($moduleInstance, $moduleSetting, $value);

                // Check that module instance setting was added successfully
                if (!$moduleInstanceSetting) {
                    return false;
                }
            }
        } else {

			// Any other setting is just an update
			// Check for existence of module instance setting
			$moduleInstanceSetting = $this->findOneBy(['moduleInstance' => $moduleInstance, 'moduleSetting' => $moduleSetting]);
			if (!$moduleInstanceSetting) {
				return false;
			}

			// Update the setting with the value
			$moduleInstanceSetting->setValue($data);
		}

        // Save changes
        $this->getEntityManager()->flush();

        // Setting updated successfully
        return true;
    }

    /**
	 * Create a new module instance setting
	 *
	 * @param ModuleInstance $moduleInstance
	 * @param                $moduleSetting
	 * @param                $value
	 *
	 * @return ModuleInstanceSetting
	 */
	public function addModuleInstanceSetting($moduleInstance, $moduleSetting, $value)
	{
		try {
			$moduleInstanceSetting = new ModuleInstanceSetting();
			$moduleInstanceSetting->setModuleInstance($moduleInstance);
			$moduleInstanceSetting->setModuleSetting($moduleSetting);
			$moduleInstanceSetting->setValue($value);
			$moduleInstanceSetting->setCreatedAt(new \DateTime());
			$moduleInstanceSetting->setUpdatedAt(new \DateTime());
			$this->getEntityManager()->persist($moduleInstanceSetting);
			return $moduleInstanceSetting;
		} catch (\Exception $e) {
			$this->logger->addError(
				'Problem adding moduleInstanceSetting: ' . $e->getMessage(),
				['exception_trace' => $e->getTrace()]
			);
			return false;
		}
	}
}