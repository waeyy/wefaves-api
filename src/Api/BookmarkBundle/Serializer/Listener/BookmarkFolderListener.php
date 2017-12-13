<?php

namespace Api\BookmarkBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

class BookmarkFolderListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => Events::POST_SERIALIZE,
                'format' => 'json',
                'class' => 'Api\BookmarkBundle\Entity\BookmarkFolder',
                'method' => 'onPostSerialize',
            ],
        ];
    }

    public static function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();

        if ($object->getBookmarkFolderParent() != null)
            $parentId = $object->getBookmarkFolderParent()->getItemId();
        else
            $parentId = 0;

        if ($object->getTitle() == null)
            $event->getVisitor()->setData('title', "");
        
        $event->getVisitor()->setData('parent_id', $parentId);
    }
}