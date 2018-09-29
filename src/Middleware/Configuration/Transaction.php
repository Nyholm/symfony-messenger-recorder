<?php

declare(strict_types=1);

namespace Symfony\Component\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\EnvelopeItemInterface;

/**
 * Marker item to tell this message should be handled in a different transaction.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Transaction implements EnvelopeItemInterface
{
    public function serialize()
    {
        return '';
    }

    public function unserialize($serialized)
    {
        // noop
    }
}