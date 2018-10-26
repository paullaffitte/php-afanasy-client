<?php

namespace Afanasy;

trait HasState {
	public function hasState($state) {
		return getFromArray($this->data, 'state', true) == $state;
		}
	}

?>
