<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Document[]    findAllByForm(string $form)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    // /**
    //  * @return Document[] Returns an array of Document objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Document
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllByForm($form){
        $q = htmlspecialchars($form['Text']);
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\Document', 'd');
        $rsm->addFieldResult('d', 'id', 'id');
        $rsm->addFieldResult('d', 'doc_name', 'name');
        $rsm->addFieldResult('d', 'doc_descr', 'description');
        $rsm->addFieldResult('d', 'file_name', 'fileName');
        $rsm->addJoinedEntityResult('App\Entity\Category', 'c', 'd', 'category');
        $rsm->addFieldResult('c', 'category_id', 'id');
        $rsm->addFieldResult('c', 'category_name', 'name');
        $rsm->addFieldResult('c', 'category_descr', 'description');
        $query;
        if($form['Text']!=""){
            $query = $this->_em->createNativeQuery("SELECT d.id, d.name as doc_name, d.description as doc_descr, d.file_name, c.id as category_id, c.name as category_name,
            c.description as category_descr
             FROM Document d 
             LEFT JOIN Category c ON d.category_id = c.id 
             WHERE to_tsvector(d.name) || to_tsvector(d.description)
            @@ plainto_tsquery('$q')",$rsm);
            
        }
        else{
            $query = $this->_em->createNativeQuery("SELECT d.id, d.name as doc_name, d.description as doc_descr, d.file_name, c.id as category_id, c.name as category_name,
            c.description as category_descr
             FROM Document d 
             LEFT JOIN Category c ON d.category_id = c.id 
             ",$rsm);
        }
        $documents = $query->getResult(); 
        $documentsNew=[];
        foreach ($documents as $doc){
            if(
                ($form['Tags']!=[] && $form['Category'] && in_array($form['Tags'][0],$doc->getTagsId()) && $doc->getCategory()->getId()==$form['Category'])||
                ($form['Tags']!=[] && !$form['Category'] && in_array($form['Tags'][0],$doc->getTagsId()))||
                ($form['Tags']==[] && $form['Category'] && $doc->getCategory()->getId()==$form['Category'])||
                ($form['Tags']==[] && !$form['Category'])
                ){
                    $documentsNew[] = $doc;
                } 
            }
            
          
        
        return $documentsNew;
    }
}
