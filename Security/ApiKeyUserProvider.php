<?php

namespace Hr\ApiBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Hr\ApiBundle\Entity\User as UserEntity;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ApiKeyUserProvider
 * @package Hr\ApiBundle\Security
 */
class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var SerializerInterface The cache manager service
     */
    protected $serializer;

    /**
     * ApiKeyUserProvider constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * This must be overrode in App/Security/ApiKeyUserProviderXXXXXXXXXX
     * @param string $encodedUser
     * @return bool|User|UserInterface
     */
    public function createUserFromJson(string $encodedUser)
    {
        /** @var UserEntity $user */
        $user = $this->serializer->deserialize($encodedUser, UserEntity::class, 'json');
    
        if (is_null($user)) {
            return false;
        } else {
            return $user;
        }
    }

    /**
     * Load user by username
     * @param string $encodedUser
     * @return bool|User|UserInterface
     */
    public function loadUserByUsername($encodedUser)
    {
        return false;
    }

    /**
     * Refresh user
     * @param UserInterface $user
     * @return UserInterface|void
     */
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    /**
     * Check if the user class is supported
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
