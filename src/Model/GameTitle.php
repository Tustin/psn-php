<?php
namespace Tustin\PlayStation\Model;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Model\Store\Concept;
use Tustin\PlayStation\Factory\GameListFactory;

class GameTitle extends Model
{
    public function __construct(GameListFactory $gameListFactory, private string $id)
    {
        parent::__construct($gameListFactory->getHttpClient());
    }

    public static function fromObject(GameListFactory $gameListFactory, object $data): GameTitle
    {
        $game = new static($gameListFactory, $data->titleId);
        $game->setCache($data);

        return $game;
    }

    /**
     * Gets the store concept for the game.
     *
     * @return Concept
     */
    public function concept(): Concept
    {
        return new Concept($this->getHttpClient(), $this->pluck('concept.id'));
    }

    /**
     * Gets the play duration.
     * Example: "PT1192H44M48S"
     *
     * @return string
     */
    public function playDuration(): string
    {
        return $this->pluck('playDuration');
    }
    
    /**
     * Gets the first played date.
     * Example: "2015-11-13T13:05:52Z"
     *
     * @return string
     */
    public function firstPlayedDateTime(): string
    {
        return $this->pluck('firstPlayedDateTime');
    }

    /**
     * Gets the last played date.
     * Example: "2021-02-16T21:39:53.890Z"
     *
     * @return string
     */
    public function lastPlayedDateTime(): string
    {
        return $this->pluck('lastPlayedDateTime');
    }

    /**
     * Gets the last play count.
     *
     * @return int
     */
    public function playCount(): int
    {
        return $this->pluck('playCount');
    }

    /**
     * Gets the category.
     * Example: "ps4_game", "ps4_nongame_mini_app", "ps5_native_game", "unknown"
     *
     * @return string
     */
    public function category(): string
    {
        return $this->pluck('category');
    }

    /**
     * Gets the localized image url.
     *
     * @return string
     */
    public function localizedImageUrl(): string
    {
        return $this->pluck('localizedImageUrl');
    }

    /**
     * Gets the image url.
     *
     * @return string
     */
    public function imageUrl(): string
    {
        return $this->pluck('imageUrl');
    }

    /**
     * Gets the localized name.
     *
     * @return string
     */
    public function localizedName(): string
    {
        return $this->pluck('localizedName');
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->pluck('name');
    }

    /**
     * Gets the id.
     * Example: "CUSA02818_00"
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id ??= $this->pluck('titleId');
    }

    /**
     * Gets the service.
     * Example: "none(purchased)", "none_purchased", "other"
     *
     * @return string
     */
    public function service(): string
    {
        return $this->pluck('service');
    }

    /**
     * Gets the stats.
     *
     * @return object
     */
    public function stats(): object
    {
        return $this->pluck('stats');
    }

    /**
     * Gets the media.
     *
     * @return object
     */
    public function media(): object
    {
        return $this->pluck('media');
    }
    
    public function fetch(): object
    {
        return $this->get('gamelist/v2/users/' . $this->getFactory()->getUser()->accountId() . '/titles/' . $this->id());
    }
}
