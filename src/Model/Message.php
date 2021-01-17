<?php

namespace Tustin\PlayStation\Model;

use Carbon\Carbon;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Model\MessageThread;

class Message extends Api
{
	use Model;

	/**
	 * The message thread this message is in.
	 *
	 * @var MessageThread
	 */
	private $thread;

	public function __construct(MessageThread $thread, object $messageData)
	{
		$this->setCache($messageData);

		$this->thread = $thread;
	}

	public function fromObject(MessageThread $thread, object $messageData)
	{
		$instance = new static($thread, $messageData);

		return $instance;
	}

	/**
	 * Gets the type of message.
	 * 
	 * Returns MessageType::unknown on unmapped message types. If you receive this type, open a PR/issue.
	 *
	 * @return MessageType
	 */
	public function type(): MessageType
	{
		try {
			return new MessageType($this->pluck('eventCategoryCode'));
		} catch (\UnexpectedValueException $e) {
			return MessageType::unknown();
		}
	}

	/**
	 * Gets the media URL if the message contains some piece of media (image, audio).
	 *
	 * @return string|null
	 */
	public function mediaUrl(): ?string
	{
		// @NeedsTesting
		return $this->pluck('attachedMediaPath');
	}

	/**
	 * Gets the message body.
	 *
	 * @return string
	 */
	public function body(): string
	{
		return $this->pluck('messageDetail.body');
	}

	/**
	 * Gets the event index ID for the message.
	 * 
	 * Used as a cursor for pagination.
	 *
	 * @return string
	 */
	public function eventIndex(): string
	{
		return $this->pluck('eventIndex');
	}

	/**
	 * Gets the date and time when the message was posted.
	 *
	 * @return Carbon
	 */
	public function date(): Carbon
	{
		// @NeedsTesting
		return Carbon::parse($this->pluck('postDate'))->setTimezone('UTC');
	}

	/**
	 * Returns the message thread that this message is in.
	 *
	 * @return MessageThread
	 */
	public function messageThread(): MessageThread
	{
		return $this->thread;
	}

	/**
	 * Gets the message sender.
	 *
	 * @return User
	 */
	public function sender(): User
	{
		return new User(
			$this->messageThread()->getHttpClient(),
			$this->pluck('sender.accountId')
		);
	}
}
