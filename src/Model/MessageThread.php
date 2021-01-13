<?php

namespace Tustin\PlayStation\Model;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\MessagesFactory;
use Tustin\PlayStation\Interfaces\Fetchable;
use Tustin\PlayStation\Factory\MessageThreadsFactory;
use Tustin\PlayStation\Factory\MessageThreadMembersFactory;

class MessageThread extends Api implements Fetchable
{
    use Model;
    
    private string $threadId;

    private array $members;

    public function __construct(MessageThreadsFactory $messageThreadsFactory, string $threadId, array $members = [])
    {
        parent::__construct($messageThreadsFactory->getHttpClient());

        $this->threadId = $threadId;
        $this->members = $members;
    }

    public static function fromObject(MessageThreadsFactory $messageThreadsFactory, object $data)
    {
        $instance = new static($messageThreadsFactory, $data->threadId, $data->threadMembers);
		$instance->setCache($data);

        return $instance;
    }

    /**
     * Gets all the members in the message thread.
     *
     * @return MessageThreadMembersFactory
     */
    public function members() : MessageThreadMembersFactory
    {
        return new MessageThreadMembersFactory($this);
    }

    /**
     * Gets all the message thread members as an array.
     *
     * @return array
     */
    public function membersArray() : array
    {
        return $this->members ??= $this->pluck('threadMembers');
    }

    /**
     * Gets the member count in the message thread.
     *
     * @return integer
     */
    public function memberCount() : int
    {
        return count(
            $this->members()
        );
    }

    // /**
    //  * Sends a message to the message thread.
    //  *
    //  * @param AbstractMessage $message
    //  * @return Message
    //  */
    // public function sendMessage(AbstractMessage $message) : Message
    // {
    //     $this->postMultiPart(
    //         'https://us-gmsg.np.community.playstation.net/groupMessaging/v1/threads/' . $this->id() . '/messages',
    //         $message->build()
    //     );

    //     return $this->messages()->first();
    // }

    /**
     * Gets all messages in the message thread.
     *
     * @return MessagesFactory
     */
    public function messages() : MessagesFactory
    {
        return new MessagesFactory($this);
    }

    /**
     * Gets the message thread ID.
     *
     * @return string
     */
    public function id() : string
    {
        return $this->threadId;
    }

    /**
     * Gets the thread info from the PlayStation API.
     *
     * @param integer $count
     * @return object
     */
    public function fetch(int $count = 1) : object
    {
        return $this->get('https://us-gmsg.np.community.playstation.net/groupMessaging/v1/threads/' . $this->id(), [
            'fields' => implode(',', [
                'threadMembers',
                'threadNameDetail',
                'threadThumbnailDetail',
                'threadProperty',
                'latestTakedownEventDetail',
                'newArrivalEventDetail',
                'threadEvents'
            ]),
            'count' => $count,
        ]);
    }
}