<?php

namespace Nicmaxcarter\SimpleQueue;

use Doctrine\DBAL\Connection as DB;
use Nicmaxcarter\SimpleQueue\Base;

class TaskQueue extends Base
{

    public function __construct(DB $db)
    {
        parent::__construct($db);
    }

    public function getTasks($maxResults = 100)
    {
        $query = $this->db->createQueryBuilder();

        $query
            ->select('*')
            ->from('task_queue')
            ->where('status = (:waiting)')
            ->addOrderBy('immediate', 'DESC')
            ->addOrderBy('created_at', 'ASC')
            ->setMaxResults($maxResults)
            ->setParameter('waiting', 'waiting');

        return $query->execute()->fetchAllAssociative();
    }

    public function getTask()
    {
        return $this->getTasks(1)[0];
    }

    public function taskInProgress($taskId)
    {
        return $this->changeStatus($taskId, 'progress', true);
    }

    public function completeTask($taskId)
    {
        return $this->changeStatus($taskId, 'complete', false, true);
    }

    public function failTask($taskId)
    {
        return $this->changeStatus($taskId, 'error', false, true);
    }

    public function changeStatus(
        int $taskId,
        string $status,
        bool $start = false,
        bool $end = false
    )
    {
        //'complete','progress','waiting','error')

        $query = $this->db->createQueryBuilder();
        $query
            ->update('task_queue', 't')
            ->set('t.status', '(:status)')
            ->where("t.id = (:taskId)")
            ->setParameter('taskId', $taskId)
            ->setParameter('status', $status);

        if($start) {
            $query->set('t.start_time', '(:now)')
                  ->setParameter('now', date('Y-m-d H:i:s'));
        }

        if($end) {
            $query->set('t.end_time', '(:now)')
                  ->setParameter('now', date('Y-m-d H:i:s'));
        }

        return $query->execute();
    }

    public function taskLog($taskId, string $log)
    {
        $query = $this->db->createQueryBuilder();
        $query
            ->update('task_queue', 't')
            ->set('t.log', '(:log)')
            ->where("t.id = (:taskId)")
            ->setParameter('taskId', $taskId)
            ->setParameter('log', $log);

        return $query->execute();
    }

    public function taskMessage($taskId, string $message)
    {
        $query = $this->db->createQueryBuilder();
        $query
            ->update('task_queue', 't')
            ->set('t.message', '(:message)')
            ->where("t.id = (:taskId)")
            ->setParameter('taskId', $taskId)
            ->setParameter('message', $message);

        return $query->execute();
    }

    public function parseArguments(string $argString) {
        $args = explode(',',$argString);
        return $args;
    }
}
