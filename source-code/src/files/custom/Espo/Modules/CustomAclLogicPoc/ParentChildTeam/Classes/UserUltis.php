<?php

namespace Espo\Modules\CustomAclLogicPoc\ParentChildTeam\Classes;

use Espo\ORM\EntityManager;
use Espo\Core\Utils\Log;
use Espo\Core\Exceptions\Error;
use Espo\Entities\User;

class UserUltis
{
    public function __construct(private EntityManager $entityManager, private Log $log)
    {
    }

    public function isUserHasRole(User $user, string $roleName): bool
    {
        $roles = $user->get('roles');
        while ($roles->valid()) {
            if ($roles->current()->get('name') === $roleName) {
                return true;
            }
            $roles->next();
        }
        return false;
    }
}
