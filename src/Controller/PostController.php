<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
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

    private EntityManagerInterface $entityManager;
    private PostRepository $postRepository;
    private SerializerInterface $serializer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PostRepository $postRepository
     */
    public function __construct(EntityManagerInterface $entityManager, PostRepository $postRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
        $this->serializer = $serializer;
    }


    #[Route('/api/posts', name: 'app_post', methods: 'GET')]
    public function index(): Response
    {
        $posts = $this->postRepository->findAll();
        $posts_jsn = $this->serializer->serialize($posts, 'json');
        return new Response($posts_jsn, 200);
    }

    #[Route('/api/post/add', name: 'app_post_add', methods: 'POST')]
    public function addPost(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()){

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Poste cree avec succes'
            ], 201);
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

    #[Route('/api/post/{id}', name: 'app_post_view', methods: 'GET')]
    public function viewPost(Post $post): Response
    {
        $post_jsn = $this->serializer->serialize($post, 'json');
        return new Response($post_jsn, 200);
    }
}
