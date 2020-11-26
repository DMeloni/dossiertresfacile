<?php

namespace App\Entity;

use App\Repository\FolderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FolderRepository::class)
 */
class Folder
{
    public const STATUS_CREATING = 'creating';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DELETED = 'deleted';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    protected int $id;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected bool $isStandard = false;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    protected ?string $ownerEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    protected ?string $userEmail;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected string $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected string $status = self::STATUS_CREATING;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="folder")
     * @var Document[]
     */
    protected $documents;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Folder constructor.
     */
    public function __construct()
    {
        // we set up "created"+"modified"
        $this->setCreatedAt(new \DateTime());
        if (!$this->hasUpdatedAt()) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateModifiedDatetime(): void
    {
        // update the modified time
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @return bool
     */
    public function hasUpdatedAt(): bool
    {
        return $this->updatedAt !== null;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStandard(): bool
    {
        return $this->isStandard;
    }

    /**
     * @param bool $isStandard
     *
     * @return self
     */
    public function setIsStandard(bool $isStandard): self
    {
        $this->isStandard = $isStandard;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return self
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Document[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param Document[] $documents
     *
     * @return self
     */
    public function setDocuments($documents): self
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOwnerEmail(): ?string
    {
        return $this->ownerEmail;
    }

    /**
     * @param string|null $ownerEmail
     *
     * @return self
     */
    public function setOwnerEmail($ownerEmail): self
    {
        $this->ownerEmail = $ownerEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    /**
     * @param string|null $userEmail
     *
     * @return self
     */
    public function setUserEmail($userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }
}