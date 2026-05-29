<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\Permission;
use App\Service\RolePermissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Bridges Symfony `isGranted()` checks to application permissions stored on roles.
 */
final class PermissionVoter extends Voter
{
    /** @var array<string, true>|null */
    private ?array $permissions = null;

    public function __construct(
        private readonly RolePermissions $rolePermissions,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return isset($this->permissions()[Permission::normalize($attribute)]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->rolePermissions->userHasPermission($user, Permission::normalize($attribute));
    }

    /**
     * @return array<string, true>
     */
    private function permissions(): array
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        return $this->permissions = array_fill_keys($this->rolePermissions->getPermissions(), true);
    }
}
