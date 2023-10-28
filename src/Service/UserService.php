<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    public function isUserExist(string $email): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user){
            return false;
        }

        return true;
    }

    public function addUser(User $user): User
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }


}