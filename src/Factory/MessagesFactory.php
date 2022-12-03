<?php

namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\Message;
use Tustin\PlayStation\Model\MessageThread;
use Tustin\PlayStation\Iterator\MessagesIterator;
use Tustin\PlayStation\Model\Message\AbstractMessage;
use Tustin\PlayStation\Iterator\Filter\MessageTypeFilter;

class MessagesFactory extends Api implements IteratorAggregate
{
    private string $typeFilter;

    public function __construct(private MessageThread $thread)
    {
        parent::__construct($thread->getHttpClient());
    }

    /**
     * Gets messages only of a certain type.
     */
    public function of(string $class): MessagesFactory
    {
        $this->typeFilter = $class;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): Iterator
    {
        $iterator = new MessagesIterator($this->thread);

        if ($this->typeFilter && class_exists($this->typeFilter) !== false) {
            $iterator = new MessageTypeFilter($iterator, $this->typeFilter);
        }

        return $iterator;
    }

    /**
     * Gets the first message in the message thread.
     */
    public function first(): AbstractMessage
    {
        return $this->getIterator()->current();
    }
}
