<?php

namespace Afanasy;

use Exception;

class BaseNetwork {

	protected $socket;
	protected $userName;
	protected $hostName;

	public function __construct($address, $port, $user) {
		$this->socket = new Socket($address, $port);
		[ $this->userName, $this->hostName ] = explode('@', $user);
	}

	public function setSocket($socket) {
		$this->socket = $socket;
	}

	public function get($filters, $json_encode = true) {
		return $this->execute([ 'get' => $filters ], $json_encode);
	}

	public function getAll($type) {
		return $this->execute([
			'get' => [
				'type' => $type,
			]
		], true);
	}

	public function delete($type, $ids) {
		return $this->action($type, $ids, [
			'operation' => [
				'type'	=> 'delete',
			]
		]);
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
