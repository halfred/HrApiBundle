<?php

namespace Hr\ApiBundle\Controller;

use Hr\ApiBundle\Service\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController extends Controller
{
//    /**
//     * Get multiple
//     * @param SerializerBuilder $serializerBuilder the serializer
//     * @return Response
//     * @Route("/", name="user_get_multi", methods={"GET"})
//     */
//    public function getMulti(SerializerBuilder $serializerBuilder)
//    {
//        return $this->json([
//            'message' => "Get all",
//        ]);
//    }
//
//    /**
//     * Get one user by id
//     * @param Request $request The http request
//     * @param int $userId The user id
//     * @return JsonResponse
//     * @Route("/{userId}", name="user_get_one", methods={"GET"}, requirements={"userId"="\d+"})
//     */
//    public function getOne(SerializerBuilder $serializerBuilder, int $userId)
//    {
//        return $this->json([
//            'message' => "Get One $userId",
//        ]);
//    }
//
//    /**
//     * delete by Id
//     * @param Request $request The http request
//     * @param int $id The  id
//     * @return JsonResponse
//     * @Route("/{id}", name="user_delete", methods={"delete"})
//     */
//    public function delete(Request $request, int $id)
//    {
//        return $this->json([
//            'message' => 'delete ' . $id,
//        ]);
//    }

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
//     * @param int $userId The user id
//     * @return JsonResponse
//     * @Route("/{userId}", name="user_update", methods={"put"}, requirements={"userId"="\d+"})
//     */
//    public function update(Request $request, int $userId)
//    {
//        return $this->json([
//            'message' => 'update User ' . $userId,
//        ]);
//    }


}
