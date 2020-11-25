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
class FolderGuard
{
    /**
     * Checks if the user can create a new document in the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToCreateDocument(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canCreateDocument($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the user can send the folder to an another user.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     * @throws UnauthorizedException
     */
    public function isGrantedToSendFolderToUser(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the user can rename the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRename(Folder $folder, UserInterface $user = null)
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
    public function isGrantedToRemove(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Checks if the user can show some information about the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToShow(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canShow($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Can the user show some information about the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canShow(Folder $folder, UserInterface $user = null): bool
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
     * Can the user create a new document in the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canCreateDocument(Folder $folder, UserInterface $user = null): bool
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
     * Can the user send the folder?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canSendFolderToUser(Folder $folder, UserInterface $user = null): bool
    {
        if ($folder->getStatus() === Folder::STATUS_CREATING && count($folder->getDocuments()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Can the user rename the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRename(Folder $folder, UserInterface $user = null): bool
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
     * Can the user remove the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRemove(Folder $folder, UserInterface $user = null): bool
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