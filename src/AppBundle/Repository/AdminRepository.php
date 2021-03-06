<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Admin;

/**
 * AdminRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdminRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * Get all admins array(id => name)
	 * @return array
	 */
	public function getAllAdmins()
	{
		/**
		 * @var Admin $admin
		 */
		$admins = [];

		$allAdmins = $this->findAll();
		foreach ($allAdmins as $admin) {
			$admins[$admin->getAdminId()] = $admin->getUsername();
		}
		return $admins;
	}
}
