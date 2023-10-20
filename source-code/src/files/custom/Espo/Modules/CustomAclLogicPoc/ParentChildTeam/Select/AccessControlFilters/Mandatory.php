<?php

namespace Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Select\AccessControlFilters;

use Espo\ORM\Query\Select;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\User;
use Espo\ORM\EntityManager;

class Mandatory implements Filter
{
    public function __construct(private User $user, private EntityManager $entityManager)
    {
    }

    public function apply(SelectBuilder $queryBuilder): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        $currentUserId = $this->user->getId();

        // $queryBuilder->where([
        //     'assignedUserId' => $this->user->getId(),
        // ]);
        // $subQuery is the instance of Espo\ORM\Query\Select
        $subQuery = Select::fromRaw([
            'from' => 'EntityTeam',
            'select' => ['entityId'],
            'whereClause' => [
                'teamId' => ['6532241c5abca9e20']  //TODO: get all child team of this user
            ],
        ]);


        $queryBuilder->where(Cond::or(
            Cond::equal(Cond::column('assignedUserId'), $currentUserId),
            Cond::in(Cond::column('id'), $subQuery)
        ));

        // $entityManager = $this->entityManager;
        // $build = $queryBuilder->build();
        // $pdoStatement = $entityManager
        //     ->getQueryExecutor()
        //     ->execute($subQuery);
        // var_dump($subQuery->getWhere());
        // echo $pdoStatement->queryString;
        // die;
    }
}
