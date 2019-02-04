<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Entity\Adapter;
use AppBundle\Entity\AdapterSetting;
use AppBundle\Entity\AdapterType;
use AppBundle\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AdapterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdapterRepository extends EntityRepository
{
    /**
     * Get all adapters array(adapter_id => company_name: adapter name)
     * @return array
     */
    public function getAllAdapters()
    {
        /**
         * @var Adapter $adapter
         */
        $adapters = [];

        $allAdapters = $this->findBy(array('isActive' => 1));
        foreach ($allAdapters as $adapter) {
            if ($adapter->getIsActive() == 1) {

                $adapters[$adapter->getAdapterId()] = $adapter->getUser()->getUsername() . ': ' . $adapter->getName();
            }
        }
        return $adapters;
    }

    /**
     * Fetch all adapters from the database
     * @param string $sortBy
     * @param string $orderBy
     * @param integer $currentPage
     * @param integer $limit
     *
     * @return Paginator
     */
    public function fetchAllAdapters($sortBy, $orderBy, $currentPage, $limit, $user = null)
    {
        // Create query
        $query = $this->createQueryBuilder('adapters')
            ->where('adapters.isActive = 1');

        if (!empty($user)) {
            $query->join('adapters.user', 'user',Query\Expr\Join::WITH,'user.id = :user_id');
            $query->setParameter('user_id',$user->getId());
        }

        // Check for the column to be sorted by
        switch ($sortBy) {
            case 'type':
                $query->join('adapters.adapterType', 'type');
                $query->addOrderBy('type.typeName', $orderBy);
                break;
            case 'user':
                $query->join('adapters.user', 'user');
                $query->addOrderBy('user.username', $orderBy);
                break;
            default:
                //$query->addOrderBy('adapters.' . $sortBy, $orderBy);
        }

        // Add default sorting column
        $query->addOrderBy('adapters.adapterId', 'ASC');

        // Set offset and limit
        $query->setFirstResult($limit * ($currentPage - 1))
            ->setMaxResults($limit);

        // Get the query object
        $query->getQuery();

        // Construct the Paginator Object
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * Create new adapter
     *
     * @param string $adapterName
     * @param string $description
     * @param User $user
     * @param AdapterType $adapterType
     *
     * @return Adapter
     */
    public function createAdapter($adapterName, $description, \CequensBundle\Entity\User $user, AdapterType $adapterType, $export)
    {
        try {
            $adapter = new Adapter();
            $adapter->setName($adapterName);
            $adapter->setDescription($description);
            $adapter->setUser($user);
            $adapter->setAdapterType($adapterType);
            $adapter->setCreatedAt(new \DateTime());
            $this->getEntityManager()->persist($adapter);

            // Set Adapter Setting export
            $adapterSettingExport = new AdapterSetting();
            $adapterSettingExport->setAdapter($adapter);
            $adapterSettingExport->setName('export');
            $adapterSettingExport->setDescription('Export to CSV');
            $adapterSettingExport->setValue($export);
            $this->getEntityManager()->persist($adapterSettingExport);

            // Set Adapter Setting exportStorage
            $adapterSettingExportStorage = new AdapterSetting();
            $adapterSettingExportStorage->setAdapter($adapter);
            $adapterSettingExportStorage->setName('exportStorage');
            $adapterSettingExportStorage->setDescription('Export to local or s3 storage');
            $adapterSettingExportStorage->setValue('s3');
            $this->getEntityManager()->persist($adapterSettingExportStorage);

            // Save changes
            $this->getEntityManager()->flush();

            // return created adapter
            return $adapter;
        } catch (Exception $e) {
            $this->logger->addError(
                'Problem saving adapter: ' . $e->getMessage(),
                ['exception_trace' => $e->getTrace()]
            );
            return false;
        }
    }

    /***
     * Create new adapter
     *
     * @param               $adapterTypeID
     * @param User $user
     * @param               $adapterName
     * @param               $description
     * @param EntityManager $entityManager
     *
     * @return Adapter
     */
    public function saveAdapter($adapterTypeID, User $user, $adapterName, $description, $export, EntityManager $entityManager)
    {
        try {
            $adapter = new Adapter();
            if ($adapterTypeID != '') {
                $adapterRepository = $this->getEntityManager()->getRepository('AppBundle:AdapterType');
                $adapterTypeObject = $adapterRepository->findOneBy(array('adapterTypeId' => $adapterTypeID));
                $adapter->setAdapterType($adapterTypeObject);
            }
            $adapter->setName($adapterName);
            $adapter->setUser($user);
            $adapter->setIsActive(1);

            if ($description != '') {
                $adapter->setDescription($description);
            }
            $adapter->setCreatedAt(new \DateTime());
            $entityManager->persist($adapter);

            // Set Adapter Setting export
            $adapterSettingExport = new AdapterSetting();
            $adapterSettingExport->setAdapter($adapter);
            $adapterSettingExport->setName('export');
            $adapterSettingExport->setDescription('Export to CSV');
            $adapterSettingExport->setValue($export);
            $entityManager->persist($adapterSettingExport);

            // Set Adapter Setting exportStorage
            $adapterSettingExportStorage = new AdapterSetting();
            $adapterSettingExportStorage->setAdapter($adapter);
            $adapterSettingExportStorage->setName('exportStorage');
            $adapterSettingExportStorage->setDescription('Export to local or s3 storage');
            $adapterSettingExportStorage->setValue('s3');
            $entityManager->persist($adapterSettingExportStorage);

            $entityManager->flush();
        } catch (Exception $e) {

            $this->logger->addError(
                'Problem saving adapter: ' . $e->getMessage(),
                ['exception_trace' => $e->getTrace()]
            );
        }
        return $adapter;
    }

    /**
     * Update an adapter
     * @param integer $adapterId
     * @param array $valuesArray
     *
     * @return Adapter
     */
    public function updateAdapter($adapterId, $valuesArray)
    {
        try {
            $adapter = $this->find($adapterId);

            // Check for existence of adapter
            if (!$adapter) {
                return false;
            }

            // Change adapter name if provided
            if (array_key_exists('name', $valuesArray)) {
                $adapter->setName($valuesArray['name']);
            }

            // Change adapter type if provided
            if (array_key_exists('adapterType', $valuesArray)) {
                $adapter->setAdapterType($valuesArray['adapterType']);
            }

            // Change adapter user if provided
            if (array_key_exists('user', $valuesArray)) {
                $adapter->setUser($valuesArray['user']);
            }

            // Change adapter description if provided
            if (array_key_exists('description', $valuesArray)) {
                $adapter->setDescription($valuesArray['description']);
            }

            // Save changes
            $this->getEntityManager()->flush();

            // return adapter
            return $adapter;
        } catch (Exception $e) {
            $this->logger->addError(
                'Problem updating adapter: ' . $e->getMessage(),
                ['exception_trace' => $e->getTrace()]
            );
            return false;
        }
    }
}