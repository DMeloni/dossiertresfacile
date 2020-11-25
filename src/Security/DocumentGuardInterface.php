<?php

namespace App\Security;

use App\Entity\Document;
use App\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface DocumentGuardInterface
 *
 * @package App\Security
 */
interface DocumentGuardInterface
{
    /**
     * Checks if the user can download the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     * @throws UnauthorizedException
     */
    public function isGrantedToDownload(Document $document, UserInterface $user = null);

    /**
     * Checks if the user can remove the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRemove(Document $document, UserInterface $user = null);

    /**
     * Checks if the user can upload the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToUpload(Document $document, UserInterface $user = null);

    /**
     * Checks if the use can clear the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToClear(Document $document, UserInterface $user = null);

    /**
     * Checks if the use can rename the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRename(Document $document, UserInterface $user = null);

    /**
     * Can the user remove the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRemove(Document $document, UserInterface $user = null): bool;

    /**
     * Can the user upload the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canUpload(Document $document, UserInterface $user = null): bool;

    /**
     * Can the user download the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canDownload(Document $document, UserInterface $user = null): bool;

    /**
     * Can the user clear the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canClear(Document $document, UserInterface $user = null): bool;

    /**
     * Can the user rename the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRename(Document $document, UserInterface $user = null): bool;
}