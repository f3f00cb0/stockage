<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\User;
use App\Repository\DocumentRepository;
use App\Repository\FolderRepository;
use App\Repository\UserRepository;
use App\Form\FolderType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HttpController extends AbstractController
{

    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== null){
            return $this->redirectToRoute('profile');
        }

        $filesystem = new Filesystem();
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $filesystem->mkdir(Path::normalize($user->getUserIdentifier()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('homepage/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/home", name="profile")
     */
    public function login(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, FolderRepository $folderRepository): Response
    {
        $user = $this->token->getToken()->getUser();
        $userId = $user->getId();
        $userClass = $userRepository->loadUserById($userId);
        $filesystem = new Filesystem();
        $folder = new Folder();
        $formFolder = $this->createForm(FolderType::class, $folder);
        $formFolder->handleRequest($request);
        $error = false;
        if ($formFolder->isSubmitted() && $formFolder->isValid()) {
            $filesystem->mkdir(Path::normalize($user->getUserIdentifier().'/'.$formFolder->getData()->getName()));
            $folder->setName($formFolder->getData()->getName());
            $folder->setUser($userClass);
            $checkFolderExist  = $folderRepository->findOneBy([
                'name' => $formFolder->getData()->getName(),
                'user' => $userId
            ]);
            if(!$checkFolderExist){
                $entityManager->persist($folder);
                $entityManager->flush();
                return $this->redirectToRoute('folder', ['name' => $folder->getName()]);
            } else {
                $error = 1;
            }

        }

        $folders = $folderRepository->findBy(
            ['user' =>$userId]
        );


        return $this->render('profile/index.html.twig', [
            'formfolder' => $formFolder->createView(),
            'userFolders' => $folders,
            'error' => $error
        ]);
    }

    /**
     * @Route("/documents", name="seeDocuments")
     */
    public function seeDocuments(DocumentRepository $documentRepository, FolderRepository $folderRepository): Response
    {
        $user = $this->token->getToken()->getUser();
        $userFolders = $folderRepository->findBy([
            'user' => $user->getId()
        ]);
        $userDocs = [];
        foreach ($userFolders as $folder) {
            $folderId = $folder->getId();
            $docs = $documentRepository->findBy([
                'folder' => $folder->getId()
            ]);
            foreach ($docs as $doc){
                $userDocs[] = $doc;
            }
        }
        return $this->render('profile/documents.html.twig', [
            'userdocs' => $userDocs
        ]);

    }

}
