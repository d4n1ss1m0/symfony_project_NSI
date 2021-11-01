<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Document;
use App\Entity\Tags;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\AddDocumentFormType;
use App\Form\EditDocumentFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentCrudController extends AbstractController
{
    #[Route('/admin_panel/document', name: 'document_crud')]
    public function index(): Response
    {
        $documents=$this->getDoctrine()->getRepository(Document::class)->findAll();
        $forRender['documents'] = $documents;
        return $this->render('document_crud/index.html.twig',$forRender);
    }

    #[Route('/admin_panel/add_doc', name: 'add_doc_crud')]
    public function addDoc(Request $request, UserPasswordHasherInterface $passwordHasher):Response
    {
        $document = new Document();
        $form = $this -> createForm(AddDocumentFormType::class, $document);
        $em  = $this -> getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($em -> getRepository(Document::class)->findOneBy(array('name' => $document->getName())))
            {
                return new Response("Ошибка: Документ с таким именем уже существует!",409);
            }      
            //работа с файлом
            $files = $form->get('fileName')->getData();
            if ($files) {
                $i = 0;
                $newFilename = [];
                foreach($files as $file){
                    $i++;
                    $newFilename[] = $document->getName().'('.$i.').'.$file->guessExtension();
                    try {
                        $file->move(
                            $this->getParameter('directory').'/'.$document->getName().'/',
                            $newFilename[count($newFilename)-1]
                        );
                    }
                    catch(FileException $e){

                    }
                             
                }
                $document->setFileName($newFilename);
            }
            

            $em->persist($document);
            $em->flush();
            return new Response("Успех: Документ добавлен",200);
       }
       $forRender['form'] = $form->createView();
       return $this->render('document_crud/addDoc.html.twig',$forRender);
    }

    #[Route('/admin_panel/delete_doc/{id}', name: 'delete_doc_crud')]
    public function deleteDoc(int $id){
        $em = $this->getDoctrine()->getManager();
        $doc = $em -> getRepository(Document::class)->findOneBy(array('id' => $id));
        if(!$doc){
            return new Response("Ошибка: Удаляемый документ не найден!", 404);
        }
        $folder = $this->getParameter('directory').'/'.$doc->getName(); // имя новой папки
        if ($files = glob($folder . "/*")) {
            // удаляем по одному
            foreach($files as $file) {
                    // если попался файл
                    unlink($file);
                
            }
        }
        // удаляем пустую папку
        rmdir($folder);
        $em->remove($doc);
        $em->flush();
        return new Response("Успех: Документ удален",200);
    }

    #[Route('/admin_panel/edit_doc/{id}', name: 'edit_doc_crud')]
    public function editUser(Request $request, $id):Response
    {
        $doc = new Document();

        $em  = $this -> getDoctrine()->getManager();
        $doc = $em -> getRepository(Document::class)->findOneBy(array('id' => $id));
        $docFiles = $doc->getFileName();
        $docName = $doc->getName();
        $form = $this -> createForm(EditDocumentFormType::class, $doc);
        $forRender['doc'] = $doc;
        $doc_name = $doc->getName();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {    
            if($doc){
                if($doc_name != $doc->getName() && $em -> getRepository(Document::class)->findOneBy(array('name' => $doc->getName())))
                {
                    return new Response("Ошибка: Документ с таким названием уже существует!",409);
                }
                //работа с файлом
                //Переименовываем папку и файлы внутри неё
                if($docName != $doc->getName()){
                    rename($this->getParameter('directory').'/'.$docName, $this->getParameter('directory').'/'.$doc->getName() );
                    $dirFiles = scandir($this->getParameter('directory').'/'.$doc->getName());
                    $docFiles = [];
                    for($c = 1; $c < count($dirFiles)-1; $c++){
                        $docFiles[] =  $doc->getName().'('.$c.').pdf';
                        rename($this->getParameter('directory').'/'.$doc->getName().'/'.$dirFiles[$c+1],
                                $this->getParameter('directory').'/'.$doc->getName().'/'.$doc->getName().'('.$c.').pdf' );
                    }
                }

                $files = $form->get('file')->getData();
                if ($files) {
                    $i = 0;
                    $newFilename = [];
                    //Перезапись файлов
                    if($form->get('check')->getData() == false){  
                        $dirFiles = scandir($this->getParameter('directory').'/'.$doc->getName());
                        for($c = 2; $c < count($dirFiles); $c++){
                            
                            $newFilename[] = $dirFiles[$c];
                            $i++;
                        }                     
                    }
                    foreach($files as $file){                       
                        $i++;
                        $newFilename[] = $doc->getName().'('.$i.').'.$file->guessExtension();
                            $file->move(
                                $this->getParameter('directory').'/'.$doc->getName().'/',
                                $newFilename[count($newFilename)-1]
                            );
                                
                    }
                    
                    $doc->setFileName($newFilename);
                }
                else{
                    $doc->setFileName($docFiles);
                }
                

                $em->persist($doc);
                $em->flush();
                return new Response("Успех: Документ изменен",200);
            }
            else{
                return new Response('Ошибка: Документ не найден',404);
            }
            

            
        }
        $forRender['form'] = $form->createView();
        return $this->render('document_crud/editDoc.html.twig',$forRender);
    }

    #[Route('/open_doc/{doc}/{name}', name: 'open_doc_crud')]
    public function openDoc(string $doc, string $name){
        $fileName = $this->getParameter('directory').'/'.$doc.'/'.$name;
        return new BinaryFileResponse($fileName);
    }
}
