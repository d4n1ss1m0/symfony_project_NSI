<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Category;
use App\Form\AddCategoryFormType;
use App\Form\EditCategoryFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryCrudController extends AbstractController
{
    #[Route('/admin_panel/category', name: 'category_crud')]
    public function index(): Response
    {
        $cats=$this->getDoctrine()->getRepository(Category::class)->findAll();
        $forRender['cats'] = $cats;
        return $this->render('category_crud/index.html.twig',$forRender);
    }
    #[Route('/admin_panel/add_cat', name: 'add_cat_crud')]
    public function addTag(Request $request):Response
    {
        $cat = new Category();
        $form = $this -> createForm(AddCategoryFormType::class, $cat);
        $em  = $this -> getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {    
            if($em -> getRepository(Category::class)->findOneBy(array('name' => $user->getName())))
            {
                return new Response("Ошибка: Категория с таким именем уже существует!",409);
            }         
            $em->persist($cat);
            $em->flush();
            return new Response("Успех: Категория добавлена",200);
       }
       $forRender['form'] = $form->createView();
       return $this->render('category_crud/addCat.html.twig',$forRender);
    }

    #[Route('/admin_panel/delete_cat/{id}', name: 'delete_cat_crud')]
    public function deleteCat(int $id){
        $em = $this->getDoctrine()->getManager();
        $cat = $em -> getRepository(Category::class)->findOneBy(array('id' => $id));
        if(!$cat){
            return new Response("Ошибка: Удаляемая категория не найдена!", 404);
        }
        $em->remove($cat);
        $em->flush();
        return new Response("Успех: Категория удалена",200);
    }

    #[Route('/admin_panel/edit_cat/{id}', name: 'edit_cat_crud')]
    public function editCat(Request $request, $id):Response
    {
        $cat = new Category();

        $em  = $this -> getDoctrine()->getManager();
        $cat = $em -> getRepository(Category::class)->findOneBy(array('id' => $id));
        $form = $this -> createForm(EditCategoryFormType::class, $cat);
        $forRender['cat'] = $cat;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {     
            if($cat){
                if($em -> getRepository(Category::class)->findOneBy(array('name' => $cat->getName())))
                {
                    return new Response("Ошибка: Категория с таким названием уже существует!",409);
                }
                    $em->persist($cat);
                    $em->flush();
                    return new Response("Успех: Категория изменена",200);
            }
            else{
                return new Response('Ошибка: Категория не найдена',404);
            }            
        }
        $forRender['form'] = $form->createView();
        return $this->render('category_crud/editCat.html.twig',$forRender);
    }
}
