<?php

namespace Tustin\PlayStation\Models;

use Tustin\PlayStation\ApiModel;
use Tustin\PlayStation\Models\Group;
use Tustin\PlayStation\Factory\MessagesList;
use Tustin\PlayStation\Models\Messages\Message;
use Tustin\PlayStation\Models\Messages\Sendable;

class MessageThread extends ApiModel
{
    public function __construct(private string $groupId, private string $threadId)
    {
        parent::__construct();
    }

    /**
     * Creates a new message thread from existing data. 
     */
    public static function fromObject(string $groupId, string $threadId, object $data): self
    {
        return (new static($groupId, $threadId))
            ->setCache($data);
    }

    /**
     * Sends a message to the message thread.
     */
    public function sendMessage(Sendable $message): Message
    {
        $this->postJson(
            'gamingLoungeGroups/v1/groups/' . $this->group()->id() . '/threads/' . $this->id() . '/messages',
            $message->build()
        );

        return $this->messages()->first();
    }

    /**
     * Gets all messages in this message thread.
     */
    public function messages(): MessagesList
    {
        return new MessagesList($this);
    }

    /**
     * Gets the id for the message thread.
     */
    public function id(): string
    {
        return $this->threadId ??= $this->pluck('threadId');
    }

    /**
     * Gets the group for the message thread.
     */
    public function group(): Group
    {
        return new Group($this->groupId);
    }

    /**
     * Gets the message count for the message thread.
     */
    public function messageCount(): int
    {
        return $this->pluck('messageCount') ?? 0;
    }

    /**
     * Gets the message thread info from the PlayStation API.
     */
    public function fetch(): object
    {
        return $this->get(
            'gamingLoungeGroups/v1/groups/' . $this->group()->id() . '/threads/' . $this->id()
        );
    }
}
