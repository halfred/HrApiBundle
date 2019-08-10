<?php

namespace Hr\ApiBundle\Controller;

use Hr\ApiBundle\Entity\User;
use Hr\ApiBundle\Repository\UserRepository;
use Hr\ApiBundle\Service\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class AdminBaseController
 * @package App\Controller
 */
abstract class AdminBaseController extends BaseController
{
    /**
     * Get multiple users
     * @param SerializerBuilder $serializerBuilder the serializer
     * @return Response
     * @Route("/", name="user_get_multi", methods={"GET"})
     */
    public function getMulti(SerializerBuilder $serializerBuilder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        $response = $serializerBuilder->getSerializer()->serialize($users, 'json');
        return new Response($response);
    }

    /**
     * Get one user by id
     * @param Request $request The http request
     * @param int $userId The user id
     * @return JsonResponse
     * @Route("/{userId}", name="user_get_one", methods={"GET"})
     */
    public function getOne(SerializerBuilder $serializerBuilder, int $userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        $response = $serializerBuilder->getSerializer()->serialize($user, 'json');
        return new Response($response);
    }

//    /**
//     * create an user
//     * @param Request $request The http request
//     * @return JsonResponse
//     * @Route("/", name="user_create", methods={"POST"})
//     */
//    public function create(Request $request)
//    {
//        return $this->json([
//            'message' => 'create User',
//        ]);
//    }
//
//    /**
//     * update an user
//     * @param Request $request The http request
//     * @param int     $userId  The user id
//     * @return JsonResponse
//     * @Route("/{userId}", name="user_update", methods={"put"})
//     */
//    public function update(Request $request, $userId)
//    {
//        return $this->json([
//            'message' => 'update User ' . $userId,
//        ]);
//    }

    /**
     * delete by Id
     * @param Request $request The http request
     * @param int $id The  id
     * @return JsonResponse
     * @Route("/{id}", name="user_delete", methods={"delete"})
     */
    public function delete(Request $request, int $id)
    {
        return $this->json([
            'message' => 'delete ' . $id,
        ]);
    }
}
