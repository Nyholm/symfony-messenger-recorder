<?php

namespace Symfony\Bridge\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\Recorder\RecordedMessageCollectionInterface;

/**
 * Doctrine listener that listens to Persist, Update and Remove. Every time this is
 * invoked we take messages from the entities.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Matthias Noback <matthiasnoback@gmail.com>
 */
class MessengerMessageCollector implements EventSubscriber, RecordedMessageCollectionInterface
{
    private $collectedMessage = array();

    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        );
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function getRecordedMessages(): array
    {
        return $this->collectedMessage;
    }

    public function resetRecordedMessages(): void
    {
        $this->collectedMessage = array();
    }

    private function collectEventsFromEntity(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof RecordedMessageCollectionInterface) {
            foreach ($entity->getRecordedMessages() as $event) {
                $this->collectedMessage[] = $event;
            }

            $entity->resetRecordedMessages();
        }
    }
}
