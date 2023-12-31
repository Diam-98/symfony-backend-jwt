<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Validation\ValidationError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods: 'POST')]
    public function index(Request $request, SerializerInterface $serializer, UserRepository $userRepository, UserService $userService, ValidationError $validationError): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->submit($data);

        if ($form->isSubmitted()){

            if ($userService->isUserExist($user->getEmail())){
                return new JsonResponse([
                    'status'=>false,
                    'message'=>'Cet email existe deja!'
                ], 401);
            }

            $userService->addUser($user);

            $response = [
                'success' => true,
                'message' => 'Utilisateur cree avec succes'
            ];
            $resp_json = $serializer->serialize($response, 'json');

            return new Response($resp_json, 201);
        }

        $errors = $validationError->getErrorsFromForms($form);

        $error_data = [
            'success' => 'false',
            'type' => 'validation_error',
            'message' => 'il y\'a une erreur de validation',
            'errors' => $errors
        ];

        return new JsonResponse($error_data, 400);
    }

}
