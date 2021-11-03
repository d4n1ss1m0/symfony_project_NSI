<?php

namespace App\Controller;
use App\Entity\Category;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $cats=$this->getDoctrine()->getRepository(Category::class)->findAll();
        $forRender['categorys'] = $cats;
        return $this->render('homepage/index.html.twig',$forRender);
    }
}
