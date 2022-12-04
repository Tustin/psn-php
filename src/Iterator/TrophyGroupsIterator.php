<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Trophy\TrophyGroup;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Model\Trophy\AbstractTrophyTitle;


class TrophyGroupsIterator extends AbstractApiIterator
{
    /**
     * Current trophy title.
     */
    private AbstractTrophyTitle $title;

    public function __construct(AbstractTrophyTitle $title)
    {
        parent::__construct($title->getHttpClient());

        $this->title = $title;

        $this->access(0);
    }

    public function access(mixed $cursor): void
    {
        if ($this->title instanceof UserTrophyTitle)
        {
            $results = $this->get(
                'trophy/v1/users/' . $this->title->getFactory()->getUser()->accountId() . '/npCommunicationIds/' . $this->title->npCommunicationId() . '/trophyGroups',
                [
                    'npServiceName' => $this->title->serviceName()
                ]
            );
        }
        else
        {
            $results = $this->get(
                'trophy/v1/npCommunicationIds/' . $this->title->npCommunicationId() . '/trophyGroups',
                [
                    'npServiceName' => $this->title->serviceName()
                ]
            );
        }

        $this->update(count($results->trophyGroups), $results->trophyGroups);
    }

    public function current(): TrophyGroup
    {
        if ($this->title instanceof UserTrophyTitle)
        {
            return new TrophyGroup($this->title, $this->getFromOffset($this->currentOffset)->trophyGroupId);
        }
        else
        {
            return new TrophyGroup($this->title,
                $this->getFromOffset($this->currentOffset)->trophyGroupId,
                $this->getFromOffset($this->currentOffset)->trophyGroupName,
                $this->getFromOffset($this->currentOffset)->trophyGroupIconUrl,
                $this->getFromOffset($this->currentOffset)->trophyGroupDetail ?? '');
        }
    }
}
