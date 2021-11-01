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
            if($em -> getRepository(Users::class)->findOneBy(array('email' => $user->getEmail())))
                {
                    return new Response("Ошибка: Пользователь с таким email уже существует!",409);
                }            
            $password = $passwordHasher -> hashPassword($user,$user -> getPassword());
            $user -> setPassword($password);
            $em->persist($user);
            $em->flush();
            return new Response("Успех: Пользователь добавлен",200);
       }
       $forRender['form'] = $form->createView();
       return $this->render('user_crud/addUser.html.twig',$forRender);
    }
    #[Route('/admin_panel/delete_user/{id}', name: 'delete_user_crud')]
    public function deleteUser(int $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em -> getRepository(Users::class)->findOneBy(array('id' => $id));
        if(!$user){
            return new Response("Ошибка: Удаляемый пользователь не найден!", 404);
        }
        if($request->getContent() == $this->getUser()->getId()){
            return new Response("Ошибка: Попытка удалить самого себя", 409);
        }
        
        if($user->getEmail() == 'sadmin@sadmin'){
            return new Response("Ошибка: sadmin@admin не может быть удален!", 409);
        }
        $em->remove($user);
        $em->flush();
        return new Response("Успех: Пользователь удален",200);
    }
    #[Route('/admin_panel/edit_user/{id}', name: 'edit_user_crud')]
    public function editUser(Request $request, UserPasswordHasherInterface $passwordHasher, $id):Response
    {
        $user = new Users();

        $em  = $this -> getDoctrine()->getManager();
        $user = $em -> getRepository(Users::class)->findOneBy(array('id' => $id));
        $form = $this -> createForm(EditUserFormType::class, $user);
        $email = $user->getEmail();
        $passwordOld = $user->getPassword();
        $forRender['user'] = $user;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {     
            if($user){
                if($em -> getRepository(Users::class)->findOneBy(array('email' => $user->getEmail())) && $email != $user->getEmail())
                {
                    return new Response("Ошибка: Пользователь с таким email уже существует!",409);
                }
                if($user -> getPassword() != $passwordOld){
                    $password = $passwordHasher -> hashPassword($user,$user -> getPassword());
                    $user -> setPassword($password);
                }
                $user -> setEmail(htmlspecialchars($user -> getEmail()));
                $em->persist($user);
                $em->flush();
                return new Response("Успех: пользователь изменен",200);
            }
            else{
                return new Response('Ошибка: Пользователь не найден',404);
            }
            

            
        }
        $forRender['form'] = $form->createView();
        return $this->render('user_crud/editUser.html.twig',$forRender);
    }
    
}
