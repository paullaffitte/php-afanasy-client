<?php

namespace Afanasy;

use Exception;

class Block {

	private $data = [];
	private $tasks = [];

	public function __construct($blockname='block', $service='generic') {
		$this->data["name"] = $blockname;
		$this->data["capacity"] = 1;
		$this->data["working_directory"] = "/tmp";
		$this->data["numeric"] = false;

		$this->setService($service);
		$this->setParser('generic');
		}

	public function getData() {
		if ( !count($this->tasks) )
			throw new Exception("zero task block");
		
		return $this->data;
		}

	public function addTask(&$task) {
		$this->tasks[] = $task;
		}

	public function setService($service, $nocheck=true) {
		if ( empty($service) )
			throw new Exception("service can not be empty");
		$this->data["service"] = $service;
		}

	public function setParser($parser, $nocheck=true) {
		if ( empty($parser) )
			throw new Exception("parser can not be empty");
		$this->data["parser"] = $parser;
		}

	public function setNumeric($start=1, $end=1, $pertask=1, $increment=1) {
		if ( count($this->tasks) )
			throw new Exception("Block.setNumeric: Block already has tasks.");
		if ( $end < $start )
			throw new Exception("Block.setNumeric: end < start ({$end} < {$start})");
		if ( $pertask < 1 )
			throw new Exception("Block.setNumeric: pertask < 1 ({$pertask} < 1)");

		$this->data["numeric"] = true;
		$this->data["frame_first"] = $start;
		$this->data["frame_last"] = $end;
		$this->data["frames_per_task"] = $pertask;
		$this->data["frames_inc"] = $increment;
		}



	// public function setFramesPerTask(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["frames_per_task"] = value


	// public function setSequential(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["sequential"] = value


	public function setCapacity($capacity) {
		if ( $capacity > 0 )
			$this->data["capacity"] = $capacity;
		}


	// public function setVariableCapacity(, capacity_coeff_min, capacity_coeff_max)
	// {

	// }
	// 	"""Missing DocString

	// 	:param capacity_coeff_min:
	// 	:param capacity_coeff_max:
	// 	:return:
	// 	"""
	// 	if capacity_coeff_min >= 0 or capacity_coeff_max >= 0:
	// 		$this->data["capacity_coeff_min"] = capacity_coeff_min
	// 		$this->data["capacity_coeff_max"] = capacity_coeff_max

	public function setWorkingDirectory($working_directory, $transfertoserver=false) {
		$this->data["working_directory"] = $working_directory;
		}

	public function setCommand($command, $prefix=false, $transfertoserver=false) {
		$this->data["command"] = $command;
		}

	// public function setCmdPre(, command_pre, TransferToServer=True)
	// {

	// }
	// 	"""Missing DocString

	// 	:param command_pre:
	// 	:param TransferToServer:
	// 	:return:
	// 	"""
	// 	if TransferToServer:
	// 		command_pre = Pathmap.toServer(command_pre)
	// 	$this->data["command_pre"] = command_pre

	// public function setCmdPost(, command_post, TransferToServer=True)
	// {

	// }
	// 	"""Missing DocString

	// 	:param command_post:
	// 	:param TransferToServer:
	// 	:return:
	// 	"""
	// 	if TransferToServer:
	// 		command_post = Pathmap.toServer(command_post)
	// 	$this->data["command_post"] = command_post

	public function setFiles($files, $transfertoserver=false) {
		if ( ! array_key_exists("files", $this->data) )
			$this->data["files"] = [];

		foreach($files as $afile) {
			$this->data["files"][] = $afile;
			}
		}

	// public function setName(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["name"] = value

	// public function setTasksName(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["tasks_name"] = value

	// public function setParserCoeff(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["parser_coeff"] = value

	// public function setErrorsAvoidHost(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["errors_avoid_host"] = value

	// public function setErrorsForgiveTime(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["errors_forgive_time"] = value

	// public function setErrorsRetries(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["errors_retries"] = value

	// public function setErrorsTaskSameHost(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["errors_task_same_host"] = value

	public function setNeedHDD($value) {
		$this->data["need_hdd"] = $value;
		}

	public function setNeedMemory($value) {
		$this->data["need_memory"] = $value;
		}


	// public function setNeedPower(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["need_power"] = value

	// public function setDependSubTask(, value=True)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["depend_sub_task"] = value

	// public function setTasksMaxRunTime(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if value > 0:
	// 		$this->data["tasks_max_run_time"] = value

	// public function setMaxRunningTasks(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if value >= 0:
	// 		$this->data["max_running_tasks"] = value

	// public function setMaxRunTasksPerHost(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if value >= 0:
	// 		$this->data["max_running_tasks_per_host"] = value

	// public function setHostsMask(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if Utils::checkRegExp(value):
	// 		$this->data["hosts_mask"] = value

	// public function setHostsMaskExclude(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if Utils::checkRegExp(value):
	// 		$this->data["hosts_mask_exclude"] = value

	public function setDependMask($value) {
		if ( Utils::checkRegExp($value) )
			$this->data["depend_mask"] = $value;
		}


	// public function setTasksDependMask(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if Utils::checkRegExp(value):
	// 		$this->data["tasks_depend_mask"] = value

	// public function setNeedProperties(, value)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	if Utils::checkRegExp(value):
	// 		$this->data["need_properties"] = value

	// # public function setGenThumbnails(, value = True)
	// 	{

	// 	}
	// # $this->data["gen_thumbnails"] = value;

	// public function setDoPost(, value=True)
	// {

	// }
	// 	"""Missing DocString

	// 	:param value:
	// 	:return:
	// 	"""
	// 	$this->data["do_post"] = value

	// public function setMultiHost(, h_min, h_max, h_max_wait, master_on_slave=False
	// {

	// }
	// 				 service=None, service_wait=-1):
	// 	"""Missing DocString

	// 	:param h_min:
	// 	:param h_max:
	// 	:param h_max_wait:
	// 	:param master_on_slave:
	// 	:param service:
	// 	:param service_wait:
	// 	:return:
	// 	"""
	// 	if h_min < 1:
	// 		print('Error: Block::setMultiHost: Minimum must be greater then '
	// 			  'zero.')
	// 		return False

	// 	if h_max < h_min:
	// 		print('Block::setMultiHost: Maximum must be greater or equal then '
	// 			  'minimum.')
	// 		return False

	// 	if master_on_slave and service is None:
	// 		print('Error: Block::setMultiHost: Master in slave is enabled but '
	// 			  'service was not specified.')
	// 		return False

	// 	$this->data['multihost_min'] = h_min
	// 	$this->data['multihost_max'] = h_max
	// 	$this->data['multihost_max_wait'] = h_max_wait

	// 	if master_on_slave:
	// 		$this->data['multihost_master_on_slave'] = master_on_slave

	// 	if service:
	// 		$this->data['multihost_service'] = service

	// 	if service_wait > 0:
	// 		$this->data['multihost_service_wait'] = service_wait

	public function fillTasks() {
		if ( count($this->tasks) )
			$this->data["tasks"] = [];
			foreach($this->tasks as $task)
				array_push($this->data["tasks"], $task->getData());
		}
	}

?>