<?php

namespace Afanasy;

use Exception;

class Network extends BaseNetwork {

	public function __construct($address, $port, $user='coord@pc') {
		parent::__construct($address, $port, $user);
	}

	public function deleteJob($id) {
		return $this->deleteJobs([$id]);
	}

	public function deleteJobs($ids) {
		return $this->delete('jobs', $ids);
	}

	public function getJobsByUser($user) {
		return $this->get([
			'type'		=> 'jobs',
			'user_name'	=> $user
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
			'type'	=> 'jobs',
			'ids'	=> $ids
		]);
	}

	public function getAllJobs() {
		return $this->getAll('jobs');
	}

	public function sendJob($job) {
		$ret = $this->execute($job->getJSON(), false);

		if ( !is_array($ret) )
			throw new Exception('invalid response from server');
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

	public function pauseJobs($ids) {
		return $this->action('jobs', $ids, [
			'operation' => [ 'type' => 'pause' ],
		]);
	}

	public function getRenders() {
		return $this->getAll('renders');
	}

	public function deleteRenders($ids) {
		return $this->delete('renders', $ids);
	}

	public function updateRendersCapacity($ids, $capacity) {
		return $this->action('renders', $ids, [
			'params' => [
				'capacity' => $capacity
			],
		]);
	}
}
