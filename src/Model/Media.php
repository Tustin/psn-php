<?php

namespace Tustin\PlayStation\Model;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enums\UgcType;
use Tustin\PlayStation\Enums\CloudStatusType;
use Tustin\PlayStation\Model\Trophy\TrophyTitle;
use Tustin\PlayStation\Enums\TranscodeStatusType;
use Tustin\PlayStation\Exceptions\NotFoundHttpException;

class Media extends Model
{
    public function __construct(private string $ugcId)
    {
        parent::__construct();
    }

    /**
     * Creates a new Media object from existing data.
     */
    public static function fromObject(object $data): self
    {
        return (new static($data->sourceUgcId))
            ->setCache($data);
    }

    /**
     * Gets the creator of the media.
     */
    public function creator(): User
    {
        return new User($this->pluck('sceUserAccountId'));
    }

    /**
     * Gets the trophy title for the media.
     */
    public function trophyTitle(): TrophyTitle
    {
        return new TrophyTitle($this->npCommunicationId());
    }

    /**
     * Gets the media id.
     */
    public function id(): string
    {
        return $this->ugcId;
    }

    /**
     * Gets if the media is a spoiler or not.
     */
    public function spoiler(): bool
    {
        return $this->pluck('isSpoiler');
    }

    /**
     * Gets the language of the media.
     */
    public function language(): string
    {
        return $this->pluck('language');
    }

    /**
     * Gets the type of the media.
     */
    public function type(): ?UgcType
    {
        return UgcType::tryFrom($this->pluck('ugcType'));
    }

    /**
     * Gets the title of the media.
     */
    public function title(): string
    {
        return $this->pluck('title');
    }

    /**
     * Gets the upload date of the media.
     */
    public function uploadDate(): Carbon
    {
        return Carbon::parse($this->pluck('uploadDate'));
    }

    /**
     * Gets the NP communication ID for the media.
     */
    public function npCommunicationId(): string
    {
        return $this->pluck('npCommId');
    }

    /**
     * Gets the title name of the media.
     */
    public function titleName(): string
    {
        return $this->pluck('sceTitleName');
    }

    /**
     * Gets the title ID of the media.
     */
    public function titleId(): string
    {
        return $this->pluck('sceTitleId');
    }

    /**
     * Gets the file size of the media.
     */
    public function fileSize(): int
    {
        return $this->pluck('fileSize');
    }

    /**
     * Gets the file type of the media.
     */
    public function fileType(): string
    {
        return $this->pluck('fileType');
    }

    /**
     * Gets the cloud status of the media.
     */
    public function cloudStatus(): ?CloudStatusType
    {
        return CloudStatusType::tryFrom($this->pluck('cloudStatus'));
    }

    /**
     * Gets the transcode status of the media.
     */
    public function transcodeStatus(): ?TranscodeStatusType
    {
        return TranscodeStatusType::tryFrom($this->pluck('transcodeStatus'));
    }

    /**
     * Generates a URL with the required parameters to access the asset. 
     */
    public function url(): ?string
    {
        switch ($this->type()) {
            case UgcType::Video:
                return $this->generateUrls()['downloadUrl'];

            case UgcType::Image:
                return $this->generateUrls()['screenshotUrl'];

            default:
                return null;
        }
    }

    /**
     * Generates parameterized URLs for the media asset.
     * 
     * @throws NotFoundHttpException If the media is not found.
     */
    public function generateUrls(): array
    {
        return $this->get('gameMediaService/v2/c2s/ugc/' . $this->id());
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
