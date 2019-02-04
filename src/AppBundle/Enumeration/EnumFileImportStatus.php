<?php

namespace AppBundle\Enumeration;

class EnumFileImportStatus
{
	const   PENDING               = 1;
	const   IN_PROGRESS           = 2;
	const   IMPORTED              = 3;
	const   ROLLED_BACK           = 4;
	const   PARTIALLY_ROLLED_BACK = 5;
	const   ERROR                 = 6;
}
