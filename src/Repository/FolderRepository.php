<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Folder;
use App\Entity\User;
use App\Exception\NotFoundEntityException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class FolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    public function getFromId($folderId) : Folder
    {
        $folder = $this->findOneById($folderId);

        if (!$folder instanceof Folder || $folder->getStatus() === Folder::STATUS_DELETED) {
            throw new NotFoundEntityException();
        }

        return $folder;
    }

    public function saveFolder(Folder $folder)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($folder);
        $entityManager->flush();
        $entityManager->clear();
    }

    public function duplicateFromFolderCategory($folderId, UserInterface $user = null): Folder
    {
        return $this->duplicate($this->findOneBy(['category' => $folderId]), $user);
    }

    public function duplicateFromFolderId($folderId, UserInterface $user = null): Folder
    {
        return $this->duplicate($this->findOneBy(['id' => $folderId]), $user);
    }
    public function duplicate(Folder $folderToDuplicate, User $user = null): Folder
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