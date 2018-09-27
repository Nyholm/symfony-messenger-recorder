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

use Symfony\Contracts\Service\ResetInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Matthias Noback <matthiasnoback@gmail.com>
 */
class MessageRecorder implements MessageRecorderInterface, RecordedMessageCollectionInterface, ResetInterface
{
    use MessageRecorderTrait {
        record as public record;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->resetRecordedMessages();
    }
}
