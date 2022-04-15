<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserInfosFormType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user
                ->setRoles([ 'ROLE_USER' ])
                ->setActive(true)
                ->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //return $this->redirectToRoute('userInfos');
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/userInfosUpdate', name: 'userInfosUpdate', methods: ['GET', 'POST'])]
    public function userInfosUpdate(UserRepository $userRepository, Request $request){
        //$user = $userRepository->findOneBy(['id' => $id]);
        $user = $this->getUser();
        $userForm = $this->createForm(UserInfosFormType::class, $user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            $userRepository->add($user);
            return $this->redirectToRoute('userInfos');
        }
        return $this->render('user/userInfosUpdate.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    #[Route('/userInfos', name: 'userInfos', methods: ['GET', 'POST'])]
    public function userInfos(UserRepository $userRepository){

        return $this->render('user/userInfos.html.twig', [
            //permet de récupérer toutes les infos du user connecté
            'user' => $this->getUser()
        ]);
    }

    #[Route('/admin/userView', name: 'userView', methods: ['GET', 'POST'])]
    public function userView(UserRepository $userRepository){
        $users = $userRepository->findBy([], ['name' => 'ASC']);
        return $this->render('article/test.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/removeUser/{id}', name: 'removeUser', methods: ['GET', 'POST'])]
    public function removeUser(UserRepository $userRepository, $id, ArticleRepository $articleRepository){
        $user = $userRepository->findOneBy(['id' => $id]);
        $userRepository->remove($user);
        return $this->redirectToRoute('userView');
    }
}
