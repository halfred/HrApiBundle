<?php

namespace Hr\ApiBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Hr\ApiBundle\Entity\User as UserEntity;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class ApiKeyUserProvider
 * @package App\Security
 */
class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ApiKeyUserProvider constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Load user by username
     * @param string $username
     * @return bool|User|UserInterface
     */
    public function loadUserByUsername($username)
    {
        $repository = $this->entityManager->getRepository(UserEntity::class);
        /** @var UserEntity $user */
        $user = $repository->findOneBy(['username' => $username]);

        if (is_null($user)) {
            return false;
        }

        return new User(
            $username,
            null,
            // the roles for the user - you may choose to determine
            // these dynamically somehow based on the user
            ['ROLE_API']
        );
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
