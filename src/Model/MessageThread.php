<?php

namespace Tustin\PlayStation\Model;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\Group;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\Interfaces\Fetchable;
use Tustin\PlayStation\Factory\MessagesFactory;

class MessageThread extends Api
{
	use Model;
	
	/**
	 * @var Group
	 */
	private $group;
	
	/**
	 * @var string
	 */
    private $threadId;

    public function __construct(Group $group, string $threadId)
    {
		parent::__construct($group->getHttpClient());

		$this->group = $group;
        $this->threadId = $threadId;
    }

    public static function fromObject(Group $group, object $data)
    {
        $instance = new static($group, $data->threadId);
		$instance->setCache($data);

        return $instance;
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
     * Gets all messages in this message thread.
     *
     * @return MessagesFactory
     */
    public function messages() : MessagesFactory
    {
        return new MessagesFactory($this);
    }

    /**
     * The thread id.
     *
     * @return string
     */
    public function id() : string
    {
        return $this->threadId ??= $this->pluck('threadId');
	}
	
	/**
	 * The group.
	 *
	 * @return Group
	 */
	public function group() : Group
	{
		return $this->group;
	}

	/**
	 * The message count for this thread.
	 *
	 * @return integer
	 */
	public function messageCount() : int
	{
		return $this->pluck('messageCount') ?? 0;
	}
}