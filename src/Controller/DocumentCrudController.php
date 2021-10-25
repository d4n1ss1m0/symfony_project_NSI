<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Document;
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
            //работа с файлом
            // /** @var UploadedFile $File */
            $files = $form->get('fileName')->getData();
            //$File = $_FILES;
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($files) {
                $i = 0;
                $newFilename = [];
                foreach($files as $file){
                    $i++;
                //this is needed to safely include the file name as part of the URL
                //$safeFilename = $slugger->slug($originalFilename);
                $newFilename[] = $document->getName().'('.$i.').'.$file->guessExtension();
                //if( ! is_dir( $this->getParameter('directory').'/'.$document->getName() ) ) mkdir( $this->getParameter('directory').'/'.$document->getName(), 0777 );
                // Move the file to the directory where brochures are stored
                /*try {
                    $files->move(
                        $this->getParameter('directory').'/'.$document->getName().'/',
                        $newFilename
                    );
                }
                catch(FileException $e){

                }*/
                // Move the file to the directory where brochures are stored
                //move_uploaded_file( $file['tmp_name']['file'], $this->getParameter('directory').$newFilename);

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                
                }
                $document->setFileName($newFilename);
            }
           // }
            

            $em->persist($document);
            $em->flush();
            return $this -> redirect('/');
       }
       $forRender['form'] = $form->createView();
       return $this->render('document_crud/addDoc.html.twig',$forRender);
    }

    #[Route('/admin_panel/delete_doc/{id}', name: 'delete_doc_crud')]
    public function deleteDoc(int $id){
        $em = $this->getDoctrine()->getManager();
        $doc = $em -> getRepository(Document::class)->findOneBy(array('id' => $id));
        $em->remove($doc);
        $em->flush();
    }

    #[Route('/admin_panel/edit_doc/{id}', name: 'edit_doc_crud')]
    public function editUser(Request $request, $id):Response
    {
        $doc = new Document();
        //$cUser = new Users();

        $em  = $this -> getDoctrine()->getManager();
        $doc = $em -> getRepository(Document::class)->findOneBy(array('id' => $id));

        $form = $this -> createForm(EditDocumentFormType::class, $doc);

        //$passwordHasher->needsRehash($cUser,$cUser->getPassword());
        //$forRender['user'] = $cUser;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {     
            if($doc){
                    //Проверка на наличие пользователя с новым id в базе
                    /*
                    if($cUser->getId() != $user->getId()){
                        if($em -> getRepository(Users::class)->findOneBy(array('id' => $user->getId())) ==null){
                            $cUser -> setId($user -> getId());
                        }
                        else{
                            throw new Exception('Пользователь с таким id уже существует!');
                        }
                    }
                    else{
                        $cUser -> setId($user -> getId());
                    }
                    if($user -> getPassword() != null){
                        $password = $passwordHasher -> hashPassword($user,$user -> getPassword());
                        $cUser -> setPassword($password);
                    }
                    $rolus = \array_diff($user->getRoles(), ["0","1","2","ROLE_USER"]);
                    ksort($rolus);
                    sort($rolus);
                    $cUser -> setEmail(htmlspecialchars($user -> getEmail()));
                    $cUser -> setRoles($rolus);
                    $em->persist($cUser);
                    $em->flush();
                    //return $this->redirect('/admin/tables');*/
            }
            else{
                    throw new Exception('Пользователь не найден');
            }
            

            
        }
        $forRender['form'] = $form->createView();
        return $this->render('document_crud/editDoc.html.twig',$forRender);
    }

    #[Route('/admin_panel/open_doc/{doc}/{name}', name: 'open_doc_crud')]
    public function openDoc(string $doc, string $name){
        $fileName = $this->getParameter('directory').'/'.$doc.'/'.$name;
        return new BinaryFileResponse($fileName);
    }
}
