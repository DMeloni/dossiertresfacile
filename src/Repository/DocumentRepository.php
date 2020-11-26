<?php

namespace App\Repository;

use App\Entity\Document;
use App\Exception\NotFoundEntityException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class DocumentRepository
 *
 * @package App\Repository
 */
class DocumentRepository extends ServiceEntityRepository
{
    /**
     * DocumentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * Renames the document.
     *
     * @param Document $document
     * @param string $name
     */
    public function rename(Document $document, string $name): void
    {
        $document->setName($name);
        $this->saveDocument($document);
    }

    /**
     * Returns the document from its id.
     *
     * @param int $id
     * @return Document
     *
     * @throws NotFoundEntityException
     */
    public function getFromId(int $id): Document
    {
        $document = $this->findOneBy(['id' => $id]);

        if (!$document instanceof Document) {
            throw new NotFoundEntityException();
        }

        return $document;
    }

    /**
     * Saves the document.
     *
     * @param Document $document
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function saveDocument(Document $document): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($document);
        $entityManager->flush();
        $entityManager->clear();
    }

    /**
     * Clears the document.
     *
     * @param Document $document
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function clearDocument(Document $document): void
    {
        $document->setOwnerEmail(null);
        $document->setState(Document::EMPTY_STATE);
        $document->setContent('');
        $this->saveDocument($document);
    }

    /**
     * Removes the document.
     *
     * @param Document $document
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function removeDocument(Document $document): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($document);
        $entityManager->flush();
        $entityManager->clear();
    }
}