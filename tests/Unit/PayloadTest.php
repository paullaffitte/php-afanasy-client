<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Afanasy\Network;
use Afanasy\Socket;
use Afanasy\Job;
use Afanasy\Block;
use Afanasy\Task;
use Afanasy\States;

// https://stackoverflow.com/a/4356295/5677103
function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function randomIds() {
	$ids = [];
	for ($i = 0; $i < 5; $i++) {
		$ids[] = rand();
	}
	return $ids;
}

function actionPayload($data) {
	[ $userName, $hostName ] = explode('@', getenv('AF_USER'));
	return [
		'action' => array_merge([
			'user_name' => $userName,
			'host_name' => $hostName,
		], $data),
	];
}

class PayloadTest extends TestCase {

	private $network;
	private $sendMock;

	public function setUp() {
		Dotenv::createImmutable(__DIR__.'/..')->load();

		$socketMock = $this->getMockBuilder(Socket::class)
			->disableOriginalConstructor()
			->setMethods(['send', 'connect'])
			->getMock();

		$this->sendMock = $socketMock->expects($this->once())->method('send');

		$this->network = new Network(getenv('AF_SERVER'), getenv('AF_SERVERPORT'), getenv('AF_USER'));
		$this->network->setSocket($socketMock);
	}

	public function testDeleteJob() {
		$id = rand();
		$this->sendMock->with(actionPayload([
			'type' => 'jobs',
			'ids' => [$id],
			'operation' => [
				'type' => 'delete',
			],
		]));
		$this->network->deleteJob($id);
	}

	public function testDeleteJobs() {
		$ids = randomIds();
		$this->sendMock->with(actionPayload([
			'type' => 'jobs',
			'ids' => $ids,
			'operation' => [
				'type' => 'delete',
			],
		]));
		$this->network->deleteJobs($ids);
	}

	public function testGetJobsByUser() {
		$username = generateRandomString();
		$this->sendMock->with([
			'get' => [
				'type' => 'jobs',
				'user_name' => $username,
			]
		]);
		$this->network->getJobsByUser($username);
	}

	public function testGetJobById() {
		$id = rand();
		$this->sendMock->with([
			'get' => [
				'type' => 'jobs',
				'ids' => [$id],
			]
		])->will($this->onConsecutiveCalls([
			'jobs' => ['fakejob'],
		]));
		$this->network->getJobById($id);
	}

	public function testGetJobByIds() {
		$ids = randomIds();
		$this->sendMock->with([
			'get' => [
				'type' => 'jobs',
				'ids' => $ids,
			]
		]);
		$this->network->getJobByIds($ids);
	}

	public function testGetAllJobs() {
		$this->sendMock->with([
			'get' => [
				'type' => 'jobs',
			],
		]);
		$this->network->getAllJobs();
	}

	public function testSendJob() {
		$job = new Job();
		$block = new Block();
		$task = new Task();
		$job->addBlock($block);
		$block->addTask($task);
		$this->sendMock->with($job->getJSON(), false)
			->will($this->onConsecutiveCalls([]));
		$this->network->sendJob($job);
	}

	public function testRestartErrors() {
		$ids = randomIds();
		$this->sendMock->with(actionPayload([
			'type' => 'jobs',
			'ids' => $ids,
			'operation' => [
				'type' => 'restart_errors',
			],
		]));
		$this->network->restartErrors($ids);
	}

	public function testRestartErrorHosts() {
		$ids = randomIds();
		$this->sendMock->with(actionPayload([
			'type' => 'jobs',
			'ids' => $ids,
			'operation' => [
				'type' => 'reset_error_hosts',
			],
		]));
		$this->network->restartErrorHosts($ids);
	}

	public function testPauseJobs() {
		$ids = randomIds();
		$this->sendMock->with(actionPayload([
			'type' => 'jobs',
			'ids' => $ids,
			'operation' => [
				'type' => 'pause',
			],
		]));
		$this->network->pauseJobs($ids);
	}

	public function testGetRenders() {
		$ids = randomIds();
		$this->sendMock->with([
			'get' => [
				'type' => 'renders',
			]
		]);
		$this->network->getRenders();
	}

	public function testDeleteRenders() {
		$ids = randomIds();
		$this->sendMock->with(actionPayload([
			'type' => 'renders',
			'ids' => $ids,
			'operation' => [
				'type' => 'delete',
			],
		]));
		$this->network->deleteRenders($ids);
	}

	public function testUpdateRendersCapacity() {
		$ids = randomIds();
		$capacity = rand();
		$this->sendMock->with(actionPayload([
			'type' => 'renders',
			'ids' => $ids,
			'params' => [
				'capacity' => $capacity,
			],
		]));
		$this->network->updateRendersCapacity($ids, $capacity);
	}
}
