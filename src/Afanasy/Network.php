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

	private $verbose = true;

	public function __construct($address, $port) {
		$this->address = $address;
		$this->port = $port;
		$this->header_size = 2048;
		// $this->header_size = strlen('AFANASY ' . ' JSON') + 8;
		}

	public function deleteJob($username, $id) {
		$this->connect();
		$this->sendMessage([
			"action"	=>	[
				"ids"		=> [$id],
				"user_name"	=> $username,
				"host_name"	=> "dummy",
				"type"		=> "jobs",
				"mask"		=> ".*",
				"operation"	=> [
					"type"	=> "delete"
					]
				]
			]);

		$ret = $this->getResponse();
		$this->disconnect();
		return $ret;
		}

	public function getJobsByUser($user) {
		$this->connect();
		$this->sendMessage([
			"get"	=>	[
				"type"		=> "jobs",
				"user_name"	=> $username
				]
			]);

		$ret = $this->getResponse();
		$this->disconnect();
		return $ret;
		}

	public function getAllJobs() {
		$this->connect();
		$this->sendMessage([
			"get"	=>	[
				"type"	=> "jobs"
				]
			]);

		$ret = $this->getResponse();
		$this->disconnect();
		return $ret;
		}

	public function sendJob($job) {
		$this->connect();
		$json = $job->getJSON();
		$this->sendMessage($json, false);
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