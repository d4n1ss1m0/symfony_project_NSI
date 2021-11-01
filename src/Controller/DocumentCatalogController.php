<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Document;
use App\Entity\Category;
use App\Form\SearchDocumentFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMapping;

class DocumentCatalogController extends AbstractController
{
    #[Route('/document', name: 'document_catalog')]
    public function index(Request $request): Response
    {
        $form = $this -> createForm(SearchDocumentFormType::class);
        $form->handleRequest($request);
        $documents=$this->getDoctrine()->getRepository(Document::class)->findAll();
        $forRender['documents'] = $documents;
        //dd($documents);
        $forRender['form'] = $form->createView();
        return $this->render('document_catalog/index.html.twig',$forRender);
    }
    #[Route('/document/search', name: 'document_search')]
    public function searchForm(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $form = $data['search_document_form'];
        if(!isset($form['Tags'])){
            $form['Tags']=[];
        }
        if(!isset($form['Category'])){
            $form['Category']=null;
        }
        $documents = $this->getDoctrine()->getRepository(Document::class)->findAllByForm($form);
        if($documents==[]){
            return $this->render('document_catalog/nullResult.html.twig');
        }
        $forRender['documents'] = $documents;
        return $this->render('document_catalog/searchResults.html.twig',$forRender);
    }

    #[Route('/document/view/{name}', name: 'document_view')]
    public function View(Request $request, $name): Response
    {
        $em = $this->getDoctrine()->getManager();
        $doc = new Document();
        $doc = $em -> getRepository(Document::class)->findOneBy(array('name' => $name));
        $FileNames = $doc->getFileName();
        $kekl =$doc->getTags();
        for($i=0;$i<count($FileNames);$i++){
            $FileNames[$i]= $this->getParameter('directory').$name.'\\'.$FileNames[$i];
        }
        $forRender['document'] = $doc;
        $forRender['path'] = $FileNames;
        return $this->render('document_catalog/documentView.html.twig',$forRender);
    }
    
}
