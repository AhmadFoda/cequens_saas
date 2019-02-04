<?php

namespace AppBundle\Enumeration;

class EnumExecutionStatus
{
	const NEW_UPLOAD	                = 1;
	const ADAPTATION_PENDING	        = 2;
	const ADAPTATION_IN_PROGRESS        = 3;
	const IMPORT_PENDING		        = 4;
	const IMPORT_IN_PROGRESS	        = 5;
	const POSTPROCESSING_PENDING        = 6;
	const POSTPROCESSING_IN_PROGRESS    = 7;
	const FINISHED		                = 8;
	const FAILED		                = 9;

	static $terminalExecutionStatuses = array(
		self::FINISHED,
		self::FAILED
	);

	static $bannedFromDeletionStatuses = array(
		self::ADAPTATION_IN_PROGRESS,
	);

	static $executionStatusMap = array(
		self::NEW_UPLOAD	                    => 'New',
		self::ADAPTATION_PENDING                => 'Adaptation Pending',
		self::ADAPTATION_IN_PROGRESS            => 'Adaptation In Progress',
		self::IMPORT_PENDING                    => 'Campaign Pending',
		self::IMPORT_IN_PROGRESS                => 'Campaign In Progress',
		self::POSTPROCESSING_PENDING            => 'Postprocessing Pending',
		self::POSTPROCESSING_IN_PROGRESS        => 'Postprocessing In Progress',
		self::FINISHED                          => 'Finished',
		self::FAILED                            => 'Failed',
	);

	// To map statuses coming from core app through RabbitMQ
	static $executionStatusMapFromCore = array(
		// Status 'New' on the Core App means that the import was removed from the pending imports queue.
		// We are mapping 'New' to 'IMPORT_PENDING' since for the Import Studio the import is still pending import.
		// We may want to change this in the future to a new Import Studio status IMPORT_PAUSED.
		'New'                               => self::IMPORT_PENDING,
		'Import Pending'                    => self::IMPORT_PENDING,
		'Import In Progress'                => self::IMPORT_IN_PROGRESS,
		'Postprocessing Pending'            => self::POSTPROCESSING_PENDING,
		'Postprocessing In Progress'        => self::POSTPROCESSING_IN_PROGRESS,
		'Finished'                          => self::FINISHED,
		'Failed'                            => self::FAILED
	);
}