<?php

namespace Afanasy;

use Exception;

class Network {

	const TIMEOUT = 5;

	private $socket = false;
	private $connected = false;
	private $header_size;
	private $address;
	private $port;
	private $userName;
	private $hostName;

	private $verbose = true;

	public function __construct($address, $port, $user='coord@pc') {
		$this->address = $address;
		$this->port = $port;
		$this->header_size = 2048;
		[ $this->userName, $this->hostName ] = explode('@', $user);
		}

	public function deleteJob($id) {
		return $this->action('jobs', [$id], [
			"type" => "delete",
			]);
		}

	public function getJobsByUser($user) {
		return $this->get([
			"type"		=> "jobs",
			"user_name"	=> $user
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

	public function get($filters, $json_encode = true) {
		return $this->execute([ 'get' => $filters ], $json_encode);
		}

	public function action($type, $ids, $operation, $json_encode = true) {
		return $this->execute([
			'action' => [
				'user_name' => $this->userName,
				'host_name' => $this->hostName,
				'type' => $type,
				'ids' => $ids,
				'operation' => $operation
				]
			], $json_encode);
		}

	protected function execute($message, $json_encode = true) {
		$this->connect();
		$this->sendMessage($message, $json_encode);

		$ret = $this->getResponse();
		$this->disconnect();
		return $ret;
		}

	private function connect() {
		// echo "Connecting to '$this->address' on port '$this->port'...\n";
		if ($this->connected)
			throw new Exception("Already connected");
		if ($this->socket)
			throw new Exception("Socket already initiated");
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->socket === false)
		    throw new Exception("Not able to create socket: " . socket_strerror(socket_last_error()));
		$result = socket_connect($this->socket, $this->address, $this->port);
		if ($result === false)
			throw new Exception("Not able to connect: " . socket_strerror(socket_last_error($this->socket)));
		socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => Network::TIMEOUT, 'usec' => 0]);
		$this->connected = true;
		}

	private function disconnect() {
		if ( !$this->socket )
			return;
		socket_shutdown($this->socket);
		socket_close($this->socket);
		$this->socket = false;
		$this->connected = false;
		}

	private function sendMessage($message, $json_encode = true) {
		if ( $json_encode )
			$json = json_encode($message);
		else
			$json = $message;

		if (! $this->connected)
			throw new Exception("Not connected");
		// echo "Sending json ...";
		$header = "AFANASY " . strlen($json) . " JSON";

		$in = $header . $json;
		socket_write($this->socket, $in, strlen($in));
		// echo " OK\n";
		}

	private function getResponse() {
		if (! $this->connected)
    		throw new Exception("Not connected");
		flush();
		$response = "";
		$header = socket_read($this->socket, strlen("AFANASY "));
		if ( empty($header) )
			throw new Exception("server not responding");

		$header = socket_read($this->socket, strlen("X JSON"));
		if ( empty($header) )
			throw new Exception("server not responding");

		do {
			switch( ($pos = strpos($header, 'J')) ) {
				case strlen($header) - 4:
					break;
				case strlen($header) - 3:
					$header .= socket_read($this->socket, 1);
					break;
				case strlen($header) - 2:
					$header .= socket_read($this->socket, 2);
					break;
				case strlen($header) - 1:
					$header .= socket_read($this->socket, 3);
					break;
				case false:
					$header .= socket_read($this->socket, 4);
					break;
				default:
					throw new Exception("wrong parsing result");
				}
			}
		while( $pos === false );

		$json_size = 0;
		sscanf($header, "%d JSON", $json_size);
		while( $json_size ) {
			$data = socket_read($this->socket, $json_size);
			$json_size -= strlen($data);
    		$response .= $data;
			}
		return json_decode($response, true);
		}
	}

?>