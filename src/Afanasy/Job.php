<?php

namespace Afanasy;

class Job {

	const STATE_DONE	= " DON";

	private $data = [];
	private $blocks = [];

	public function __construct($jobname=null, $verbose=false) {
		global $cgruconfig;
		$this->data["name"]				= "noname";
		$this->data["user_name"]		= "default_username";
		$this->data["host_name"]		= gethostname();
		$this->data["priority"]			= 1;
		$this->data["time_creation"]	= time();
		if ( $jobname )
			$this->setName($jobname);
		}

	public function addBlock(&$block) {
		$this->blocks[] = $block;
		}

	public function setName($name) {
		if ( $name and strlen($name) )
			$this->data["name"] = $name;
		}

	public function setUserName($username) {
		if ( $username and strlen($username) )
			$this->data["user_name"] = strtolower($username);
		}

	public function setPriority($priority) {
		if ( $priority < 0)
			return;

		if ( $priority > 250 ) {
			$priority = 250;
			print("Warning: priority clamped to maximum = {$priority}");
			}

		$this->data["priority"] = $priority;
		}


	// public function setCmdPre(, command, TransferToServer=True)
	// {
	// 	"""Missing DocString

	// 	:param command:
	// 	:param TransferToServer:
	// 	:return:
	// 	"""
	// 	if TransferToServer:
	// 		command = Pathmap.toServer(command)
	// 	$this->data["command_pre"] = command
	// }


	// public function setCmdPost(, command, TransferToServer=True)
	// {
	// 	"""Missing DocString

	// 	:param command:
	// 	:param TransferToServer:
	// 	:return:
	// 	"""
	// 	if TransferToServer:
	// 		command = Pathmap.toServer(command)
	// 	$this->data["command_post"] = command
	// }


	public function setFolder($i_name, $i_folder, $i_transferToServer=false) {
		if ( ! array_key_exists("folders", $this->data) )
			$this->data["folders"] = [];

		$this->data["folders"][$i_name] = $i_folder;
		}

	public function fillBlocks() {
		$this->data["blocks"] = [];
		foreach($this->blocks as $block) {
			$block->fillTasks();
			$this->data["blocks"][] = $block->data;
			}
		}

	public function output() {
		$this->fillBlocks();
		print(json_encode($this->data, $options=JSON_PRETTY_PRINT));
		}

	public function getJSON() {
		if ( count($this->blocks) == 0 )
			print('Error: Job has no blocks');

		$this->fillBlocks();

		# Set folder if empty:
		if ( ! array_key_exists("folders", $this->data) ) {
			$this->data["folders"] = [];
			# Try to set output folder from files:
			foreach($this->blocks as $block)
				if ( in_array("files", $block->data) and count($block->data["files"]) )
					$this->data["folders"]["output"] = dirname( $block.data["files"][0] );
			}
		$obj = array("job" => $this->data);

		return json_encode($obj);
		}

	public function setAnnotation($value) {
		$this->data["annotation"] = $value;
		}


	public function setDescription($value) {
		$this->data["description"] = $value;
		}


	// public function setWaitTime(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if value > 0:
	// 		$this->data["time_wait"] = value
	// }


	// public function setMaxRunningTasks(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if value >= 0:
	// 		$this->data["max_running_tasks"] = value
	// }


	// public function setMaxRunTasksPerHost(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if value >= 0:
	// 		$this->data["max_running_tasks_per_host"] = value
	// }


	public function setHostsMask($value) {
		if ( checkRegExp($value) )
			$this->data["hosts_mask"] = $value;
		}


	// public function setHostsMaskExclude(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if checkRegExp(value):
	// 		$this->data["hosts_mask_exclude"] = value
	// }


	// public function setDependMask(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if checkRegExp(value):
	// 		$this->data["depend_mask"] = value
	// }


	// public function setDependMaskGlobal(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if checkRegExp(value):
	// 		$this->data["depend_mask_global"] = value
	// }


	// public function setNeedOS(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if checkRegExp(value):
	// 		$this->data["need_os"] = value
	// }


	// public function setNeedProperties(, value)
	// {
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if checkRegExp(value):
	// 		$this->data["need_properties"] = value
	// }


	// public function setNativeOS()
	// {
	// global $cgruconfig;
	// 	"""Missing DocString
	// 	"""
	// 	$this->data["need_os"] = $cgruconfig->VARS['platform'][-1]
	// }


	// public function setAnyOS()
	// {
	// 	"""Missing DocString
	// 	"""
	// 	$this->data["need_os"] = ''
	// }


	// public function setPPApproval()
	// {
	// 	$this->data["ppa"] = True
	// }


	// public function pause()
	// {
	// 	"""Missing DocString
	// 	"""
	// 	$this->data["offline"] = True
	// }


	// public function setPaused()
	// {
	// 	"""Missing DocString
	// 	"""
	// 	$this->data["offline"] = True
	// }


	// public function setOffline()
	// {
	// 	"""Missing DocString
	// 	"""
	// 	$this->data["offline"] = True
	// }


	// public function offline()
	// {
	// 	"""Missing DocString
	// 	"""
	// 	$this->data["offline"] = True
	// }


	// public function offLine()
	// {
	// 	"""Missing DocString
	// 	"""
	// 	$this->data["offline"] = True
	// }
		
	// public function setTimeLife(, value)
	// {
	// 	"""Set job's time-life after which it will automatically be deleted.

	// 	:param value: time in seconds
	// 	"""
	// 	# this will only pass positive int		
	// 	if str(value).isdigit():
	// 		$this->data['time_life'] = value
	// }
	}

?>