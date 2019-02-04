<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * AdapterType
 *
 * @ORM\Table(name="adapter_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdapterTypeRepository")
 */
class AdapterType
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="adapter_type_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $adapterTypeId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type_name", type="string", length=255)
	 */
	protected $typeName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type_description", type="string", length=255)
	 */
	protected $typeDescription;

	/**
	 * @var Adapter[]
	 *
	 * @ORM\OneToMany(targetEntity="Adapter", mappedBy="adapterType")
	 * @ORM\JoinColumn(name="adapter_id", referencedColumnName="adapter_id")
	 */
	protected $adapters;

	/**
	 * AdapterType constructor.
	 */
	public function __construct()
	{
		$this->adapters = new ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getAdapterTypeId()
	{
		return $this->adapterTypeId;
	}

	/**
	 * Set typeName
	 *
	 * @param string $typeName
	 *
	 * @return AdapterType
	 */
	public function setTypeName($typeName)
	{
		$this->typeName = $typeName;

		return $this;
	}

	/**
	 * Get typeName
	 *
	 * @return string
	 */
	public function getTypeName()
	{
		return $this->typeName;
	}

	/**
	 * Set typeDescription
	 *
	 * @param string $typeDescription
	 *
	 * @return AdapterType
	 */
	public function setTypeDescription($typeDescription)
	{
		$this->typeDescription = $typeDescription;

		return $this;
	}

	/**
	 * Get typeDescription
	 *
	 * @return string
	 */
	public function getTypeDescription()
	{
		return $this->typeDescription;
	}

	/**
	 * @param Adapter $adapter
	 */
	public function assignedToAdapter(Adapter $adapter)
	{
		$this->adapters[] = $adapter;
	}
}

