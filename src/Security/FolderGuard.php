<?php

namespace App\Security;

use App\Entity\Document;
use App\Entity\Folder;
use App\Entity\User;
use App\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;

class FolderGuard
{
    public function isGrantedToCreateDocument(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canCreateDocument($folder, $user)) {
            throw new UnauthorizedException();
        }
    }
    public function isGrantedToSendFolderToUser(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    public function isGrantedToRename(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    public function isGrantedToRemove(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canSendFolderToUser($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    public function isGrantedToShow(Folder $folder, UserInterface $user = null)
    {
        if (!$this->canShow($folder, $user)) {
            throw new UnauthorizedException();
        }
    }

    public function canShow(Folder $folder, UserInterface $user = null)
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

    public function canCreateDocument(Folder $folder, UserInterface $user = null)
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

    public function canSendFolderToUser(Folder $folder, UserInterface $user = null)
    {
        if ($folder->getStatus() === Folder::STATUS_CREATING && count($folder->getDocuments()) > 0) {
            return true;
        }

        return false;
    }

    public function canRename(Folder $folder, UserInterface $user = null)
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

    public function canRemove(Folder $folder, UserInterface $user = null)
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