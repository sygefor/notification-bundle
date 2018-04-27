<?php

namespace NotificationBundle\Notification\Handler;

use Doctrine\ORM\EntityManager;
use Monolog\Handler\AbstractProcessingHandler;
use NotificationBundle\Notification\NotificationTypeInterface;

/**
 * Class DoctrineORMHandler.
 */
class DoctrineORMHandler extends AbstractProcessingHandler
{
    /** @var EntityManager */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    protected function write(array $record)
    {
        $context = $record['context'];
        if (!isset($context['notification'])) {
            return false;
        }

        /** @var NotificationTypeInterface $notification */
        $notification = $context['notification'];
        $extra = $record['extra'];

        // get entities
        $entities = $notification->getEntity();
        if (!array($entities)) {
            $entities = [$entities];
        }
        if ($entities) {
            foreach ($entities as $entity) {
                $entity->setMessage($record['message']);
                $entity->setPlainMessage($extra['plain_message']);
                $entity->setLevel($record['level']);
                $entity->setLevelName($record['level_name']);
                $entity->setDatetime($record['datetime']);

                if (isset($extra['description'])) {
                    $entity->setDescription($extra['description']);
                }

                if (isset($extra['route'])) {
                    $entity->setRoute($extra['route']);
                    $entity->setRouteParams($extra['route_parameters']);
                }
                $this->em->persist($entity);
            }
            $this->em->flush();
        }
    }
}
