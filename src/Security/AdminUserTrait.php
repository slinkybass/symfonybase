<?php

namespace App\Security;

use App\Entity\User;

/**
 * Admin area (`access_control` on `^/admin`) assumes the authenticated principal is {@see User}.
 */
trait AdminUserTrait
{
    /**
     * @throws \LogicException when the token is missing or not {@see User}
     */
    public function user(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('Admin expects an authenticated App\Entity\User.');
        }

        return $user;
    }
}
