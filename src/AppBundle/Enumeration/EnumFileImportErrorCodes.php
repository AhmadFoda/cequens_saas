<?php

namespace AppBundle\Enumeration;

class EnumFileImportErrorCodes
{
	const SUCCESS_EXIT_CODE                      = 0;
	const SOMETHING_WENT_WRONG                   = -1;
	const FILE_IMPORT_STATUS_NOT_MATCHING        = -2;
	const EXCEPTION_WHILE_PROCESSING_FILE_IMPORT = -3;
}
