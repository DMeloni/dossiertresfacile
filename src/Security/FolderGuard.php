<?php

namespace App\Security;

use App\Entity\Folder;
use App\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FolderGuard
 *
 * @package App\Security
 */
class FolderGuard implements FolderGuardInterface
{
    /**
     * @inheritDoc
     */
    public function isGrantedToCreateDocument(Folder $folder, ?UserInterface $user): void
    {
        if (!$this->canCreateDocument($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function isGrantedToSendFolderToUser(Folder $folder, ?UserInterface $user): void
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function isGrantedToRename(Folder $folder, ?UserInterface $user): void
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the user can remove the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRemove(Folder $folder, ?UserInterface $user): void
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function isGrantedToShow(Folder $folder, ?UserInterface $user): void
    {
        if (!$this->canShow($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function canShow(Folder $folder, ?UserInterface $user): bool
    {
        if ($folder->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($folder->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            ($folder->getOwnerEmail() === $user->getEmail() ||
                $folder->getUserEmail() === $user->getEmail())
        ) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function canCreateDocument(Folder $folder, ?UserInterface $user): bool
    {
        if ($folder->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($folder->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            ($folder->getOwnerEmail() === $user->getEmail())) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function canSendFolderToUser(Folder $folder, ?UserInterface $user): bool
    {
        if ($folder->getStatus() === Folder::STATUS_CREATING && count($folder->getDocuments()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function canRename(Folder $folder, ?UserInterface $user): bool
    {
        if ($folder->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($folder->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            $folder->getOwnerEmail() === $user->getEmail()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function canRemove(Folder $folder, ?UserInterface $user): bool
    {
        if ($folder->getStatus() === Folder::STATUS_CREATING) {
            return true;
        }

        if ($folder->getStatus() === Folder::STATUS_IN_PROGRESS &&
            $user !== null &&
            $folder->getOwnerEmail() === $user->getEmail()) {
            return true;
        }

        return false;
    }
}