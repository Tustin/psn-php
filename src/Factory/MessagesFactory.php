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
    /**
     * @var MessageThread
     */
    private $thread;

    private $typeFilter;

    public function __construct(MessageThread $thread)
    {
        parent::__construct($thread->getHttpClient());

        $this->thread = $thread;
    }

    public function of(string $class)
    {
        $this->typeFilter = $class;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
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
     *
     * @return Message
     */
    public function first(): AbstractMessage
    {
        return $this->getIterator()->current();
    }

    /**
     * Creates and sends a new message in the message thread.
     *
     * @param AbstractMessage $message
     * @return Message
     */
    // public function create(AbstractMessage $message) : Message
    // {
    //     return $this->thread->sendMessage($message);
    // }
}
