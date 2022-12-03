<?php

namespace Tustin\PlayStation\Iterator;

use InvalidArgumentException;
use Tustin\PlayStation\Model\Message;
use Tustin\PlayStation\Model\MessageThread;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class MessagesIterator extends AbstractApiIterator
{
    /**
     * @var MessageThread
     */
    protected $thread;

    /**
     * @var int
     */
    protected $limit;

    private $totalCount = 0;

    public function __construct(MessageThread $thread, int $limit = 20)
    {
        if ($limit <= 0) {
            throw new InvalidArgumentException('$limit must be greater than zero.');
        }

        parent::__construct($thread->getHttpClient());
        $this->thread = $thread;
        $this->limit = $limit;
        $this->access(null);
    }

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

    public function next(): void
    {
        $this->currentOffset++;

        // Since totalResults for the messages API just returns the amount of messages sent in the response, we have to do it like this.
        if ($this->currentOffset == $this->totalResults) {
            $this->access($this->customCursor);
        }
    }

    public function current(): AbstractMessage
    {
        return AbstractMessage::create(
            $this->thread,
            $this->getFromOffset($this->currentOffset)
        );
    }
}
