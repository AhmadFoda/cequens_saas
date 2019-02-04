<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdapterSetting
 *
 * @ORM\Table(name="adapter_setting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdapterSettingRepository")
 */
class AdapterSetting
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="adapter_setting_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $adapterSettingId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	protected $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255, nullable=true)
	 */
	protected $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="value", type="string", length=255)
	 */
	protected $value;

	/**
	 * @var Adapter
	 *
	 * @ORM\ManyToOne(targetEntity="Adapter", inversedBy="adapterSettings")
	 * @ORM\JoinColumn(name="adapter_id", referencedColumnName="adapter_id")
	 */
	protected $adapter;

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getAdapterSettingId()
	{
		return $this->adapterSettingId;
	}

	/**
	 * Set adapterId
	 *
	 * @param Adapter $adapter
	 *
	 * @return AdapterSetting
	 */
	public function setAdapter(Adapter $adapter)
	{
		$adapter->assignedToAdapterSetting($this);
		$this->adapter = $adapter;

		return $this;
	}

	/**
	 * Get adapterId
	 *
	 * @return Adapter
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return AdapterSetting
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return AdapterSetting
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set value
	 *
	 * @param string $value
	 *
	 * @return AdapterSetting
	 */
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
}

