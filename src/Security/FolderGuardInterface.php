<?php

namespace App\Security;

use App\Entity\Folder;
use App\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface FolderGuardInterface
 *
 * @package App\Security
 */
interface FolderGuardInterface
{
    /**
     * Checks if the user can create a new document in the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToCreateDocument(Folder $folder, ?UserInterface $user): void;

    /**
     * Checks if the user can send the folder to an another user.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     * @throws UnauthorizedException
     */
    public function isGrantedToSendFolderToUser(Folder $folder, ?UserInterface $user): void;

    /**
     * Checks if the user can rename the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRename(Folder $folder, ?UserInterface $user): void;

    /**
     * Checks if the user can remove the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToRemove(Folder $folder, ?UserInterface $user): void;

    /**
     * Checks if the user can show some information about the folder.
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @throws UnauthorizedException
     */
    public function isGrantedToShow(Folder $folder, ?UserInterface $user): void;

    /**
     * Can the user show some information about the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canShow(Folder $folder, ?UserInterface $user): bool;

    /**
     * Can the user create a new document in the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canCreateDocument(Folder $folder, ?UserInterface $user): bool;

    /**
     * Can the user send the folder?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canSendFolderToUser(Folder $folder, ?UserInterface $user): bool;

    /**
     * Can the user rename the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRename(Folder $folder, ?UserInterface $user): bool;

    /**
     * Can the user remove the folder ?
     *
     * @param Folder $folder
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function canRemove(Folder $folder, ?UserInterface $user): bool;
}