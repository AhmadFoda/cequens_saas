<?php
/**
 * Created by PhpStorm.
 * User: karim Mohamed
 * Date: 6/29/2017
 * Time: 9:38 PM
 */

namespace AppBundle\Enumeration;

class EnumFileStorageType
{
	const S3    = 's3';
	const LOCAL = 'local';
	static $fileStorageTypes = array(
		self::S3,
		self::LOCAL,
	);

}