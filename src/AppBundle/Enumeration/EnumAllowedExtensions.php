<?php

namespace AppBundle\Enumeration;

class EnumAllowedExtensions
{
	static $ALLOWED_MIMETYPE_EXTENSIONS = array(
		'text/plain' => 'txt',
		'application/vnd.ms-excel' => 'xlsx',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
		'txt/csv' => 'csv',
	);
}
