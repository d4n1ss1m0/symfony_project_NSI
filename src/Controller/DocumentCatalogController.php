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
{/*
    #[Route('/document', name: 'document_catalog')]
    public function index(Request $request): Response
    {
        $form = $this -> createForm(SearchDocumentFormType::class);
        $form->handleRequest($request);
        $documents=$this->getDoctrine()->getRepository(Document::class)->findAll();
        $forRender['documents'] = $documents;
        $cats=$this->getDoctrine()->getRepository(Category::class)->findAll();
        $forRender['categorys'] = $cats;
        //dd($documents);
        $forRender['form'] = $form->createView();
        return $this->render('document_catalog/index.html.twig',$forRender);
    }*/
    #[Route('/document_search', name: 'document_search')]
    public function searchForm(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        
        $form = $data['search_document_form'];
        $form['Category'] = $data['Category'];
        if(!isset($form['Tags'])){
            $form['Tags']=[];
        }
        if($form['Category']=="null"){
            $form['Category']=null;
        }   
        
        $documents = $this->getDoctrine()->getRepository(Document::class)->findAllByForm($form);
        if($documents==[]){
            return $this->render('document_catalog/nullResult.html.twig');
        }
        $forRender['documents'] = $documents;
        return $this->render('document_catalog/searchResults.html.twig',$forRender);
    }
    //?category=1&text=sdfs&tags=1-2-3-4
    #[Route('/document', name: 'document_category')]
    public function getByCategory (Request $request): Response
    {

        $request->query->get('category');
        $data['Category'] = $request->query->get('category');
        $data['Text'] = $request->query->get('text');
        if($data['Text']==null){
            $data['Text']="";
        }
        if($request->query->get('tags')==null){
            $data['Tags']=[];
        }else{
            $data['Tags'] = explode('-',$request->query->get('tags'));
        }
        $documents = $this->getDoctrine()->getRepository(Document::class)->findAllByForm($data);
        $form = $this -> createForm(SearchDocumentFormType::class);
        $forRender['documents'] = $documents;
        $cats=$this->getDoctrine()->getRepository(Category::class)->findAll();
        $forRender['categorys'] = $cats;
        //dd($documents);
        $forRender['form'] = $form->createView();
        return $this->render('document_catalog/index.html.twig',$forRender);
    }/*
    #[Route('/document/{cat}/{text}/{tags}', name: 'document_category')]
    public function getByAtribute (Request $request,$cat,$text,$tags): Response
    {
        $form = $this -> createForm(SearchDocumentFormType::class);
        $form->handleRequest($request);
        $cat = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('name' => $cat));
        $documents=$cat->getDocuments();
        $forRender['documents'] = $documents;
        $cats=$this->getDoctrine()->getRepository(Category::class)->findAll();
        $forRender['categorys'] = $cats;
        //dd($documents);
        $forRender['form'] = $form->createView();
        return $this->render('document_catalog/index.html.twig',$forRender);
    }*/


    






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
