<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods: 'POST')]
    public function index(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->submit($data);

        if ($form->isSubmitted()){

            $exist_user = $userRepository->findOneBy(['email' => $user->getEmail()]);

            if ($exist_user){
                return new JsonResponse([
                    'status'=>false,
                    'message'=>'Cet email existe deja!'
                ], 401);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            $response = [
                'success' => true,
                'message' => 'Utilisateur cree avec succes'
            ];
            $resp_json = $serializer->serialize($response, 'json');

            return new Response($resp_json, 201);
        }

        $errors = $this->getErrorsFromForms($form);

        $error_data = [
            'success' => 'false',
            'type' => 'validation_error',
            'message' => 'il y\'a une erreur de validation',
            'errors' => $errors
        ];

        return new JsonResponse($error_data, 400);
    }

    private function getErrorsFromForms(FormInterface $form): array
    {
        $errors = array();

        foreach ($form->getErrors() as $error){
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm){
            if ($childForm instanceof FormInterface){
                $errors[$childForm->getName()] = $childForm;
            }
        }

        return $errors;
    }
}
