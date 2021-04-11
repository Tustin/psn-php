<?php

namespace Tustin\PlayStation\Model;

use Carbon\Carbon;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\Interfaces\Fetchable;
use Tustin\PlayStation\Factory\GroupsFactory;
use Tustin\PlayStation\Model\Message\Sendable;
use Tustin\PlayStation\Factory\MessagesFactory;
use Tustin\PlayStation\Factory\GroupMembersFactory;
use Tustin\PlayStation\Model\Message\AbstractMessage;
use Tustin\PlayStation\Factory\MessageThreadMembersFactory;

class Group extends Api implements Fetchable
{
    use Model;

    private string $groupId;

    private array $members;

    public function __construct(GroupsFactory $groupsFactory, string $groupId)
    {
        parent::__construct($groupsFactory->getHttpClient());

        $this->groupId = $groupId;
    }

    public static function fromObject(GroupsFactory $groupsFactory, object $data)
    {
        $instance = new static($groupsFactory, $data->groupId);
        $instance->setCache($data);

        return $instance;
    }

    /**
     * Gets all the members in the message thread.
     *
     * @return GroupMembersFactory
     */
    public function members(): GroupMembersFactory
    {
        return new GroupMembersFactory($this);
    }

    /**
     * Gets all the message thread members as an array.
     *
     * @return array
     */
    public function membersArray(): array
    {
        return $this->pluck('members');
    }

    /**
     * Gets the member count in the message thread.
     *
     * @return integer
     */
    public function memberCount(): int
    {
        return count(
            $this->members()
        );
    }

    /**
     * The date and time when the client joined the group.
     *
     * @return Carbon
     */
    public function joined(): Carbon
    {
        return Carbon::parse($this->pluck('joinedTimestamp'));
    }

    /**
     * Gets if the group is favorited or not.
     *
     * @return boolean
     */
    public function isFavorited(): bool
    {
        return $this->pluck('isFavorited');
    }

    public function favorite()
    {
        // @TODO: Add favorite code here.
    }

    /**
     * The main message thread for this group.
     *
     * @return MessageThread
     */
    public function messageThread(): MessageThread
    {
        return MessageThread::fromObject($this, (object)$this->pluck('mainThread'));
    }

    public function partySessions()
    {
        // @TODO: Figure this one out...
    }

    /**
     * Sends a message to the group's message thread.
     *
     * @param Sendable $message
     * @return AbstractMessage
     */
    public function sendMessage(Sendable $message): AbstractMessage
    {
        return $this->messageThread()->sendMessage($message);
    }

    /**
     * The group id.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->groupId;
    }

    /**
     * Gets the group info from the PlayStation API.
     *
     * @return object
     */
    public function fetch(): object
    {
        return $this->get('gamingLoungeGroups/v1/members/me/groups/' . $this->id(), [
            'fields' => implode(',', [
                'groupName',
                'groupIcon',
                'members',
                'mainThread',
                'joinedTimestamp',
                'modifiedTimestamp',
                'isFavorite',
                'existsNewArrival',
                'partySession'
            ])
        ]);
    }
}
