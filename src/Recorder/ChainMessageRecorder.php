<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger\Recorder;


/**
 * Aggregates multiple recorders.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Matthias Noback <matthiasnoback@gmail.com>
 */
class ChainMessageRecorder implements RecordedMessageCollectionInterface
{
    /**
     * @var RecordedMessageCollectionInterface[]
     */
    private $messageRecorders;

    public function __construct(iterable $messageRecorders)
    {
        foreach ($messageRecorders as $messageRecorder) {
            $this->addMessageRecorder($messageRecorder);
        }
    }

    /**
     * Get messages recorded by all known message recorders.
     *
     * {@inheritdoc}
     */
    public function getRecordedMessages(): array
    {
        $allRecordedMessages = [];

        foreach ($this->messageRecorders as $messageRecorder) {
            $allRecordedMessages = array_merge($allRecordedMessages, $messageRecorder->getRecordedMessages());
        }

        return $allRecordedMessages;
    }

    /**
     * Erase messages recorded by all known message recorders.
     *
     * {@inheritdoc}
     */
    public function resetRecordedMessages(): void
    {
        foreach ($this->messageRecorders as $messageRecorder) {
            $messageRecorder->resetRecordedMessages();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->resetRecordedMessages();
    }

    private function addMessageRecorder(RecordedMessageCollectionInterface $messageRecorder): void
    {
        $this->messageRecorders[] = $messageRecorder;
    }
}
