<?php
namespace Tustin\PlayStation\Model;

use Tustin\PlayStation\Api;
use InvalidArgumentException;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Model\TrophyTitle;
use Tustin\PlayStation\AbstractTrophyTitle;
use Tustin\PlayStation\Interfaces\Fetchable;
use Tustin\PlayStation\Factory\TrophyFactory;

class TrophyGroup extends Api implements Fetchable
{
    use Model;

	private $trophyTitle;
	
	private $groupId;

    public function __construct(AbstractTrophyTitle $trophyTitle, string $groupId)
    {
		parent::__construct($trophyTitle->getHttpClient());
        $this->trophyTitle = $trophyTitle;
		$this->groupId = $groupId;
    }

    public static function fromObject(AbstractTrophyTitle $trophyTitle, object $data) : TrophyGroup
    {
		$instance = new static($trophyTitle, $data->trophyGroupId);
		$instance->setCache($data);
		
		return $instance;
    }

    /**
     * Gets the trophy title for this trophy group.
     *
     * @return AbstractTrophyTitle
     */
    public function title() : AbstractTrophyTitle
    {
        return $this->trophyTitle;
    }
    
    /**
     * Gets all the trophies in the trophy group.
     *
     * @return TrophyFactory
     */
    public function trophies() : TrophyFactory
    {
        return new TrophyFactory($this);
    }

    /**
     * Gets the trophy group name.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->pluck('trophyGroups.0.trophyGroupName');
    }

    /**
     * Gets the trophy group ID.
     *
     * @return string
     */
    public function id() : string
    {
        return $this->pluck('trophyGroups.0.trophyGroupId');
    }

    /**
     * Gets the trophy group icon URL.
     *
     * @return string
     */
    public function iconUrl() : string
    {
        return $this->pluck('trophyGroups.0.trophyGroupIconUrl');
    }

    /**
     * Gets the defined trophies for this trophy group.
     *
     * @return array
     */
    public function definedTrophies() : array
    {
        return $this->pluck('definedTrophies');
    }

    /**
     * Gets the bronze trophy count.
     *
     * @return integer
     */
    public function bronze() : int
    {
        return $this->pluck('definedTrophies.bronze');
    }

    /**
     * Gets the silver trophy count.
     *
     * @return integer
     */
    public function silver() : int
    {
        return $this->pluck('definedTrophies.silver');
    }

    /**
     * Gets the gold trophy count.
     *
     * @return integer
     */
    public function gold() : int
    {
        return $this->pluck('definedTrophies.gold');
    }

    /**
     * Gets whether this trophy group has a platinum or not.
     * 
     * @return boolean
     */
    public function hasPlatinum() : bool
    {
        return $this->pluck('definedTrophies.platinum') == 1;
    }

    /**
     * Gets the trophy count for a specificed trophy type.
     *
     * @param TrophyType $trophyType
     * @return integer
     */
    public function trophyCount(TrophyType $trophyType) : int
    {
        switch ($trophyType)
        {
            case TrophyType::bronze():
            return $this->bronze();
            case TrophyType::silver():
            return $this->silver();
            case TrophyType::gold():
            return $this->gold();
            case TrophyType::platinum():
            return (int)$this->hasPlatinum();
            default:
            throw new InvalidArgumentException("Trophy type [$trophyType] does not contain a count method.");
        }
    }

    /**
     * Gets the amount of trophies in the trophy group.
     *
     * @return integer
     */
    public function totalTrophyCount() : int
    {
        $count = $this->bronze() + $this->silver() + $this->gold();

        return $this->hasPlatinum() ? ++$count : $count;
	}
	
	public function fetch() : object
	{
		return $this->get(
			'trophy/v1/npCommunicationIds/' . $this->title()->npCommunicationId()  . '/trophyGroups',
			[
				'npServiceName' => $this->title()->serviceName()
			]
		);
	}
}