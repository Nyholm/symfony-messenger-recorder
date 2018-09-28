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
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\Configuration\Transaction;
use Symfony\Component\Messenger\Recorder\RecordedMessageCollectionInterface;

/**
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class HandleRecordedMessageMiddleware implements MiddlewareInterface, EnvelopeAwareInterface
{
    /**
     * @var array A queue of messages and callables
     */
    private $queue = array();

    /**
     * @var bool Indicate if we are running the middleware or not. Ie, are we called inside a message handler?
     */
    private $running = false;

    /**
     * @param Envelope $envelope
     */
    public function handle($envelope, callable $next)
    {
        if (null !== $envelope->get(Transaction::class)) {
            if (!$this->running) {
                throw new \LogicException('We have to use the transaction in the context of an event handler');
            }
            $this->queue[] = ['message'=>$envelope, 'callable'=>$next];

            return;
        }

        try {
            $returnData = $next($envelope);
        } catch (\Throwable $exception) {
            $this->queue = [];

            throw $exception;
        }

        $exceptions = array();
        while (!empty($queueItem = array_pop($this->queue))) {
            foreach ($queueItem as [$message, $callable]) {
                try {
                    $callable($message);
                } catch (\Throwable $exception) {
                    $exceptions[] = $exception;
                }
            }
        }

        if (!empty($exceptions)) {
            if (1 === \count($exceptions)) {
                throw $exceptions[0];
            }
            throw new MessageHandlingException($exceptions);
        }

        return $returnData;
    }
}
