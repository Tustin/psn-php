<?php

namespace Tustin\PlayStation\Models;

use Carbon\Carbon;
use Tustin\PlayStation\ApiModel;
use Tustin\PlayStation\Factories\GroupMembers;
use Tustin\PlayStation\Models\Messages\Message;
use Tustin\PlayStation\Models\Messages\Sendable;

class Group extends ApiModel
{
    private array $members;

    public function __construct(private string $groupId)
    {
    }

    /**
     * Creates a new group from existing data.
     */
    public static function fromObject(string $groupId, object $data): self
    {
        return (new static($groupId))
            ->setCache($data);
    }

    /**
     * Gets all the members in the message thread.
     */
    public function members(): GroupMembers
    {
        return new GroupMembers($this);
    }

    /**
     * Gets all the message thread members as an array.
     */
    public function membersArray(): array
    {
        return $this->pluck('members');
    }

    /**
     * Gets the member count in the message thread.
     */
    public function memberCount(): int
    {
        return count(
            $this->members()
        );
    }

    /**
     * The date and time when the client joined the group.
     */
    public function joined(): \DateTime
    {
        return Carbon::parse($this->pluck('joinedTimestamp'));
    }

    /**
     * Gets if the group is favorited or not.
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
     */
    public function messageThread(): MessageThread
    {
        $mainThread = $this->pluck('mainThread');

        return new MessageThread(
            $this->id(),
            $mainThread['threadId']
        );
    }

    public function partySessions()
    {
        // @TODO: Figure this one out...
    }

    /**
     * Sends a message to the group's message thread.
     */
    public function sendMessage(Sendable $message): Message
    {
        return $this->messageThread()->sendMessage($message);
    }

    /**
     * The group id.
     */
    public function id(): string
    {
        return $this->groupId;
    }

    /**
     * Gets the group info from the PlayStation API.
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
