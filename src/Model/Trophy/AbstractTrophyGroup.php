<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Factory\TrophyFactory;

abstract class AbstractTrophyGroup extends Model
{
    public function __construct(
        private AbstractTrophyTitle $trophyTitle,
        private string $groupId,
    ) {
        parent::__construct($trophyTitle->getHttpClient());
    }

    /**
     * Creates a new trophy group from existing data.
     */
    public static function fromObject(AbstractTrophyTitle $trophyTitle, object $data): self
    {
        $instance = new static($trophyTitle, $data->trophyGroupId);
        $instance->setCache($data);

        return $instance;
    }

    /**
     * Gets the id for this trophy group.
     */
    public function id(): string
    {
        return $this->pluck('trophyGroupId');
    }

    /**
     * Gets the trophy title for this trophy group.
     */
    public function title(): AbstractTrophyTitle
    {
        return $this->trophyTitle;
    }

    /**
     * Gets all the trophies in the trophy group.
     */
    public function trophies(): TrophyFactory
    {
        return new TrophyFactory($this);
    }
}
