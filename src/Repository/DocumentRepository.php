<?php

namespace App\Repository;

use App\Entity\Document;
use App\Exception\NotFoundEntityException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * @param $id
     * @param $name
     */
    public function renameFromId($id, $name)
    {
        $document = $this->getFromId($id);

        $this->rename($document, $name);
    }

    public function rename(Document $document, string $name)
    {
        $document->setName($name);
        $this->saveDocument($document);
    }

    public function getFromId($id)
    {
        $document = $this->findOneBy(['id' => $id]);

        if (!$document instanceof Document) {
            throw new NotFoundEntityException();
        }

        return $document;
    }

    public function saveDocument(Document $document)
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($document);
        $entityManager->flush();
        $entityManager->clear();
    }

    /**
     * @param Document $document
     */
    public function clearDocument(Document $document)
    {
        $document->setOwnerEmail(null);
        $document->setState(Document::EMPTY_STATE);
        $this->saveDocument($document);
    }

    /**
     * @param Document $document
     */
    public function removeDocument(Document $document)
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($document);
        $entityManager->flush();
        $entityManager->clear();
    }
}