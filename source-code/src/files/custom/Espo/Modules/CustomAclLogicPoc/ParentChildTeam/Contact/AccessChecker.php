<?php

namespace Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Contact;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\AccessEntityCREDSChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Acl\OwnershipChecker;
use Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Classes\UserUltis;

class AccessChecker implements AccessEntityCREDSChecker
{
    private DefaultAccessChecker $defaultAccessChecker;

    public function __construct(DefaultAccessChecker $defaultAccessChecker, private OwnershipChecker $ownershipChecker, private UserUltis $userUltis)
    {
        $this->defaultAccessChecker = $defaultAccessChecker;
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

    /**
     * Nếu user có role có tên là "read team contacts" thì sẽ có quyền đọc record của mình và của team
     */
    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (
            $this->userUltis->isUserHasRole($user, 'read team contacts')
        ) {
            return
                $this->ownershipChecker->checkOwn($user, $entity) ||
                $this->ownershipChecker->checkTeam($user, $entity);
        }
        return $this->ownershipChecker->checkOwn($user, $entity);

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
        return $this->defaultAccessChecker->checkEntityStream($user, $entity, $data);
    }
}
