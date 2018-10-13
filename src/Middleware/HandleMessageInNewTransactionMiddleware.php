<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\EnvelopeAwareInterface;
use Symfony\Component\Messenger\Exception\MessageHandlingException;
use Symfony\Component\Messenger\Middleware\Configuration\Transaction;

/**
 * Allow to configure messages to be handled in a new Doctrine transaction if using
 * the DoctrineTransactionMiddleware. This middleware should be used before DoctrineTransactionMiddleware.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class HandleMessageInNewTransactionMiddleware implements MiddlewareInterface, EnvelopeAwareInterface
{
    /**
     * @var array A queue of messages and callables
     */
    private $queue = array();

    /**
     * @var bool Indicate if we are running the middleware or not. Ie, are we called inside a message handler?
     */
    private $insideMessageHandler = false;

    /**
     * @param Envelope $envelope
     */
    public function handle($envelope, callable $next)
    {
        if (null !== $envelope->get(Transaction::class)) {
            if (!$this->insideMessageHandler) {
                throw new \LogicException('We have to use the transaction in the context of a message handler');
            }
            $this->queue[] = ['envelope'=>$envelope, 'callable'=>$next];

            return;
        }

        if ($this->insideMessageHandler) {
            /*
             * If come inside a second message handler, just continue as normal. We should not
             * run the stored messages.
             */
            return $next($envelope);
        }

        $this->insideMessageHandler = true;
        try {
            $returnData = $next($envelope);
        } catch (\Throwable $exception) {
            $this->queue = [];
            $this->insideMessageHandler = false;

            throw $exception;
        }

        $exceptions = array();
        while (!empty($queueItem = array_pop($this->queue))) {
            try {
                // Execute the stored messages
                $queueItem['callable']($queueItem['envelope']);
            } catch (\Throwable $exception) {
                $exceptions[] = $exception;
            }
        }

        // Assert: $this->queue is empty.
        $this->insideMessageHandler = false;
        if (!empty($exceptions)) {
            if (1 === \count($exceptions)) {
                throw $exceptions[0];
            }
            throw new MessageHandlingException($exceptions);
        }

        return $returnData;
    }
}
