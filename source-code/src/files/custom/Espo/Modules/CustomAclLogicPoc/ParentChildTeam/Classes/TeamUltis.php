<?php

namespace Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Classes;

use Espo\ORM\EntityManager;
use Espo\Core\Utils\Log;
use Espo\Core\Exceptions\Error;

class TeamUltis
{
    public const TEAM_ENTITY_NAME = 'Team';
    public const TEAM_CHILD_TEAMS_FIELD_NAME = 'childTeams';
    public const TEAM_PARENT_TEAMS_FIELD_NAME = 'parent';
    private array $teamIds = [];
    private int $maxLevel = 10;
    private int $currentLevel = 0;

    public function __construct(private EntityManager $entityManager, private Log $log)
    {
    }

    public function getAllChildTeamsByTeam(string $teamID, int $maxLevel = 10): array
    {
        $entityManager = $this->entityManager;
        $team = $entityManager->getEntityById(self::TEAM_ENTITY_NAME, $teamID);
        $this->buildChildTeamData($team);
        $this->maxLevel = $maxLevel;

        return $this->teamIds;
    }

    private function buildChildTeamData($team)
    {
        $this->currentLevel++;
        if ($this->currentLevel > $this->maxLevel) {
            $this->throwMaxLevelError($team);
            return;
        }
        $childTeams = $team->get(self::TEAM_CHILD_TEAMS_FIELD_NAME);
        while ($childTeams->valid()) {
            array_push($this->teamIds, $childTeams->current()->getId());
            $team = $childTeams->current();
            $this->buildChildTeamData($team);
            $childTeams->next();
        }
    }

    private function throwMaxLevelError($team)
    {
        $duplicateTeamIds = $this->getDuplicateTeamIds($this->teamIds);
        if (count($duplicateTeamIds) > 0) {
            $this->log->error("There is Duplicate Teams Id, may be loop in team tree: " . json_encode($duplicateTeamIds));
        }
        throw new Error("Max level {$this->maxLevel} reached when get child teams of team {$team->getId()}");
    }

    private function getDuplicateTeamIds(array $teamIds): array
    {
        return array_unique(array_diff_assoc($teamIds, array_unique($teamIds)));
    }

    public function getAllParentTeamsByTeam(string $teamID, int $maxLevel = 10): array
    {
        $entityManager = $this->entityManager;
        $team = $entityManager->getEntityById(self::TEAM_ENTITY_NAME, $teamID);
        $this->buildParentTeamData($team);
        $this->maxLevel = $maxLevel;

        return $this->teamIds;
    }

    private function buildParentTeamData($team)
    {
        $this->currentLevel++;
        if ($this->currentLevel > $this->maxLevel) {
            $this->throwMaxLevelError($team);
            return;
        }
        $parentTeam = $team->get(self::TEAM_PARENT_TEAMS_FIELD_NAME);
        if ($parentTeam != null) {
            array_push($this->teamIds, $parentTeam->getId());
            $this->buildParentTeamData($parentTeam);
        }
    }
}
