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

	public function tearDown() {
	}

	public function testJobCount() {
	    $this->sendMock->with([
	    	'get' => [
	    		'type' => 'jobs',
    		]
    	]);
		$this->network->getAllJobs();
	}
}
