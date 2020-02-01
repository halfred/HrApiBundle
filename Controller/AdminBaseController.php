<?php

namespace Hr\ApiBundle\Controller;

use Hr\ApiBundle\Entity\User;
use Hr\ApiBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class AdminBaseController
 * @package App\Controller
 */
abstract class AdminBaseController extends BaseController
{
    /**
     * Get multiple users
     * @param SerializerInterface $serializer the serializer
     * @return Response
     * @Route("/", name="user_get_multi", methods={"GET"})
     */
    public function getMulti(SerializerInterface $serializer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $users          = $userRepository->findAll();
        
        $response = $serializer->serialize($users, 'json');
        return new Response($response, 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * Get one user by id
     * @param Request $request The http request
     * @param int $userId The user id
     * @return Response
     * @Route("/{userId}", name="user_get_one", methods={"GET"})
     */
    public function getOne(SerializerInterface $serializer, int $userId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $user           = $userRepository->find($userId);
        
        $response = $serializer->serialize($user, 'json');
        return new Response($response, 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * delete by Id
     * @param Request $request The http request
     * @param int $id The  id
     * @return Response
     * @Route("/{id}", name="user_delete", methods={"delete"})
     */
    public function delete(Request $request, SerializerInterface $serializer, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $user           = $userRepository->find($id);
        
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        
        $response = $serializer->serialize([
            "User $id deleted",
        ], 'json');
        return new Response($response, 200, ['Content-Type' => 'application/json']);
    }
    
}
