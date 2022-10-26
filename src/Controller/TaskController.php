<?php

namespace Nicmaxcarter\SimpleQueue\Controller;

use Psr\Container\ContainerInterface as Container;
use Nicmaxcarter\SimpleQueue\Entity\Task;
use Nicmaxcarter\ApiTool\Api;
use GuzzleHttp\Client as Guzzle;

abstract class TaskController
{
        protected $task;
    protected $workerurl;
    protected $taskId;
    protected $workerId;

    public function __construct(
        Task $task,
        Container $container,
        $secretNumber
    )
    {
        $this->task = $task;

        // this is that old shit
        // we don't do this anymore
        //$this->workerurl = $container->get('workerurl');

        $this->setWorkerData();

        $this->secretNumber = $secretNumber;
    }

    abstract public function createTask(
        int $companyId,
        int $actionId,
        string $name,
        string $args
    ) : int;
    //abstract public function sendTask($addData);

    public function workerNotSet(){
        return (is_null($this->workerId) || !$this->workerurl);
    }

    public function returnNoWorker($response) {
        return Api::respond400($response, [
            'result' => [
                'error' => 1,
                'errorMessage' => 'no servers available',
                'taskId' => null
            ]
        ]);
    }

    public function setWorkerData() {
        $worker = $this->task->findAvailableWorkerData();

        if(!$worker){
            $this->workerId = null;
            $this->workerurl = null;
            return false;
        }

        $this->workerId = $worker['id'];
        $this->workerurl = $worker['url'];

        return true;
    }

    public function sendTask(
        string $url,
        $addData = null,
        $timeout = 2
    ) {
        $client = new Guzzle([
            'base_uri' => $this->workerurl,
            'verify' => false
        ]);

        $req = $client->requestAsync(
            'POST',
            $url,
            [
                'body' => Api::AuthPostData(
                    $this->secretNumber,
                    $addData
                ),
                'timeout' => $timeout
            ]
        );

        try {
            $req->wait();
        } catch (\Exception $ex) {
            //$this->dump($ex->getMessage());
            ## Handle
        }
    }

    public function sendTest(
        string $url,
        $addData = null,
        $timeout = 60
    ) {
        $client = new Guzzle([
            'base_uri' => $this->workerurl,
            'verify' => false
        ]);

        try {
            $resp = $client->request(
                'POST',
                $url,
                [
                    'body' => Api::AuthPostData(
                        $this->secretNumber,
                        $addData
                    ),
                    'timeout' => $timeout
                ]
            );

        } catch (\Exception $ex) {
            //$this->dump($ex->getMessage());
            return $ex->getMessage();
            ## Handle
        }

        return $resp->getBody()->getContents();
    }
}
