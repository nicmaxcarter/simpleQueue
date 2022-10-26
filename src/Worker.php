<?php

namespace Nicmaxcarter\SimpleQueue;

use Doctrine\DBAL\Connection as DB;
use Psr\Container\ContainerInterface as Container;
use Nicmaxcarter\SimpleQueue\Base;

class Worker extends Base
{

    public function __construct(
        DB $db,
        Container $container
    )
    {
        parent::__construct($db);

        $this->internaldb = $container->get('localdb');
    }


    public function getStatus()
    {

        $query = $this->internaldb->createQueryBuilder();

        $query->select('available')
              ->from('identification');

        $result = $query->execute()->fetchAssociative()['available'];

        return intval($result);
    }

    public function isAvailable()
    {
        $status = $this->getStatus();

        return $status === 1;
    }

    public function isBusy()
    {
        $status = $this->getStatus();

        return $status === 0;
    }

    public function makeBusy(bool $external = false)
    {
        if($external)
            if(!$this->changeExternalStatus(false))
                return false;

        $query = $this->internaldb->createQueryBuilder();

        $query
            ->update('identification')
            ->set('available', '(:false)')
            ->setParameter('false', false);

        return $query->execute();
    }

    public function makeAvailable(bool $external = false)
    {
        if($external)
            if(!$this->changeExternalStatus(true))
                return false;

        $query = $this->internaldb->createQueryBuilder();

        $query
            ->update('identification')
            ->set('available', '(:true)')
            ->setParameter('true', true);

        return $query->execute();
    }

    public function changeExternalStatus(bool $available) {
        $workerId = $this->workerId();

        if(!$workerId)
            return false;

        $query = $this->db->createQueryBuilder();

        $query
            ->update('worker')
            ->set('available', '(:available)')
            ->where('id = (:id)')
            ->setParameter('available', intval($available))
            ->setParameter('id', $workerId);

        return $query->execute();
    }

    public function workerId() {
        // try and get id from internal sqlite
        $selfId = $this->getInternalId();

        // if the id has not been set yet
        if(is_null($selfId))
            return false;

        return intval($selfId);
    }

    public function getInternalId() {
        $query = $this->internaldb->createQueryBuilder();

        $query->select('selfId')
              ->from('identification');

        $result = $query->execute()->fetchAssociative()['selfId'];

        return $result;
    }

    public function getInternalUrl() {
        return 'maybe one day';
    }

    public function getExternalId(string $ip, string $url) {
        $query = $this->db->createQueryBuilder();

        $query
            ->select('id')
            ->from('worker')
            ->where('ip_address = (:ip) AND url = (:url)')
            ->setParameter('ip', $ip)
            ->setParameter('url', $url);

        return $query->execute()->fetchAssociative()['id'];
    }

    public function setInternalId(int $id) {
        $query = $this->internaldb->createQueryBuilder();

        $query
            ->update('identification')
            ->set('selfId', '(:id)')
            ->setParameter('id', $id);

        return $query->execute();
    }

    public function setInternalIp(string $ip) {
        $query = $this->internaldb->createQueryBuilder();

        $query
            ->update('identification')
            ->set('ip_address', '(:ip)')
            ->setParameter('ip', $ip);

        return $query->execute();
    }

    public function updateInternal(string $id, string $ip) {
        $query = $this->internaldb->createQueryBuilder();

        $query
            ->update('identification')
            ->set('selfId', '(:id)')
            ->set('ip_address', '(:ip)')
            ->setParameter('id', $id)
            ->setParameter('ip', $ip);

        return $query->execute();
    }

    public function add(string $ip, string $url)
    {
        $query = $this->db->createQueryBuilder();

        $query
            ->insert('worker')
            ->values(
                array(
                    'ip_address' => '(:ip)',
                    'url' => '(:url)'
                )
            )
            ->setParameter('ip', $ip)
            ->setParameter('url', $url);

        try {
            // try to insert value
            $query->execute();
        } catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            // if driver is already present
            return 0;
        }

        //return id of driver that was inserted
        return $this->db->lastInsertId();
    }
}
