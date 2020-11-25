<?php

namespace App\Controller\Ajax;

use App\Entity\Document;
use App\Entity\Folder;
use App\Exception\InternalErrorException;
use App\Exception\NotFoundEntityException;
use App\Exception\UnauthorizedException;
use App\Repository\DocumentRepository;
use App\Repository\FolderRepository;
use App\Security\DocumentGuard;
use App\Security\FolderGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class FolderAjaxController
 *
 * @package App\Controller\Ajax
 */
class FolderAjaxController extends AbstractController
{
    private FolderRepository $folderRepository;
    private DocumentRepository $documentRepository;
    private FolderGuard $folderGuard;
    private DocumentGuard $documentGuard;

    /**
     * FolderAjaxController constructor.
     *
     * @param DocumentRepository $documentRepository
     * @param FolderRepository $folderRepository
     * @param FolderGuard $folderGuard
     * @param DocumentGuard $documentGuard
     */
    public function __construct(DocumentRepository $documentRepository,
                                FolderRepository $folderRepository,
                                FolderGuard $folderGuard,
                                DocumentGuard $documentGuard
    )
    {
        $this->documentRepository = $documentRepository;
        $this->folderRepository = $folderRepository;
        $this->folderGuard = $folderGuard;
        $this->documentGuard = $documentGuard;
    }

    /**
     * Returns the folders of the user as a contributor or a manager.
     *
     * @return JsonResponse
     */
    public function getFolders(): JsonResponse
    {
        $folderRepository = $this->folderRepository;
        $currentUser = $this->getUser();

        $foldersToReturn = [];
        if ($currentUser !== null) {
            $ownersFolders = $folderRepository->findBy(['ownerEmail' => $currentUser->getEmail()]);
            $userFolders = $folderRepository->findBy(['userEmail' => $currentUser->getEmail()]);
            $folders = [...$ownersFolders, ...$userFolders];

            foreach ($folders as $folder) {
                if ($folder->getStatus() !== Folder::STATUS_DELETED) {
                    $foldersToReturn[] = [
                        'id' => $folder->getId(),
                        'name' => $folder->getName(),
                        'status' => $folder->getStatus(),
                        'isOwned' => $folder->getOwnerEmail() === $currentUser->getEmail(),
                    ];
                }
            }
        }

        return $this->json($foldersToReturn);
    }

