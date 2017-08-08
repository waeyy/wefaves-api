<?php

namespace Api\UserBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

class UserListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => Events::POST_SERIALIZE,
                'format' => 'json',
                'class' => 'Api\UserBundle\Entity\User',
                'method' => 'onPostSerialize',
            ]
        ];
    }

    public static function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();

        $is_admin = false;
        $roles = $object->getRoles();

        if (in_array("--SUPER", $roles) ||  in_array("ROLE_ADMIN", $roles))
            $is_admin = true;

        $event->getVisitor()->addData('is_admin', $is_admin);
    }
}