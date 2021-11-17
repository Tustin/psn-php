<?php
namespace Tustin\PlayStation\Model;

use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\UgcType;
use Tustin\PlayStation\Model\Trophy\TrophyTitle;

class Media extends Model
{
	/**
	 * @var string
	 */
	private $ugcId;

	public function __construct(Client $client, string $ugcId)
	{
		parent::__construct($client);

		$this->ugcId = $ugcId;
	}

	public static function fromObject(Client $client, object $data): Media
    {
        $media = new static($client, $client->sourceUgcId);
        $media->setCache($data);

        return $media;
    } 

	public function spoiler(): bool
	{
		return $this->pluck('isSpoiler');
	}

	public function language(): string
	{
		return $this->pluck('language');
	}

	public function type(): UgcType
	{
		return new UgcType($this->pluck('ugcType'));
	}

	public function uploadDate(): Carbon
	{
		return Carbon::parse($this->pluck('uploadDate'));
	}

	public function npCommunicationId(): string
	{
		return $this->pluck('npCommId');
	}

	public function titleName(): string
	{
		return $this->pluck('sceTitleName');
	}

	public function sender(): User
	{
		return new User($this->getHttpClient(), $this->pluck('sceUserAccountId'));
	}
	
	public function trophyTitle(): TrophyTitle
	{
		return new TrophyTitle($this->getHttpClient(), $this->npCommunicationId());
	}

	public function titleId(): string
	{
		return $this->pluck('sceTitleId');
	}

	/**
	 * Gives a URL (with a token) for the media.
	 * 
	 * Media can only be consumed using a valid JWT token generated from this method.
	 * 
	 * Actually I don't believe the above is true. CloudFront just needs a Key-Pair-Id sent with the request. Needs more investigating.
	 * - Tustin, November 16, 2021.
	 * 
	 * @return string
	 */
	public function url(): string
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
		catch (Exception $ex) {
			die($ex); // @TODO Debug.
		}
	}

	public function fetch(): object
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