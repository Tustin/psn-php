<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\TrophyGroup;
use Tustin\PlayStation\Model\TrophyTitle;
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
				'trophy/v1/users/' . $this->title->getFactory()->getUser()->accountId() . '/trophyTitles/' . $this->title->npCommunicationId() .'/trophyGroups',
				[
					'serviceName' => 'trophy'
				]
			);
		}
		else
		{
			$results = $this->get(
				'trophy/v1/npCommunicationIds/' . $this->title->npCommunicationId()  . '/trophyGroups',
				[
					'npServiceName' => 'trophy'
				]
			);
		}

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
