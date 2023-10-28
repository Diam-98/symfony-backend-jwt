<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PostService
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

    public function getAllPosts(): string
    {
        $posts = $this->postRepository->findAll();
        return $this->serializer->serialize($posts, 'json');
    }

    public function getSinglePost(Post $post): string
    {
        return $this->serializer->serialize($post, 'json');
    }

    public function createPost(Post $post, $user): Post
    {

        $post->setAuthor($user);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }
}