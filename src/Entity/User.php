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
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @var string[]
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $company;

    /**
     * @ORM\ManyToMany(targetEntity="Folder", inversedBy="owners")
     * @ORM\JoinTable(name="owners_folders")
     */
    private $ownedFolders;

    /**
     * @ORM\ManyToMany(targetEntity="Folder", inversedBy="users")
     * @ORM\JoinTable(name="users_folders")
     */
    private $usedFolders;

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
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     *
     * @return User
     */
    public function setCompany(string $company): self
    {
        $this->company = $company;
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

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