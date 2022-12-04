<?php
namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\TrophyType;

class Trophy extends Model
{
    public function __construct(private TrophyGroup $trophyGroup, private int $id)
    {
        parent::__construct($trophyGroup->getHttpClient());
    }

    public static function fromObject(TrophyGroup $trophyGroup, object $data): Trophy
    {
        $trophy = new static($trophyGroup, $data->trophyId);
        $trophy->setCache($data);

        return $trophy;
    }
    /**
     * Gets the trophy name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->pluck('trophyName');
    }
    
    /**
     * Gets the trophy id.
     *
     * @return integer
     */
    public function id(): int
    {
        return $this->id ??= $this->pluck('id');
    }

    /**
     * Gets the trophy details.
     *
     * @return string
     */
    public function detail(): string
    {
        return $this->pluck('trophyDetail');
    }

    /**
     * Gets the trophy type. (platinum, bronze, silver, gold)
     *
     * @return TrophyType
     */
    public function type(): TrophyType
    {
        return new TrophyType($this->pluck('trophyType'));
    }

    /**
     * Get the trophy earned rate.
     *
     * @return float
     */
    public function earnedRate(): float
    {
        return $this->pluck('trophyEarnedRate');
    }

    /**
     * Check if the trophy is hidden.
     *
     * @return boolean
     */
    public function hidden(): bool
    {
        return $this->pluck('trophyHidden');
    }
    
    /**
     * Gets the trophy icon URL.
     *
     * @return string
     */
    public function iconUrl(): string
    {
        return $this->pluck('trophyIconUrl');
    }

    /**
     * Gets the trophy progress target value, if any.
     *
     * @return string
     */
    public function progressTargetValue(): string
    {
        return $this->pluck('trophyProgressTargetValue') ?? '';
    }
    
    /**
     * Gets the trophy reward name, if any.
     * Examples: "Emote", "Profile Avatar", "Profile Banner"
     * 
     * @return string
     */
    public function rewardName(): string
    {
        return $this->pluck('trophyRewardName') ?? '';
    }

    /**
     * Gets the trophy reward image url, if any.
     * 
     * @return string
     */
    public function rewardImageUrl(): string
    {
        return $this->pluck('trophyRewardImageUrl') ?? '';
    }

    /**
     * Check if the user have earned this trophy.
     *
     * @return boolean
     */
    public function earned(): bool
    {
        return $this->pluck('earned');
    }

    /**
     * Get the date and time the user earned this trophy, if any.
     *
     * @return string
     */
    public function earnedDateTime(): string
    {
        return $this->pluck('earnedDateTime') ?? '';
    }

    /**
     * Get the progress count for the user on this trophy, if any.
     *
     * @return string
     */
    public function progress(): string
    {
        return $this->pluck('progress') ?? '';
    }

    /**
     * Get the progress percentage for the user on this trophy, if any.
     *
     * @return string
     */
    public function progressRate(): string
    {
        return $this->pluck('progressRate') ?? '';
    }

    /**
     * Get the date and time when a progress was made for the user on this trophy, if any.
     *
     * @return string
     */
    public function progressedDateTime(): string
    {
        return $this->pluck('progressedDateTime') ?? '';
    }
    
    /**
     * Fetches the trophy data from the API.
     *
     * @return object
     */
    public function fetch(): object
    {
        return $this->get('trophy/v1/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId()  . '/trophies/' . $this->id(), [
            'npServiceName' => $this->trophyGroup->title()->serviceName()
        ]);
    }
}
