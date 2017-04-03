<?php

namespace Afanasy;

class Task {

	private $data;

	public function __construct($taskname='') {
		$this->setName($taskname);
		}

	public function getData() {
		return $this->data;
		}

	public function setName($name) {
		if ( $name and strlen($name) )
			$this->data["name"] = $name;
		}

	public function setCommand($command, $transfertoserver=false) {
		$this->data["command"] = $command;
		}

	public function setFiles($files, $transfertoserver=false) {
		if ( ! in_array("files", $this->data) )
			$this->data["files"] = [];

		foreach($files as $afile) {
			array_push($this->data["files"], $afile);
			}
		}
	}

?>