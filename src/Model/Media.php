<?php
namespace Tustin\PlayStation\Model;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\UgcType;
use Tustin\PlayStation\Model\Trophy\TrophyTitle;
use Tustin\PlayStation\Enum\CloudStatusType;
use Tustin\PlayStation\Enum\TranscodeStatusType;

class Media extends Model
{
	public function __construct(Client $client, private string $ugcId)
	{
		parent::__construct($client);
	}

	/**
	 * Creates a new Media object from existing data.
	 */
	public static function fromObject(Client $client, object $data): self
    {
        return ( new static($client, $data->sourceUgcId))->withCache($data);
    }

	public function creator(): User
	{
		return new User($this->getHttpClient(), $this->pluck('sceUserAccountId'));
	}
	
	public function trophyTitle(): TrophyTitle
	{
		return new TrophyTitle($this->getHttpClient(), $this->npCommunicationId());
	}

	public function game(): UserGameTitle
	{
		return new UserGameTitle($this->getHttpClient(), $this->titleId());
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
		return UgcType::from($this->pluck('ugcType'));
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
		return CloudStatusType::from($this->pluck('cloudStatus'));
	}

	public function transcodeStatus(): TranscodeStatusType
	{
		return TranscodeStatusType::from($this->pluck('transcodeStatus'));
	}

	/**
	 * Generates a URL with the required parameters to access the asset. 

	 * @return string
	 */
	public function url(): string
	{
		switch ($this->type())
		{
			case UgcType::Video:
				return $this->generateUrls()->downloadUrl;
			break;

			case UgcType::Image:
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