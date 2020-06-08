<?php

namespace Afanasy;

use Exception;

class Network {

	protected $socket;
	protected $userName;
	protected $hostName;

	public function __construct($address, $port, $user='coord@pc') {
		$this->socket = new Socket($address, $port);
		[ $this->userName, $this->hostName ] = explode('@', $user);
	}

	public function setSocket($socket) {
		$this->socket = $socket;
	}

	public function deleteJob($id) {
		return $this->deleteJobs([$id]);
	}

	public function deleteJobs($ids) {
		return $this->action('jobs', $ids, [
			'operation' => [
				'type'	=> 'delete',
			]
		]);
	}

	public function getJobsByUser($user) {
		return $this->get([
			"type"		=> "jobs",
			"user_name"	=> $user
		]);
	}

	public function getJobById($id) {
		$jobs = $this->getJobByIds([ $id ])['jobs'];

		if ( count($jobs) == 0 )
			throw new Exception("Job with id {$id} not found");

		return $jobs[0];
	}

	public function getJobsByIds($ids) {
		return $this->get([
			"type"	=> "jobs",
			"ids"	=> $ids
		]);
	}

	public function getAllJobs() {
		return $this->get([
			"type"	=> "jobs"
		]);
	}

	public function sendJob($job) {
		$ret = $this->execute($job->getJSON(), false);

		if ( !is_array($ret) )
			throw new Exception("invalid response from server");
		if ( array_key_exists('error', $ret) )
			throw new Exception($ret['error']);

		return $ret;
	}

	public function restartErrors($ids) {
		$this->action('jobs', $ids, [
			'operation' => [
				'type' => 'restart_errors',
			]
		]);
	}

	public function resetErrorHosts($ids) {
		$this->action('jobs', $ids, [
			'operation' => [
				'type' => 'reset_error_hosts',
			]
		]);
	}

	public function get($filters, $json_encode = true) {
		return $this->execute([ 'get' => $filters ], $json_encode);
	}

	public function action($type, $ids, $options, $json_encode = true) {
		return $this->execute([
			'action' => array_merge($options, [
				'user_name' => $this->userName,
				'host_name' => $this->hostName,
				'type' => $type,
				'ids' => $ids
			]),
		], $json_encode);
	}

	protected function execute($message, $json_encode = true) {
		$this->socket->connect();
		$ret = $this->socket->send($message, $json_encode);
		$this->socket->disconnect();
		return $ret;
	}
}
