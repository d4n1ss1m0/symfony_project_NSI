<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tags;
use App\Form\AddTagFormType;
use App\Form\EditTagFormType;
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
            if($em -> getRepository(Tags::class)->findOneBy(array('name' => $tag->getName())))
            {
                return new Response("Ошибка: Тэг с таким именем уже существует!",409);
            }          
            $em->persist($tag);
            $em->flush();
            return new Response("Успех: Тэг добавлен",200);
       }
       $forRender['form'] = $form->createView();
       return $this->render('tags_crud/addTag.html.twig',$forRender);
    }

    #[Route('/admin_panel/delete_tag/{id}', name: 'delete_tag_crud')]
    public function deleteTag(int $id){
        $em = $this->getDoctrine()->getManager();
        $tag = $em-> getRepository(Tags::class)->findOneBy(['id' => $id]);
        if(!$tag){
            return new Response("Ошибка: Удаляемый тэг не найден!", 404);
        }
        $em->remove($tag);
        $em->flush();
        return new Response("Успех: Тэг удален",200);
    }
    #[Route('/admin_panel/edit_tag/{id}', name: 'edit_tag_crud')]
    public function editTag(Request $request, $id):Response
    {
        $tag = new Tags();

        $em  = $this -> getDoctrine()->getManager();
        $tag = $em -> getRepository(Tags::class)->findOneBy(array('id' => $id));
        $form = $this -> createForm(EditTagFormType::class, $tag);
        $forRender['tag'] = $tag;
        $tag_name = $tag->getName();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {     
            if($tag){
                if($tag_name != $tag->getName() && $em -> getRepository(Tags::class)->findOneBy(array('name' => $tag->getName())))
                {
                    return new Response("Ошибка: Тэг с таким названием уже существует!",409);
                }
                    $em->persist($tag);
                    $em->flush();
                    return new Response("Успех: Тэг изменен",200);
            }
            else{
                return new Response('Ошибка: Тэг не найден',404);
            }            
        }
        $forRender['form'] = $form->createView();
        return $this->render('tags_crud/editTag.html.twig',$forRender);
    }

}
