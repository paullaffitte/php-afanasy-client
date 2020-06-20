# Introduction

This is an implementation of a client for the Afanasy server. It's based on the [python client](https://github.com/CGRU/cgru/blob/master/afanasy/python), but even if it's goal is to provide the same features, some of them are missing other are specific to this package and even if the overall architecture looks similar, there is a few differences. So don't expect this to provide a 1:1 reproduction of the python client.

Here is the API's [official documentation](https://cgru.info/afanasy/api).

# How to

First, you need to install the library, you can use composer: `composer require abuisine/php-afanasy-client`.

Then, the usage is quite straightforward. Create a Network object with your server's adress and port.

```php
use Afanasy\Network;

$afnetwork = new Network('localhost', 51000);
```

You will then be able to interact with the server from this object. Here are a few examples.

```php
use Afanasy\Job;
use Afanasy\Block;
use Afanasy\Task;

// Get all jobs
$jobs = $afnetwork->getAllJobs();

// Pause jobs
$jobIds = array_column($jobs['jobs'], 'job_id');
$afnetwork->pauseJobs($jobIds);

// Send a new Job
$job = new Job("Foo");
$block = new Block("Bar");
$task = new Task("FooBar");
$job->addBlock($block);
$block->addTask($task);
$task->setCommand('echo "Hello, World!"');

$afnetwork->sendJob($job);
```
