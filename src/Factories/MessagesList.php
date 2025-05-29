<?php

namespace Tustin\PlayStation\Factories;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Models\MessageThread;
use Tustin\PlayStation\Models\Messages\Message;
use Tustin\PlayStation\Iterators\MessagesIterator;
use Tustin\PlayStation\Iterators\Filter\MessageTypeFilter;
use Tustin\PlayStation\Models\Message\Message as MessageMessage;

class MessagesList extends Api implements IteratorAggregate
{
    private ?string $typeFilter = null;

    public function __construct(private MessageThread $messageThread) {}

    /**
     * Gets messages only of a certain type.
     */
    public function of(string $class): MessagesList
    {
        $this->typeFilter = $class;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): Iterator
    {
        $iterator = new MessagesIterator($this->messageThread);

        if ($this->typeFilter && class_exists($this->typeFilter) !== false) {
            $iterator = new MessageTypeFilter($iterator, $this->typeFilter);
        }

        return $iterator;
    }

    /**
     * Gets the first message in the message thread.
     */
    public function first(): Message
    {
        return $this->getIterator()->current();
    }
}
