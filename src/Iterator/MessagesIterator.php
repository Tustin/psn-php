<?php
namespace Tustin\PlayStation\Iterator;

use InvalidArgumentException;
use Tustin\PlayStation\Model\Message;
use Tustin\PlayStation\Model\MessageThread;

class MessagesIterator extends AbstractApiIterator
{
    /**
     * The message thread that these messages are in.
     *
     * @var MessageThread $thread
     */
    protected $thread;

    protected int $limit;

    protected string $maxEventIndexCursor;
        
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
        // $params = [
        //     'fields' => 'threadEvents',
        //     'count' => $this->limit,
        // ];

        // if ($cursor != null)
        // {
        //     if (!is_string($cursor))
        //     {
        //         throw new InvalidArgumentException("$cursor must be a string.");
        //     }
       
        //     $params['maxEventIndex'] = $cursor;
        // }

        // $results = $this->get(
        //     'https://us-gmsg.np.community.playstation.net/groupMessaging/v1/threads/' . $this->thread->id(), 
        //     $params
        // );

        // $this->maxEventIndexCursor = $results->maxEventIndexCursor;

        // $this->update($results->resultsCount, $results->threadEvents);
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
            $this->getFromOffset($this->currentOffset)->messageEventDetail
        );
    }
}