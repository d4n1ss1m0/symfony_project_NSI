<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Form\AddUserFormType;
use App\Form\EditUserFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractController
{
    #[Route('/admin_panel/user', name: 'user_crud', methods:'POST')]
    public function index(): Response
    {
        $users=$this->getDoctrine()->getRepository(Users::class)->findAll();
        $forRender['users'] = $users;
        return $this->render('user_crud/index.html.twig',$forRender);
    }
    #[Route('/admin_panel/add_user', name: 'add_user_crud')]
    public function addUser(Request $request, UserPasswordHasherInterface $passwordHasher):Response
    {
        $user = new Users();
        $form = $this -> createForm(AddUserFormType::class, $user);
        $em  = $this -> getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {             
            $password = $passwordHasher -> hashPassword($user,$user -> getPassword());
            $user -> setPassword($password);
            $em->persist($user);
            $em->flush();
            //return $this -> redirect('/');
       }
       $forRender['form'] = $form->createView();
       return $this->render('user_crud/addUser.html.twig',$forRender);
    }
    #[Route('/admin_panel/delete_user/{id}', name: 'delete_user_crud')]
    public function deleteUser(int $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em -> getRepository(Users::class)->findOneBy(array('id' => $id));
        $em->remove($user);
        $em->flush();
        return $this -> redirect('/');
    }
    #[Route('/admin_panel/edit_user/{id}', name: 'edit_user_crud')]
    public function editUser(Request $request, UserPasswordHasherInterface $passwordHasher, $id):Response
    {
        $user = new Users();
        $cUser = new Users();

        $em  = $this -> getDoctrine()->getManager();
        $cUser = $em -> getRepository(Users::class)->findOneBy(array('id' => $id));

        $roles = $cUser->getRoles();
        $roles = \array_diff($roles, ["ROLE_USER"]);
        sort($roles);

        $form = $this -> createForm(EditUserFormType::class, $user,array('roles' => $roles));

        //$passwordHasher->needsRehash($cUser,$cUser->getPassword());
        $forRender['user'] = $cUser;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {     
            if($cUser){
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
                    }*/
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
                    //return $this->redirect('/admin/tables');
            }
            else{
                    throw new Exception('Пользователь не найден');
            }
            

            
        }
        $forRender['form'] = $form->createView();
        return $this->render('user_crud/editUser.html.twig',$forRender);
    }
    
}
