<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\TrophyGroup;
use Tustin\PlayStation\Model\TrophyTitle;


class TrophyGroupsIterator extends AbstractApiIterator
{
    /**
     * Current trophy title.
     *
     * @var TrophyTitle
     */
    private $title;

    public function __construct(TrophyTitle $title)
    {
        parent::__construct($title->getHttpClient());

        $this->title = $title;

        $this->access(0);
    }

    public function access($cursor) : void
    {
        $results = $this->get(
            'trophy/v1/users/' . $this->title->getFactory()->getUser()->accountId() . 'trophyTitles/' . $this->title->npCommunicationId() .'/trophyGroups',
            [
                'serviceName' => 'trophy'
            ]
        );

        $this->update(count($results->trophyGroups), $results->trophyGroups);
    }

    public function current()
    {
        return new TrophyGroup(
            $this->title,
            $this->getFromOffset($this->currentOffset),
        );
    }
}