<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Repository\FolderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FolderController extends AbstractController
{
    /**
     * @Route("/folder/{name}", name="folder")
     */
    public function index($name, Request $request, DocumentRepository $documentRepository, FolderRepository $folderRepository, EntityManagerInterface $entityManager): Response
    {
        $username = $this->getUser()->getUserIdentifier();
        $folder = $folderRepository->findOneByName($name);
        //get all files of folder and store files uploaded in the folder
        $folderFiles = [];
        $docs = new Document();
        $formDocs = $this->createForm(DocumentType::class, $docs);
        $formDocs->handleRequest($request);
        if ($formDocs->isSubmitted() && $formDocs->isValid()) {
            $formData = $formDocs['name']->getData();
            $originalName =  $formData->getClientOriginalName();
            $extension = $formData->getClientOriginalExtension();
            $originalFileName = pathinfo($originalName, PATHINFO_FILENAME);
            $checkFolderExist  = $documentRepository->findByNameAndFolder($originalName, $folder->getId());
            if(!$checkFolderExist){
                try {
                    $formData->move($username.'/'.$name.'/', $originalName);
                } catch (FileException $e) {

                }
                //get folder by name
                $docs->setFolder($folder);
                $docs->setName($originalName);
                $docs->setExtension($extension);
                $entityManager->persist($docs);
                $entityManager->flush();
            } else {
                $error = 1;
                return $this->render('folder/index.html.twig', [
                    'folderName' => $name,
                    'formdocs' => $formDocs->createView(),
                    'userdocs' => $documentRepository->findById($folder->getId()),
                    'error' => $error
                ]);
            }

            return $this->redirectToRoute('folder', ['name' => $name]);

        }
        return $this->render('folder/index.html.twig', [
            'folderName' => $name,
            'formdocs' => $formDocs->createView(),
            'userdocs' => $documentRepository->findById($folder->getId()),
            'error' => false,
        ]);
    }
}
