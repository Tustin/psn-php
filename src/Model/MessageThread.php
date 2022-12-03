<?php

namespace Tustin\PlayStation\Model;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Model\Group;
use Tustin\PlayStation\Model\Message\Sendable;
use Tustin\PlayStation\Factory\MessagesFactory;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class MessageThread extends Model
{
    public function __construct(private Group $group, private string $threadId)
    {
        parent::__construct($group->getHttpClient());
    }

    public static function fromObject(Group $group, object $data)
    {
        $instance = new static($group, $data->threadId);
        $instance->setCache($data);

        return $instance;
    }

    /**
     * Sends a message to the message thread.
     */
    public function sendMessage(Sendable $message): AbstractMessage
    {
        $this->postJson(
            'gamingLoungeGroups/v1/groups/' . $this->group()->id() . '/threads/' . $this->id() . '/messages',
            $message->build()
        );

        return $this->messages()->first();
    }

    /**
     * Gets all messages in this message thread.
     *
     * @return MessagesFactory
     */
    public function messages(): MessagesFactory
    {
        return new MessagesFactory($this);
    }

    /**
     * The thread id.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->threadId ??= $this->pluck('threadId');
    }

    /**
     * The message group for this thread.
     *
     * @return Group
     */
    public function group(): Group
    {
        return $this->group;
    }

    /**
     * The message count for this thread.
     *
     * @return integer
     */
    public function messageCount(): int
    {
        return $this->pluck('messageCount') ?? 0;
    }

    // @TODO: Implement this.
    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
