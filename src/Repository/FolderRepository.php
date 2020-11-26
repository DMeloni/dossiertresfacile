<?php

namespace App\Repository;

use App\Entity\{Document, Folder, User};
use App\Exception\NotFoundEntityException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FolderRepository
 *
 * @package App\Repository
 */
class FolderRepository extends ServiceEntityRepository
{
    /**
     * FolderRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    /**
     * Gets the folder from its id.
     *
     * @param int $folderId
     *
     * @return Folder
     *
     * @throws NotFoundEntityException
     */
    public function getFromId($folderId): Folder
    {
        $folder = $this->findOneById($folderId);

        if (!$folder instanceof Folder || $folder->getStatus() === Folder::STATUS_DELETED) {
            throw new NotFoundEntityException();
        }

        return $folder;
    }

    /**
     * Saves the folder.
     *
     * @param Folder $folder
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function saveFolder(Folder $folder): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($folder);
        $entityManager->flush();
        $entityManager->clear();
    }

    /**
     * Duplicates the first folder found in a specific category.
     *
     * @param string $folderCategory
     * @param UserInterface|null $user
     *
     * @return Folder
     */
    public function duplicateFromFolderCategory(string $folderCategory, ?UserInterface $user): Folder
    {
        return $this->duplicate($this->findOneBy(['category' => $folderCategory]), $user);
    }

    /**
     * Duplicates the folder from its id.
     *
     * @param int $folderId
     * @param UserInterface|null $user
     *
     * @return Folder
     */
    public function duplicateFromFolderId(int $folderId, ?UserInterface $user): Folder
    {
        return $this->duplicate($this->findOneBy(['id' => $folderId]), $user);
    }

    /**
     * Duplicates the folder.
     *
     * @param Folder $folderToDuplicate
     * @param User|null $user
     *
     * @return Folder
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function duplicate(Folder $folderToDuplicate, ?User $user): Folder
    {
        $em = $this->getEntityManager();

        if ($user !== null) {
            $ownerEmail = $user->getEmail();
        } else {
            $ownerEmail = '';
        }

        $duplicatedFolder = (new Folder())
            ->setName($folderToDuplicate->getName())
            ->setCategory($folderToDuplicate->getCategory())
            ->setOwnerEmail($ownerEmail)
            ->setIsStandard(false);

        $em->getConnection()->beginTransaction();
        $documents = [];
        foreach ($folderToDuplicate->getDocuments() as $documentToDuplicate) {
            $duplicatedDocument = (new Document())
                ->setName($documentToDuplicate->getName())
                ->setContent($documentToDuplicate->getContent())
                ->setState($documentToDuplicate->getState())
                ->setFolder($duplicatedFolder)
                ->setOwnerEmail($ownerEmail)
            ;
            $documents[] = $duplicatedDocument;
            $em->persist($duplicatedDocument);
        }
        $duplicatedFolder->setDocuments($documents);
        $em->persist($duplicatedFolder);
        $em->getConnection()->commit();
        $em->flush();
        $em->clear();

        return $duplicatedFolder;
    }
}