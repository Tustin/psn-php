<?php

namespace Tustin\PlayStation\Iterator;

use InvalidArgumentException;
use Tustin\PlayStation\Model\MessageThread;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class MessagesIterator extends AbstractApiIterator
{
    private int $totalCount = 0;

    public function __construct(private MessageThread $thread, private int $limit = 20)
    {
        if ($limit <= 0) {
            throw new InvalidArgumentException('$limit must be greater than zero.');
        }

        parent::__construct($thread->getHttpClient());
        $this->access(null);
    }

    /**
     * Accesses the API to get the next set of messages.
     */
    public function access(mixed $cursor): void
    {
        $params = [];

        if ($cursor != null) {
            if (!is_string($cursor)) {
                throw new InvalidArgumentException("$cursor must be a string.");
            }

            $params['before'] = $cursor;
        }

        $results = $this->get('gamingLoungeGroups/v1/members/me/groups/' . $this->thread->group()->id() . '/threads/' . $this->thread->id() . '/messages', $params);

        $this->totalCount += $results->messageCount;
        // if ($results->reachedEndOfPage && $results->messageCount == 0) {
        //     return;
        // }

        // $this->force(!$results->reachedEndOfPage);
        $this->update($this->totalCount, $results->messages, $results->previous);
    }

    /**
     * Moves the iterator to the next message.
     */
    public function next(): void
    {
        $this->currentOffset++;

        // Since totalResults for the messages API just returns the amount of messages sent in the response, we have to do it like this.
        if ($this->currentOffset == $this->totalResults) {
            $this->access($this->customCursor);
        }
    }

    /**
     * Gets the current message in the iterator.
     * 
     * Will automatically convert the message to a specific type of message.
     */
    public function current(): AbstractMessage
    {
        return AbstractMessage::create(
            $this->thread,
            $this->getFromOffset($this->currentOffset)
        );
    }
}
