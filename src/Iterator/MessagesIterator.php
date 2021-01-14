<?php
namespace Tustin\PlayStation\Iterator;

use InvalidArgumentException;
use Tustin\PlayStation\Model\Message;
use Tustin\PlayStation\Model\MessageThread;

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

    public function __construct(MessageThread $thread, int $limit = 20)
    {
        if ($limit <= 0)
        {
            throw new InvalidArgumentException('$limit must be greater than zero.');
        }

        parent::__construct($thread->getHttpClient());
        $this->thread = $thread;
        $this->limit = $limit;
        $this->access(null);
    }

    public function access($cursor) : void
    {
		$params = [];

		if ($cursor != null)
        {
            if (!is_string($cursor))
            {
                throw new InvalidArgumentException("$cursor must be a string.");
            }
       
            $params['before'] = $cursor;
        }
		
		$results = $this->get('gamingLoungeGroups/v1/members/me/groups/' . $this->thread->group()->id() . '/threads/' . $this->thread->id() . '/messages', $params);

		$this->force(!$results->reachedEndOfPage);
        $this->update($results->messageCount, $results->messages, $results->previous);
    }

    public function next() : void
    {
        $this->currentOffset++;
        if (($this->currentOffset % $this->limit) == 0)
        {
            $this->access($this->maxEventIndexCursor);
        }
    }

    public function current()
    {
        return new Message(
            $this->thread,
            $this->getFromOffset($this->currentOffset)
        );
    }
}