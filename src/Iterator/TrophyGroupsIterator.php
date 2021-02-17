<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\TrophyGroup;
use Tustin\PlayStation\AbstractTrophyTitle;
use Tustin\PlayStation\Model\UserTrophyTitle;

class TrophyGroupsIterator extends AbstractApiIterator
{
    /**
     * Current trophy title.
     *
     * @var AbstractTrophyTitle
     */
    private $title;

    public function __construct(AbstractTrophyTitle $title)
    {
        parent::__construct($title->getHttpClient());

        $this->title = $title;

        $this->access(0);
    }

    public function access($cursor) : void
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

    public function current()
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
