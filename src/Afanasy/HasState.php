<?php

namespace Afanasy;

use Afanasy\States;

trait HasState {
	public function hasState($state) {
		return States::arrayHasState($this->data, $state);
		}
	}

?>
