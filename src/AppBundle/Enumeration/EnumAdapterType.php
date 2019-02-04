<?php
/**
 * Created by PhpStorm.
 * User: karim Mohamed
 * Date: 6/28/2017
 * Time: 3:48 PM
 */

namespace AppBundle\Enumeration;

class EnumAdapterType
{
	const PROJECT 	= 1;
	const INVOICES 	= 2;

	static $adapterTypeMap = array(
		self::PROJECT 	=> 'Project',
		self::INVOICES 	=> 'Invoices',
	);
}