    /**
     * Gets the details of a folder.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getFolderDetails(Request $request): JsonResponse
    {
        try {
            $folderRepository = $this->folderRepository;
            $folderGuard = $this->folderGuard;
            $documentGuard = $this->documentGuard;

            $folderId = $request->get('folder-id');

            $folder = $folderRepository->getFromId($folderId);
            $currentUser = $this->getUser();

            $folderToReturn = [
                'id' => $folder->getId(),
                'name' => $folder->getName(),
                'status' => $folder->getStatus(),
                'canRename' => $folderGuard->canRename($folder, $currentUser),
                'canCreateDocument' => $folderGuard->canCreateDocument($folder, $currentUser),
                'canSendFolderToUser' => $folderGuard->canSendFolderToUser($folder, $currentUser),
                'canRemove' => $folderGuard->canRemove($folder, $currentUser),
                'updatedAt' => $folder->getUpdatedAt()->format('\l\e d/m/Y à H:i'),
                'ownerEmail' => $folder->getOwnerEmail()?:'gestionnaire@gmail.com',
                'userEmail' => $folder->getUserEmail()?:'destinataire@gmail.com',
                'documents' => [],
                'role' => 'ROLE_MANAGER'
            ];

            foreach ($folder->getDocuments() as $document) {
                $folderToReturn['documents'][] = [
                    'id' => $document->getId(),
                    'name' => $document->getName(),
                    'status' => $document->getState(),
                    'uploader' => $document->getOwnerEmail(),
                    'canRemove' => $documentGuard->canRemove($document, $currentUser),
                    'canUpload' => $documentGuard->canUpload($document, $currentUser),
                    'canDownload' => $documentGuard->canDownload($document, $currentUser),
                    'canClear' => $documentGuard->canClear($document, $currentUser),
                    'canRename' => $documentGuard->canRename($document, $currentUser),
                    'updatedAt' => $folder->getUpdatedAt()->format('\l\e d/m/Y à H:i'),
                ];
            }

            return $this->json($folderToReturn);
        } catch (NotFoundEntityException $e) {
            return $e->toJsonResponse();
        }
    }

    /**
     * Shares the folder to an another user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        try {
            $folderRepository = $this->folderRepository;

            $folder = $folderRepository->findOneBy(['id' => $request->get('folder-id')]);
            $ownerEmail = $request->get('owner-email');
            $userEmail = $request->get('user-email');

            if (!$folder instanceof Folder) {
                return $this->json(['error' => [
                    'message' => sprintf('The resource folder with id %s is not found', $request->get('folder-id')),
                    'code' => JsonResponse::HTTP_NOT_FOUND,
                ]], JsonResponse::HTTP_NOT_FOUND);
            }

            $folder->setOwnerEmail($ownerEmail);
            $folder->setUserEmail($userEmail);
            $folder->setStatus(Folder::STATUS_IN_PROGRESS);
            foreach ($folder->getDocuments() as $document) {
                $document->setOwnerEmail($ownerEmail);
            }

            // The documents of the folder are saved too with the cascading operation.
            $folderRepository->saveFolder($folder);

            return $this->json(['id' => $request->get('folder-id')], JsonResponse::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return InternalErrorException::toJsonResponse();
        }
    }

    /**
     * Creates the document in the folder.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createDocumentToFolder(Request $request)
    {
        try {
            $folderRepository = $this->folderRepository;
            $documentRepository = $this->documentRepository;

            $folder = $folderRepository->getFromId($request->get('folder-id'));

            $document = (new Document())
                ->setName('Nouveau document')
                ->setFolder($folder);

            $documentRepository->saveDocument($document);

            return $this->json([
                'document-id' => $document->getId()
            ], Response::HTTP_CREATED);
        } catch (NotFoundEntityException $e) {
            return $e->toJsonResponse();
        } catch (\Exception $e) {
            return InternalErrorException::toJsonResponse();
        }
    }

    /**
     * Changes the name of the folder.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeNameOfFolder(Request $request): JsonResponse
    {
        $folderRepository = $this->folderRepository;
        $folderGuard = $this->folderGuard;

        $currentUser = $this->getUser();
        $folderId = $request->get('folder-id');

        try {
            $folder = $folderRepository->getFromId($folderId);
            $folderGuard->isGrantedToRename($folder, $currentUser);

            $folder->setName($request->get('folder-name'));
            $folderRepository->saveFolder($folder);

            return $this->json(['id' => $folderId], JsonResponse::HTTP_ACCEPTED);
        } catch (NotFoundEntityException $e) {
            return $e->toJsonResponse();
        } catch (\Exception $e) {
            return InternalErrorException::toJsonResponse();
        }
    }

    /**
     * Changes the name of the document.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeNameOfDocument(Request $request): JsonResponse
    {
        $documentRepository = $this->documentRepository;
        $documentGuard = $this->documentGuard;

        $documentId = $request->get('document-id');
        $documentName = $request->get('document-name');
        $currentUser = $this->getUser();

        try {
            $document = $documentRepository->getFromId($documentId);
            $documentGuard->isGrantedToRename($document, $currentUser);
            $documentRepository->rename($document, $documentName);

            return $this->json(['id' => $documentId], JsonResponse::HTTP_ACCEPTED);
        } catch (NotFoundEntityException | UnauthorizedException $e) {
            return $e->toJsonResponse();
        } catch (\Exception $e) {
            return InternalErrorException::toJsonResponse();
        }
    }

    /**
     * Clears the document.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function clearDocument(Request $request): JsonResponse
    {
        $documentRepository = $this->documentRepository;
        $documentGuard = $this->documentGuard;
        $currentUser = $this->getUser();
        $documentId = $request->get('document-id');

        try {
            $document = $documentRepository->getFromId($documentId);
            $documentGuard->isGrantedToClear($document, $currentUser);
            $documentRepository->clearDocument($document);

            return $this->json(['id' => $documentId], JsonResponse::HTTP_ACCEPTED);
        } catch (NotFoundEntityException | UnauthorizedException $e) {
            return $e->toJsonResponse();
        } catch (\Exception $e) {
            return InternalErrorException::toJsonResponse();
        }
    }

    /**
     * Removes the document from the folder.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeDocumentFromFolder(Request $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $documentRepository = $this->documentRepository;
        $documentGuard = $this->documentGuard;
        $documentId = $request->get('document-id');

        try {
            $document = $documentRepository->getFromId($documentId);
            $documentGuard->isGrantedToRemove($document, $currentUser);
            $documentRepository->removeDocument($document);

            return $this->json([], JsonResponse::HTTP_ACCEPTED);
        } catch (NotFoundEntityException | UnauthorizedException $e) {
            return $e->toJsonResponse();
        } catch (\Exception $e) {
            return InternalErrorException::toJsonResponse();
        }
    }

    /**
     * Uploads the document.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $documentRepository = $this->documentRepository;
        $folderGuard = $this->folderGuard;
        $documentId = $request->get('document-id');

        $currentUser = $this->getUser();
        try {
            $document = $documentRepository->getFromId($documentId);
            $folderGuard->isGrantedToShow($document->getFolder(), $currentUser);

            /** @var UploadedFile */
            $uploadedFile = $request->files->get('fichier');

            $targetPath = '/tmp/' . $uploadedFile->getClientOriginalName();
            $document->setContent($targetPath);
            $document->setState(Document::UPLOADED_STATE);

            if ($document->getFolder()->getStatus() === Folder::STATUS_IN_PROGRESS) {
                $document->setOwnerEmail($currentUser->getEmail());
            }

            $uploadedFile->move('/tmp', $uploadedFile->getClientOriginalName());

            $documentRepository->saveDocument($document);

            return $this->json(['name' => $uploadedFile->getClientOriginalName()], JsonResponse::HTTP_CREATED);
        } catch (NotFoundEntityException | UnauthorizedException $e) {
            return $e->toJsonResponse();
        } catch (\Exception $e) {
            return InternalErrorException::toJsonResponse();
        }
    }

    /**
     * Downloads the document.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function downloadDocument(Request $request): Response
    {
        $documentRepository = $this->documentRepository;
        $documentGuard = $this->documentGuard;
        $currentUser = $this->getUser();

        try {
            $documentId = $request->get('document-id');
            $document = $documentRepository->getFromId($documentId);
            $documentGuard->isGrantedToDownload($document, $currentUser);

            $response = new BinaryFileResponse($document->getContent());
            $response->headers->set ( 'Content-Type', 'text/plain' );
            $response->setContentDisposition ( ResponseHeaderBag::DISPOSITION_ATTACHMENT, $document->getName().'.pdf' );

            return $response;
        } catch (NotFoundEntityException | UnauthorizedException $e) {
            return $e->toJsonResponse();
        }
    }
}