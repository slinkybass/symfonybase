<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\Permission;
use App\Service\RolePermissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
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

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            $vote?->addReason('User is not authenticated.');

            return false;
        }

        $permission = Permission::normalize($attribute);
        if (!$this->rolePermissions->userHasPermission($user, $permission)) {
            $vote?->addReason(\sprintf('Role lacks permission "%s".', $permission));

            return false;
        }

        return true;
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
