<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tags;
use App\Form\AddTagFormType;
//use App\Form\EditUserFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TagsCrudController extends AbstractController
{
    #[Route('/admin_panel/tag', name: 'tags_crud')]
    public function index(): Response
    {
        $tags=$this->getDoctrine()->getRepository(Tags::class)->findAll();
        $forRender['tags'] = $tags;
        return $this->render('tags_crud/index.html.twig',$forRender);
    }
    #[Route('/admin_panel/add_tag', name: 'add_tag_crud')]
    public function addTag(Request $request, UserPasswordHasherInterface $passwordHasher):Response
    {
        $tag = new Tags();
        $form = $this -> createForm(AddTagFormType::class, $tag);
        $em  = $this -> getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {           
            $em->persist($tag);
            $em->flush();
            return $this -> redirect('/');
       }
       $forRender['form'] = $form->createView();
       return $this->render('tags_crud/addTag.html.twig',$forRender);
    }

    #[Route('/admin_panel/delete_tag/{id}', name: 'delete_tag_crud')]
    public function deleteTag(int $id){
        $em = $this->getDoctrine()->getManager();
        $tag = $em-> getRepository(Tags::class)->findOneBy(['id' => $id]);
        $em->remove($tag);
        $em->flush();
    }
    

}
