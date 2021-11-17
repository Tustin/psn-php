<?php
namespace Tustin\PlayStation\Model;

use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\UgcType;
use Tustin\PlayStation\Model\Trophy\TrophyTitle;
use Tustin\PlayStation\Exception\MissingKeyPairIdException;
use GuzzleHttp\Cookie\SetCookie as CookieParser;
use Tustin\PlayStation\Enum\CloudStatusType;
use Tustin\PlayStation\Enum\TranscodeStatusType;

class Media extends Model
{
	private string $ugcId;

	private array $cookies = [];

	public function __construct(Client $client, string $ugcId)
	{
		parent::__construct($client);

		$this->ugcId = $ugcId;
	}

	public static function fromObject(Client $client, object $data): Media
    {
        $media = new static($client, $data->sourceUgcId);
        $media->setCache($data);

        return $media;
    }

	public function creator(): User
	{
		return new User($this->getHttpClient(), $this->pluck('sceUserAccountId'));
	}
	
	public function trophyTitle(): TrophyTitle
	{
		return new TrophyTitle($this->getHttpClient(), $this->npCommunicationId());
	}

	public function game(): GameTitle
	{
		return new GameTitle($this->getHttpClient(), $this->titleId());
	}

	public function id(): string
	{
		return $this->pluck('id');
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

	public function title(): string
	{
		return $this->pluck('title');
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

	public function titleId(): string
	{
		return $this->pluck('sceTitleId');
	}

	public function fileSize(): int
	{
		return $this->pluck('fileSize');
	}

	public function fileType(): string
	{
		return $this->pluck('fileType');
	}

	public function cloudStatus(): CloudStatusType
	{
		return new CloudStatusType($this->pluck('cloudStatus'));
	}

	public function transcodeStatus(): TranscodeStatusType
	{
		return new TranscodeStatusType($this->pluck('transcodeStatus'));
	}

	/**
	 * Generates a URL with the required parameters to access the asset. 

	 * @return string
	 */
	public function url(): string
	{
		switch ($this->type())
		{
			case UgcType::video():
				return $this->generateUrls()->downloadUrl;
			break;

			case UgcType::image():
				return $this->generateUrls()->screenshotUrl;
			break;
		}
	}

	/**
	 * Generates parameterized URLs for the media asset.
	 *
	 * @return object
	 */
	private function generateUrls(): object
	{
		return $this->get('gameMediaService/v2/c2s/ugc/' . $this->id() . '/url');

		// Lol so you dont need to do this below.
		// $url .= '?';

		// if (array_key_exists('CloudFront-Policy', $this->cookies)) {
		// 	$url .= 'Policy=' . $this->cookies['CloudFront-Policy'] . '&';
		// }

		// if (array_key_exists('CloudFront-Key-Pair-Id', $this->cookies)) {
		// 	$url .= 'Key-Pair-Id=' . $this->cookies['CloudFront-Key-Pair-Id'] . '&';
		// }

		// if (array_key_exists('CloudFront-Signature', $this->cookies)) {
		// 	$url .= 'Signature=' . $this->cookies['CloudFront-Signature'] . '&';
		// }

		// return $url;
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