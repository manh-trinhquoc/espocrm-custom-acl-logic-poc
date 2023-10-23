<?php

namespace Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Acl;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\AccessEntityCREDSChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Table;
use Espo\Core\AclManager;

class AccessChecker implements AccessEntityCREDSChecker
{
    private DefaultAccessChecker $defaultAccessChecker;
    private AclManager $aclManager;

    public function __construct(DefaultAccessChecker $defaultAccessChecker, AclManager $aclManager)
    {
        $this->defaultAccessChecker = $defaultAccessChecker;
        $this->aclManager = $aclManager;
    }

    public function check(User $user, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->check($user, $data);
    }

    public function checkCreate(User $user, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkCreate($user, $data);
    }

    public function checkRead(User $user, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkRead($user, $data);
    }

    public function checkEdit(User $user, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkEdit($user, $data);
    }

    public function checkDelete(User $user, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkDelete($user, $data);
    }

    public function checkStream(User $user, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkStream($user, $data);
    }

    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkEntityCreate($user, $entity, $data);
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkEntityRead($user, $entity, $data);
    }

    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkEntityEdit($user, $entity, $data);
    }

    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->defaultAccessChecker->checkEntityDelete($user, $entity, $data);
    }

    public function checkEntityStream(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->aclManager->checkUserPermission($user, $entity, 'user');
        return $this->defaultAccessChecker->checkEntityStream($user, $entity, $data);
    }
}
