<?php
namespace Tustin\PlayStation\Model;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Enum\UgcType;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Interfaces\Fetchable;

class Media extends Api implements Fetchable
{
	use Model;

	/**
	 * @var string
	 */
	private $ugcId;

	public function __construct(Client $client, string $ugcId)
	{
		parent::__construct($client);

		$this->ugcId = $ugcId;
	}

	public function spoiler() : bool
	{
		return $this->pluck('isSpoiler');
	}

	public function language() : string
	{
		return $this->pluck('language');
	}

	public function type() : UgcType
	{
		return new UgcType($this->pluck('ugcType'));
	}

	public function uploadDate() : Carbon
	{
		return Carbon::parse($this->pluck('uploadDate'));
	}

	public function npCommunicationId() : string
	{
		return $this->pluck('npCommId');
	}

	public function titleName() : string
	{
		return $this->pluck('sceTitleName');
	}

	public function sender() : User
	{
		return new User($this->getHttpClient(), $this->pluck('sceUserAccountId'));
	}
	
	public function trophyTitle() : TrophyTitle
	{
		return new TrophyTitle($this->getHttpClient(), $this->npCommunicationId())
	}

	public function titleId() : string
	{
		return $this->pluck('sceTitleId');
	}

	/**
	 * Gives a URL (with a token) for the media.
	 * 
	 * Media can only be consumed using a valid JWT token generated from this method.
	 *
	 * @return string
	 */
	public function url() : string
	{
		try {
			if ($this->type() == UgcType::video()) {
				$response = $this->get('gameMediaService/v2/c2s/ugc/' . $this->ugcId . '/url');
				return $response->videoUrl;
			}
			else if ($this->type() == UgcType::image()) {
				$response = $this->get($this->pluck('screenshotUrl'));
				var_dump($response); // @TestMe!
			}
		}
	}

	public function fetch() : object
	{
		return $this->get('gameMediaService/v2/c2s/content', [
			'fields' => implode(',', [
				'title',
				'description',
				'broadcastDate',
				'sceTitleName',
				'countOfViewers',
				'sceUserOnlineId',
				'streamingPreviewImage',
				'serviceType',
				'channelId',
				'sceTitleId',
				'isSpoiler',
				'transcodeStatus'
			]),
			'ugcIds' => $this->ugcId
		]);
	}
}