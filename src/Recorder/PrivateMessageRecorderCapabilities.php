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
 * Use this trait in classes which implement RecordedMessageCollectionInterface
 * to privately record and later release Message instances, like events.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Matthias Noback <matthiasnoback@gmail.com>
 */
trait PrivateMessageRecorderCapabilities
{
    private $messages = [];

    /**
     * {@inheritdoc}
     */
    public function getRecordedMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function resetRecordedMessages()
    {
        $this->messages = [];
    }

    /**
     * Record a message.
     *
     * @param object $message
     */
    protected function record($message)
    {
        $this->messages[] = $message;
    }
}
