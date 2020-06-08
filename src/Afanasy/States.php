<?php

namespace Afanasy;

class States {
	const READY					= "RDY";
	const RUNNING				= "RUN";
	const DONE					= "DON";
	const ERROR					= "ERR";
	const SKIPPED				= "SKP";
	const WAITING_DEPENDENCIES	= "WD";
	const WAITING_TIME			= "WT";
	const PREVIEW				= "PPA";
	const OFFLINE				= "OFF";

	static function arrayHasState($array, $state) {
		return array_key_exists('state', $array)
			? in_array($state, explode(' ', $array['state']))
			: false;
	}
}
