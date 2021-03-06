<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
	public function getAllUsers()
	{
		/**
		 * @var User $user
		 */
		$users = [];
		$allUsers = $this->findBy(['isActive' => 1]);
		foreach ($allUsers as $user) {
			$users[$user->getId()] = $user->getUsername();
		}
		return $users;
	}
}
