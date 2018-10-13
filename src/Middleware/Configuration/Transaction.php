<?php

declare(strict_types=1);

namespace Symfony\Component\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\EnvelopeItemInterface;

/**
 * Marker item to tell this message should be handled in a different Doctrine transaction.
 * This should be used together with HandleMessageInNewTransactionMiddleware and DoctrineTransactionMiddleware.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Transaction implements EnvelopeItemInterface
{
}