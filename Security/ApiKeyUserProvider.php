<?php

namespace Hr\ApiBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Hr\ApiBundle\Entity\User as UserEntity;
use Hr\ApiBundle\Entity\UserOrganizer;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use \Exception;

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
     * ApiKeyUserProvider constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Load user by username and app scope
     * @param string $username
     * @return bool|User|UserInterface
     */
    public function loadUserByUsernameAndAppScope(string $username, string $appScope)
    {
        switch ($appScope) {
            case 'organizer':
                $repository = $this->entityManager->getRepository(UserOrganizer::class);
                /** @var UserOrganizer $userOrganizer */
                $userOrganizer = $repository->findByUsername($username);
                break;
            default:
                throw new Exception("appScope '$appScope' not handled");
        }

        $user = $userOrganizer->getUser();
        /** @var UserEntity $user */
        $user->setAppScope($userOrganizer);

        if (is_null($user)) {
            return false;
        }

        return $user;
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

        return $user;
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
