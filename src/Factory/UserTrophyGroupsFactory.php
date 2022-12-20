<?php
namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Model\Trophy\UserTrophyGroup;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Iterator\UserTrophyGroupsIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyGroup\TrophyTypeFilter;

class UserTrophyGroupsFactory extends Api implements \IteratorAggregate, FactoryInterface
{
    private array $certainTrophyTypeFilter = [];

    public function __construct(private UserTrophyTitle $title)
    {
    }





}