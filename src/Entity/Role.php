<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: "role")]
#[UniqueEntity(fields: 'name', errorPath: 'displayName')]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $displayName = null;

    #[ORM\Column]
    private ?bool $isAdmin = false;

    #[ORM\Column]
    private array $permissions = [];

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'role')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->displayName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;
        if (!$this->id) {
            $roleName = $displayName;
            $roleName = preg_replace('~[^\pL\d]+~u', '', $roleName);
            $roleName = iconv('utf-8', 'us-ascii//TRANSLIT', $roleName);
            $roleName = preg_replace('~[^-\w]+~', '', $roleName);
            $roleName = strtoupper($roleName);
            $this->setName("ROLE_" . $roleName);
        }

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getPermission($name): ?bool
    {
        return $this->permissions[$name] ?? false;
    }

    public function setPermissions(array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }

        return $this;
    }
}
