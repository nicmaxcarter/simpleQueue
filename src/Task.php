<?php

namespace Nicmaxcarter\SimpleQueue;

use Doctrine\DBAL\Connection as DB;
use Nicmaxcarter\SimpleQueue\Base;

class Task extends Base
{
    public function __construct(DB $db)
    {
        parent::__construct($db);
    }

    public function findAvailableWorker() {
        $query = $this->db->createQueryBuilder();

        $query
            ->select('id')
            ->from('worker')
            ->where('available = (:available)')
            ->setParameter('available', 1);

        return $query->execute()->fetchAssociative()['id'];
    }

    public function findAvailableWorkerData() {
        $query = $this->db->createQueryBuilder();

        $query
            ->select('id, ip_address, url')
            ->from('worker')
            ->where('available = (:available)')
            ->setParameter('available', 1);

        return $query->execute()->fetchAssociative();
    }

    public function createTask(
        $name,
        $actionId,
        $companyId,
        $args = null,
        bool $immediate = false
    )
    {
        $query = $this->query();

        $values = [
            'name' => '(:name)',
            'action' => '(:actionId)',
            'company' => '(:companyId)',
            'arguments' => '(:args)',
        ];

        if($immediate)
            $values['immediate'] = '(:immediate)';

        $query
            ->insert('task_queue')
            ->values($values)
            ->setParameter('name', $name)
            ->setParameter('actionId', $actionId)
            ->setParameter('companyId', $companyId)
            ->setParameter('args', $args)
            ->setParameter('immediate', 1);

        $query->execute();

        return $this->db->lastInsertId();
    }

    public function status(int $taskId) {
        $query = $this->db->createQueryBuilder();

        $query
            ->select('status, message, log')
            ->from('task_queue')
            ->where('id = (:taskId)')
            ->setParameter('taskId', $taskId);

        return $query->execute()->fetchAssociative();
    }
}
