<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Exception\NotFoundEntityException;
use App\Exception\UnauthorizedException;
use App\Repository\FolderRepository;
use App\Security\FolderGuardInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FolderController
 *
 * @package App\Controller
 */
class FolderController extends AbstractController
{
    protected FolderRepository $folderRepository;
    protected FolderGuardInterface $folderGuard;
    protected EntityManagerInterface $entityManager;

    /**
     * FolderController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FolderRepository $folderRepository
     * @param FolderGuardInterface $folderGuard
     */
    public function __construct(EntityManagerInterface $entityManager, FolderRepository $folderRepository, FolderGuardInterface $folderGuard)
    {
        $this->entityManager = $entityManager;
        $this->folderRepository = $folderRepository;
        $this->folderGuard = $folderGuard;
    }

    /**
     * Returns the current user folders page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('folder.html.twig');
    }

    /**
     * Displays the page to edit the folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $folderRepository = $this->folderRepository;
        $currentUser = $this->getUser();

        $folderId = $request->get('folder-id');
        try {
            $folder = $folderRepository->getFromId(['id' => $folderId]);
        } catch (NotFoundEntityException $e) {
            return $this->redirectToRoute('error', ['errorCode' => $e->getCode()]);
        }

        if ($folder->getStatus() === Folder::STATUS_IN_PROGRESS) {
            if ($currentUser === null) {
                return $this->redirectToRoute('app_login');
            }
            if ($currentUser->getEmail() !== $folder->getOwnerEmail() &&
                $currentUser->getEmail() !== $folder->getUserEmail()
            ) {
                return $this->redirectToRoute('error');
            }
        }

        return $this->render('folder-edition.html.twig', [
            'folder' => $folder,
        ]);
    }

    /**
     * Displays the page to edit the new folder.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $folderRepository = $this->folderRepository;
        $currentUser = $this->getUser();

        try {
            $folder = (new Folder())
                ->setName('Dossier de location')
                ->setCategory('joij')
                ->setOwnerEmail($currentUser !== null?$currentUser->getEmail():'')
                ->setIsStandard(true);

            $folderRepository->saveFolder($folder);

            return $this->redirectToRoute('editFolder', ['folder-id' => $folder->getId()]);

        } catch (\Throwable | NotFoundEntityException | UnauthorizedException $e) {
            return $this->redirectToRoute('error', ['errorCode' => $e->getCode()]);
        }
    }

    /**
     * Displays the page to edit the new folder created from a template folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createFromTemplate(Request $request): Response
    {
        $folderRepository = $this->folderRepository;
        $folderCategory = $request->get('folder-category');
        $currentUser = $this->getUser();

        $duplicatedFolder = $folderRepository->duplicateFromFolderCategory($folderCategory, $currentUser);

        return $this->redirectToRoute('editFolder', ['folder-id' => $duplicatedFolder->getId()]);
    }

    /**
     * Displays the page to edit the duplicated folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function duplicate(Request $request): Response
    {
        $folderRepository = $this->folderRepository;
        $folderId = $request->get('folder-id');
        $currentUser = $this->getUser();

        $duplicatedFolder = $folderRepository->duplicateFromFolderId($folderId, $currentUser);

        return $this->redirectToRoute('editFolder', ['folder-id' => $duplicatedFolder->getId()]);
    }

    /**
     * Displays the confirmation page of the removed folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function remove(Request $request): Response
    {
        $folderGuard = $this->folderGuard;
        $folderRepository = $this->folderRepository;
        $currentUser = $this->getUser();

        $folderId = $request->get('folder-id');

        try {
            $folder = $folderRepository->getFromId($folderId);
            $folderGuard->isGrantedToRemove($folder, $currentUser);
            $folder->setStatus(Folder::STATUS_DELETED);
            $folderRepository->saveFolder($folder);

            return $this->render('folder-removing-confirmation.twig');
        } catch (\Throwable | NotFoundEntityException | UnauthorizedException $e) {
            return $this->redirectToRoute('error', ['errorCode' => $e->getCode()]);
        }
    }
}