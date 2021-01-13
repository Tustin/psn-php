<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Api\Model\MessageThread;
use Tustin\PlayStation\Api\MessageThreadsFactory;
use Tustin\PlayStation\Api\MessageThreadsRepository;

class MessageThreadsIterator extends AbstractApiIterator
{
    /**
     * The message threads repository.
     *
     * @var MessageThreadsFactory
     */
    private $messageThreadsFactory;
    
    public function __construct(MessageThreadsFactory $messageThreadsFactory)
    {
        parent::__construct($messageThreadsFactory->httpClient);

        $this->messageThreadsFactory = $messageThreadsFactory;
        $this->limit = 20;

        $this->access(0);
    }

    public function access($cursor) : void
    {
        $results = $this->get('gamingLoungeGroups/v1/members/me/groups', [
            'favoriteFilter' => 'notFavorite',
            'limit' => $this->limit,
            'offset' => $cursor, // Needs Testing
            'includeFields' => 'groupName,groupIcon,members,mainThread,joinedTimestamp,modifiedTimestamp,totalGroupCount,isFavorite,existsNewArrival,partySessions'
        ]);

        $this->update($results->totalSize, $results->threads);
    }

    public function current()
    {
        return MessageThread::fromObject(
            $this->messageThreadsFactory,
            $this->getFromOffset($this->currentOffset)
        );
    }
}