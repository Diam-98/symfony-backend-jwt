<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\PostService;
use App\Validation\ValidationError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PostController extends AbstractController
{

    private PostService $postService;

    /**
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }


    #[Route('/api/posts', name: 'app_post', methods: 'GET')]
    public function index(): Response
    {
        return new Response($this->postService->getAllPosts(), 200);
    }

    #[Route('/api/post/add', name: 'app_post_add', methods: 'POST')]
    public function addPost(Request $request, ValidationError $validationError): Response
    {
        $data = json_decode($request->getContent(), true);

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()){

            $user = $this->getUser();

            $this->postService->createPost($post, $user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Poste cree avec succes'
            ], 201);
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

    #[Route('/api/post/{id}', name: 'app_post_view', methods: 'GET')]
    public function viewPost(Post $post): Response
    {
        return new Response($this->postService->getSinglePost($post), 200);
    }
}
