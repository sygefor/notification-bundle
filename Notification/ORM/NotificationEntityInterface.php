<?php

namespace NotificationBundle\Notification\ORM;

/**
 * Interface NotificationEntityInterface.
 */
interface NotificationEntityInterface
{
    public function setMessage($message);

    public function setPlainMessage($message);

    public function setLevel($level);

    public function setLevelName($name);

    public function setDatetime($datetime);

    public function setDescription($description);

    public function setRoute($route);

    public function setRouteParams($params);
}
