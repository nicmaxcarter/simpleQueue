<?php

namespace Nicmaxcarter\SimpleQueue\Controller;

use Nicmaxcarter\SimpleQueue\Entity\Worker;
use Nicmaxcarter\SimpleQueue\Entity\TaskQueue;
use Nicmaxcarter\ApiTool\Api;

abstract class WorkerTaskController
{
    protected $worker;
    protected $queue;
    protected $taskId;

    // the constructor is final because we want to ensure
    // that the worker ALWAYS marks itself as busy before
    // any work is done
    public function __construct(
        Worker $worker,
        TaskQueue $queue
    )
    {
        $this->worker = $worker;
        $this->queue = $queue;

        $this->worker->makeBusy(true);

        $this->taskId = Api::getData()->taskId;
    }

    // the destructor is final because we want to ensure
    // that the worker ALWAYS marks itself as available
    // when completing a task
    final function __destruct(){
        $this->worker->makeAvailable(true);
    }

}
