<?php

namespace Hr\ApiBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Hr\ApiBundle\Service\JsonHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController extends Controller
{
    /** @var Security */
    protected $security;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var Serializer $serializer */
    protected $serializer;
    /** @var JsonHelper */
    protected $jsonHelper;

    /** @var string */
    protected $defaultEntitySerializationGroup;


    public function __construct(Security $security,
                                EntityManagerInterface $entityManager,
                                SerializerInterface $serializer,
                                JsonHelper $jsonHelper)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->jsonHelper = $jsonHelper;
        $this->setDefaultEntitySerializationGroup();
    }

    /**
     * @param $objectToReturn
     * @return Response
     */
    protected function returnSuccessObject($objectToReturn): Response
    {
        $serializedObjectToReturn = $this->serializer->serialize($objectToReturn, 'json', ['groups' => [$this->defaultEntitySerializationGroup]]);
        return new Response($serializedObjectToReturn, 200, ['Content-Type' => 'application/json']);
    }

    /**
     *
     */
    protected function setDefaultEntitySerializationGroup(): void
    {
        $defaultEntitySerializationGroup = explode('\\', get_class($this));
        $defaultEntitySerializationGroup = lcfirst(array_pop($defaultEntitySerializationGroup));
        $this->defaultEntitySerializationGroup = str_replace('Controller', '', $defaultEntitySerializationGroup);
    }

}
