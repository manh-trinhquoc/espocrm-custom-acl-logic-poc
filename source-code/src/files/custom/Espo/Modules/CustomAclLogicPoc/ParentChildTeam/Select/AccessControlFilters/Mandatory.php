<?php

namespace Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Select\AccessControlFilters;

use Espo\ORM\Query\Select;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\User;
use Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Classes\TeamUltis;
use Espo\ORM\EntityManager;

class Mandatory implements Filter
{
    public function __construct(private User $user, private EntityManager $entityManager, private TeamUltis $teamUltis)
    {
    }

    public function apply(SelectBuilder $queryBuilder): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        $currentUserId = $this->user->getId();

        $teamIds = $this->getAllChildTeamsOfUser($this->user);

        // $subQuery is the instance of Espo\ORM\Query\Select
        $subQuery = Select::fromRaw([
            'from' => 'EntityTeam',
            'select' => ['entityId'],
            'whereClause' => [
                'teamId' => $teamIds,
            ],
        ]);

        $userSubQuery = Select::fromRaw([
            'from' => 'TeamUser',
            'select' => ['userId'],
            'whereClause' => [
                'teamId' => $teamIds,
            ],
        ]);


        $queryBuilder->where(Cond::or(
            Cond::equal(Cond::column('assignedUserId'), $currentUserId),
            Cond::in(Cond::column('id'), $subQuery),
            Cond::in(Cond::column('assignedUserId'), $userSubQuery)
        ));


        // debug
        // $entityManager = $this->entityManager;
        // $build = $queryBuilder->build();
        // $pdoStatement = $entityManager
        //     ->getQueryExecutor()
        //     // ->execute($subQuery);
        //     ->execute($build);
        // // var_dump($subQuery->getWhere());
        // echo $pdoStatement->queryString;
        // die;
    }

    private function getAllChildTeamsOfUser(User $user): array
    {
        $teamIds = [];
        $teams = $user->get('teams');
        while ($teams->valid()) {
            $team = $teams->current();
            $teamId = $team->getId();
            $childTeamIds = $this->teamUltis->getAllChildTeamsByTeam($teamId);
            array_push($teamIds, $teamId, ...$childTeamIds);
            $teams->next();
        }
        $teamIds = array_unique($teamIds);
        return $teamIds;
    }
}
