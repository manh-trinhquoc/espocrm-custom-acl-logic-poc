<?php

namespace Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Acl;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Acl\OwnershipOwnChecker;
use Espo\Core\Acl\OwnershipTeamChecker;

use Espo\ORM\EntityManager;
use Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Classes\TeamUltis;
use Espo\Core\Utils\Log;
use Espo\Core\Exceptions\Error;

class OwnershipChecker implements OwnershipOwnChecker, OwnershipTeamChecker
{
    public function __construct(private EntityManager $entityManager, private TeamUltis $teamUltis)
    {
    }

    public function checkOwn(User $user, Entity $entity): bool
    {
        return $user->getId() === $entity->get('assignedUserId');
    }

    public function checkTeam(User $user, Entity $entity): bool
    {
        assert($entity instanceof CoreEntity);

        $teamIds = $this->getAllParentTeamsOfEntity($entity);

        $intersect = array_intersect(
            $user->getLinkMultipleIdList('teams'),
            $teamIds
        );

        if (count($intersect)) {
            return true;
        }

        return false;
    }

    private function getAllParentTeamsOfEntity(Entity $entity): array
    {

        $teamIds = [];
        $teams = $entity->getLinkMultipleIdList('teams');
        $assignedUser = $entity->get('assignedUser');
        if ($assignedUser != null) {
            $assignedUserTeams = $assignedUser->getLinkMultipleIdList('teams');
            $teams = array_merge($teams, $assignedUserTeams);
            $teams = array_unique($teams);
        }

        foreach($teams as $teamId) {
            $parentTeamIds = $this->teamUltis->getAllParentTeamsByTeam($teamId);
            array_push($teamIds, $teamId, ...$parentTeamIds);
        }

        $teamIds = array_unique($teamIds);

        return $teamIds;
    }


}
