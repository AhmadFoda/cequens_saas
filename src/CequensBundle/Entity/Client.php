<?php
/**
 * Created by PhpStorm.
 * User: k.mohamed
 * Date: 4/9/2018
 * Time: 2:45 PM
 */

namespace CequensBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Client extends BaseClient
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 *@ORM\ManyToOne(targetEntity="CequensBundle\Entity\User")
	 *@ORM\JoinColumn(name="user_id",referencedColumnName="id")
	 */
	protected $user;

	public function __construct()
	{
		parent::__construct();
		// your own logic
	}

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
		return $this;
	}
}