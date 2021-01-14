<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\Message;
use Tustin\PlayStation\Model\MessageThread;
use Tustin\PlayStation\Iterator\MessagesIterator;

class MessagesFactory extends Api implements IteratorAggregate
{
    /**
     * @var MessageThread
     */
    private $thread;
    
    public function __construct(MessageThread $thread)
    {
        parent::__construct($thread->getHttpClient());

        $this->thread = $thread;
    }

    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        $iterator = new MessagesIterator($this->thread);

        return $iterator;
    }

    /**
     * Gets the first message in the message thread.
     *
     * @return Message
     */
    public function first() : Message
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