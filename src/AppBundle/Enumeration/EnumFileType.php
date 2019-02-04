<?php

namespace AppBundle\Enumeration;

class EnumFileType
{
	const XLSX  = 1;
	const CSV   = 2;
	const TXT   = 3;

	static $fileTypeMapping = array(
		'xlsx'  => self::XLSX,
		'xls'  => self::XLSX,
		'csv'   => self::CSV,
		'txt'   => self::TXT,
	);

	static $fileTypeArray = array(
		'xlsx',
		'xls',
		'csv',
		'txt'
	);
}
