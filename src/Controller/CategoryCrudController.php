<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Category;
use App\Form\AddCategoryFormType;
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
            $em->persist($cat);
            $em->flush();
            return $this -> redirect('/');
       }
       $forRender['form'] = $form->createView();
       return $this->render('category_crud/addCat.html.twig',$forRender);
    }

    #[Route('/admin_panel/delete_cat/{id}', name: 'delete_cat_crud')]
    public function deleteCat(int $id){
        $em = $this->getDoctrine()->getManager();
        $cat = $em -> getRepository(Category::class)->findOneBy(array('id' => $id));
        $em->remove($cat);
        $em->flush();
    }
}
