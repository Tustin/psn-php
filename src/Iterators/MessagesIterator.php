<?php

namespace Tustin\PlayStation\Iterators;

use InvalidArgumentException;
use Tustin\PlayStation\Models\MessageThread;
use Tustin\PlayStation\Models\Messages\Message;

class MessagesIterator extends AbstractApiIterator
{
    private int $totalCount = 0;

    public function __construct(private MessageThread $messageThread, protected ?int $limit = 20)
    {
        if ($limit <= 0) {
            throw new InvalidArgumentException('$limit must be greater than zero.');
        }

        parent::__construct();
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

        $results = $this->get('gamingLoungeGroups/v1/members/me/groups/' . $this->messageThread->group()->id() . '/threads/' . $this->messageThread->id() . '/messages', $params);

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
    public function current(): Message
    {
        return Message::create(
            $this->messageThread,
            $this->getFromOffset($this->currentOffset)
        );
    }
}
