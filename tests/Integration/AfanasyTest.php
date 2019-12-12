<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Afanasy\Network;
use Afanasy\Job;
use Afanasy\Block;
use Afanasy\Task;
use Afanasy\States;

class AfanasyTest extends TestCase {

	private $network;
	private $jobCount;

	public function setUp() {
		Dotenv::createImmutable(__DIR__)->load();
		$this->network = new Network(getenv('AF_SERVER'), getenv('AF_SERVERPORT'), getenv('AF_USER'));
		$this->jobCount = 1;
		}

	public function tearDown() {
		$jobs = $this->network->getAllJobs()['jobs'];
		$this->network->deleteJobs(array_column($jobs, 'id'));
		}

	public function testJobCount() {
		$jobs = $this->network->getAllJobs()['jobs'];
		$jobCount = count($jobs);
		$this->assertEquals($this->jobCount, $jobCount); // There is always at least 1 job running (the afadmin job)
		}

	public function testSendAndDeleteJobs() {
		$job = new Job("testSendAndDeleteJobs");
		$block = new Block();
		$task = new Task();

		$job->addBlock($block);
		$block->addTask($task);

		while ( $this->jobCount < 4 ) {
			$response = $this->network->sendJob($job);
			++$this->jobCount;

			$this->assertIsArray($response);
			$this->assertArrayHasKey('id', $response);
			$this->assertEquals($this->jobCount, $response['id']);
			}

		$this->testJobCount();

		while ( $this->jobCount > 1 ) {
			$response = $this->network->deleteJob($this->jobCount);
			--$this->jobCount;

			$this->assertIsArray($response);
			$this->assertEquals(1, count($response));
			}

		$this->testJobCount();
		}

	public function testGetJobById() {
		$job = new Job("testGetJobById");
		$block = new Block();
		$task = new Task();

		$job->addBlock($block);
		$block->addTask($task);

		$response = $this->network->sendJob($job);

		$job = $this->network->getJobById($response['id']);

		$this->assertArrayHasKey('blocks', $job);
		}

	public function testRestartErrors() {
		$job = new Job("testRestartErrors");
		$block = new Block("");
		$task = new Task("");

		$job->addBlock($block);
		$block->addTask($task);
		$task->setCommand('eco ok');

		$response = $this->network->sendJob($job);
		for ($i = 0; $i < 10; $i++) {
			$blockResponse = $this->network->getJobById($response['id'])['blocks'][0];
			if ( array_key_exists('p_error_hosts', $blockResponse) && $blockResponse['p_error_hosts'] > 0 )
				break;
			sleep(1);
			}
		$this->assertArrayHasKey('p_error_hosts', $blockResponse);
		$this->assertGreaterThan(0, $blockResponse['p_error_hosts']);

		$this->network->restartErrorHosts([$response['id']]);
		for ($i = 0; $i < 10; $i++) {
			$jobResponse = $this->network->getJobById($response['id']);
			if ( States::arrayHasState($jobResponse, States::ERROR) )
				break;
			sleep(1);
			}
		$this->assertTrue(States::arrayHasState($jobResponse, States::ERROR));

		$this->network->restartErrors([$response['id']]);
		$jobResponse = $this->network->getJobById($response['id']);
		$this->assertTrue(States::arrayHasState($jobResponse, States::READY));
		$this->assertFalse(States::arrayHasState($jobResponse, States::ERROR));
		}
	}