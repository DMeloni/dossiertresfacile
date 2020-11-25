<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @var string
     */
    protected string $email;

    /**
     * @ORM\Column(type="json")
     * @var string[]
     */
    protected $roles = [];

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $password;

    /**
     * @ORM\ManyToMany(targetEntity="Folder", inversedBy="owners")
     * @ORM\JoinTable(name="owners_folders")
     */
    protected $ownedFolders;

    /**
     * @ORM\ManyToMany(targetEntity="Folder", inversedBy="users")
     * @ORM\JoinTable(name="users_folders")
     */
    protected $usedFolders;

    /**
     * User constructor.
     *
     * @return self
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * @return Folder[]
     */
    public function getOwnedFolders()
    {
        return $this->ownedFolders;
    }

    /**
     * @param Folder[] $ownedFolders
     *
     * @return self
     */
    public function setOwnedFolders($ownedFolders)
    {
        $this->ownedFolders = $ownedFolders;
        return $this;
    }
    /**
     * @param Folder[] $ownedFolders
     *
     * @return self
     */
    public function setUsedFolders($usedFolders)
    {
        $this->usedFolders = $usedFolders;
        return $this;
    }

    /**
     * @return Folder[]
     */
    public function getUsedFolders()
    {
        return $this->usedFolders;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     *
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}