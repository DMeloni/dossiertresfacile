<?php

namespace App\Security;

use App\Entity\{Document, Folder};
use App\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DocumentGuard
 *
 * @package App\Security
 */
class DocumentGuard implements DocumentGuardInterface
{
    /**
     * @inheritDoc
     */
    public function isGrantedToDownload(Document $document, ?UserInterface $user)
    {
        if (!$this->canDownload($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function isGrantedToRemove(Document $document, ?UserInterface $user)
    {
        if (!$this->canRemove($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function isGrantedToUpload(Document $document, ?UserInterface $user)
    {
        if (!$this->canUpload($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function isGrantedToClear(Document $document, ?UserInterface $user)
    {
        if (!$this->canClear($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function isGrantedToRename(Document $document, ?UserInterface $user)
    {
        if (!$this->canRename($document, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function canRemove(Document $document, ?UserInterface $user): bool
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
     * @inheritDoc
     */
    public function canUpload(Document $document, ?UserInterface $user): bool
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
     * @inheritDoc
     */
    public function canDownload(Document $document, ?UserInterface $user): bool
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
     * @inheritDoc
     */
    public function canClear(Document $document, ?UserInterface $user): bool
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
     * @inheritDoc
     */
    public function canRename(Document $document, ?UserInterface $user): bool
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