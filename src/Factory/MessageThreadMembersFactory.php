<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use Countable;
use IteratorAggregate;
use CallbackFilterIterator;
use Tustin\PlayStation\Model\MessageThread;

class MessageThreadMembersFactory //implements IteratorAggregate, Countable
{
    /**
     * The message thread the members are in.
     *
     * @var MessageThread
     */
    private $messageThread;


    /**
     * The name to filter with.
     *
     * @var string
     */
    private $name;

    public function __construct(MessageThread $messageThread)
    {
        $this->messageThread = $messageThread;
    }

    /**
     * Returns only members with a name containing the supplied value.
     *
     * @param string $name
     * @return MessageThreadMembersFactory
     */
    public function withName(string $name) : MessageThreadMembersFactory
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns whether or not a member with the onlineId exists in this thread.
     * 
     * @param string $onlineId
     * @return boolean
     */
    public function contains(string $onlineId) : bool
    {
        foreach ($this as $member)
        {
            if (strcasecmp($member->onlineId(), $onlineId) === 0)
            {
                return true;
            }        
        }

        return false;
    }

    /**
     * Returns whether or not this thread contains only the user supplied and the client.
     *
     * @param string $onlineId
     * @return boolean
     */
    public function containsOnly(string $onlineId) : bool
    {
        return $this->contains($onlineId) && $this->count() === 2;
    }

    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
     */
    // public function getIterator() : Iterator
    // {
    //     // Since there is no endpoint to get thread members from, we can just yield from the existing array.
    //     // $iterator = yield from array_map(
    //     //     fn($member) => User::create($this->messageThread->getHttpClient(), $member->onlineId, true),
    //     //     $this->messageThread->membersArray()
    //     // );

    //     // if ($this->name)
    //     // {
    //     //     $iterator = new CallbackFilterIterator(
    //     //         $iterator, 
    //     //         fn($it) => stripos($it->onlineId(), $this->name) !== false
    //     //     );
    //     // }

    //     return null;
    // }

    public function count() : int
    {
        return \count($this->messageThread->membersArray());
    }
}