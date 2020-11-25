<?php

namespace App\Security;

use App\Entity\Document;
use App\Entity\Folder;
use App\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DocumentGuard
 *
 * @package App\Security
 */
class DocumentGuard
{
    /**
     * Checks if the user can download the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     * @throws UnauthorizedException
     */
    public function isGrantedToDownload(Document $document, UserInterface $user = null)
    {
        if (!$this->canDownload($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the user can remove the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRemove(Document $document, UserInterface $user = null)
    {
        if (!$this->canRemove($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the user can upload the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToUpload(Document $document, UserInterface $user = null)
    {
        if (!$this->canUpload($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the use can clear the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToClear(Document $document, UserInterface $user = null)
    {
        if (!$this->canClear($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the use can rename the document.
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRename(Document $document, UserInterface $user = null)
    {
        if (!$this->canRename($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Can the user remove the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRemove(Document $document, UserInterface $user = null): bool
    {
        if ($document->getFolder()->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null && $document->getFolder()->getOwnerEmail() === $user->getEmail()) {
            return true;
        }

        return false;
    }

    /**
     * Can the user upload the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canUpload(Document $document, UserInterface $user = null): bool
    {
        if ($document->getFolder()->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            $document->getState() === Document::UPLOADED_STATE &&
            ($document->getFolder()->getOwnerEmail() === $user->getEmail() ||
                $document->getOwnerEmail() === $user->getEmail())) {
            return true;
        }

        if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            ($document->getFolder()->getUserEmail() === $user->getEmail() ||
                $document->getFolder()->getOwnerEmail() === $user->getEmail()) &&
            $document->getState() === Document::EMPTY_STATE) {
            return true;
        }

        return false;
    }

    /**
     * Can the user download the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canDownload(Document $document, UserInterface $user = null): bool
    {
        if ($document->getFolder()->getStatus() === Folder::STATUS_CREATING &&
            $document->getState() === Document::UPLOADED_STATE) {
            return true;
        }

        if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $document->getState() === Document::UPLOADED_STATE &&
            $user !== null &&
            ($document->getFolder()->getOwnerEmail() === $user->getEmail() ||
                $document->getFolder()->getUserEmail() === $user->getEmail()
            )) {
            return true;
        }

        return false;
    }

    /**
     * Can the user clear the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canClear(Document $document, UserInterface $user = null): bool
    {
        if ($document->getFolder()->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            $document->getState() === Document::UPLOADED_STATE &&
            ($document->getFolder()->getOwnerEmail() === $user->getEmail() ||
                $document->getOwnerEmail() === $user->getEmail())) {
            return true;
        }

        if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            ($document->getFolder()->getUserEmail() === $user->getEmail() ||
                $document->getFolder()->getOwnerEmail() === $user->getEmail()) &&
            $document->getState() === Document::EMPTY_STATE) {
            return true;
        }

        return false;
    }

    /**
     * Can the user rename the document ?
     *
     * @param Document $document
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRename(Document $document, UserInterface $user = null): bool
    {
        if ($document->getFolder()->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            $document->getFolder()->getOwnerEmail() === $user->getEmail()) {
            return true;
        }

        return false;
    }
}