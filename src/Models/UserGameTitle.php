<?php

namespace Tustin\PlayStation\Models;

use Tustin\PlayStation\ApiModel;
use Tustin\PlayStation\Models\Store\Concept;

class UserGameTitle extends ApiModel
{
    public function __construct(private string $accountId, private string $npTitleId) {}

    /**
     * Creates a new game title from existing data.
     */
    public static function fromObject(string $accountId, string $npTitleId, object $data): self
    {
        return (new UserGameTitle($accountId, $npTitleId))
            ->setCache($data);
    }

    /**
     * Gets the store concept for the game.
     */
    public function concept(): Concept
    {
        return new Concept($this->pluck('concept.id'));
    }

    /**
     * Gets the play duration.
     * Example: "PT1192H44M48S"
     */
    public function playDuration(): string
    {
        return $this->pluck('playDuration');
    }

    /**
     * Gets the first played date.
     * Example: "2015-11-13T13:05:52Z"
     */
    public function firstPlayedDateTime(): string
    {
        return $this->pluck('firstPlayedDateTime');
    }

    /**
     * Gets the last played date.
     * Example: "2021-02-16T21:39:53.890Z"
     */
    public function lastPlayedDateTime(): string
    {
        return $this->pluck('lastPlayedDateTime');
    }

    /**
     * Gets the last play count.
     */
    public function playCount(): int
    {
        return $this->pluck('playCount');
    }

    /**
     * Gets the category.
     * Example: "ps4_game", "ps4_nongame_mini_app", "ps5_native_game", "unknown"
     */
    public function category(): string
    {
        return $this->pluck('category');
    }

    /**
     * Gets the localized image url.
     */
    public function localizedImageUrl(): string
    {
        return $this->pluck('localizedImageUrl');
    }

    /**
     * Gets the image url.
     */
    public function imageUrl(): string
    {
        return $this->pluck('imageUrl');
    }

    /**
     * Gets the localized name.
     */
    public function localizedName(): string
    {
        return $this->pluck('localizedName');
    }

    /**
     * Gets the name.
     */
    public function name(): string
    {
        return $this->pluck('name');
    }

    /**
     * Gets the id.
     * Example: "CUSA02818_00"
     */
    public function id(): string
    {
        return $this->npTitleId ??= $this->pluck('titleId');
    }

    /**
     * Gets the service.
     * Example: "none(purchased)", "none_purchased", "other"
     */
    public function service(): string
    {
        return $this->pluck('service');
    }

    /**
     * Gets the stats.
     */
    public function stats(): object
    {
        return $this->pluck('stats');
    }

    /**
     * Gets the media.
     */
    public function media(): object
    {
        return $this->pluck('media');
    }

    /**
     * Gets the game title data.
     */
    public function fetch(): object
    {
        return $this->get('gamelist/v2/users/' . $this->accountId . '/titles/' . $this->id());
    }
}
