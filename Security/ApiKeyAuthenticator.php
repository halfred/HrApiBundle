<?php

namespace Hr\ApiBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;


/**
 * Class ApiKeyAuthenticator
 * @package Hr\ApiBundle\Security
 */
class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @var EntityManagerInterface The doctrine entity manager service
     */
    protected $entityManager;
    /**
     * @var UserPasswordEncoderInterface The password encoder service
     */
    protected $userPasswordEncoder;
    /**
     * @var CacheItemPoolInterface The cache manager service
     */
    protected $cacheManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param CacheItemPoolInterface $cacheManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        CacheItemPoolInterface $cacheManager
    )
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Create a token api key based on the requested apiKey
     * @param Request $request
     * @param         $providerKey
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey): PreAuthenticatedToken
    {
        $apiKey = $request->headers->get('apiKey');
        if (empty($apiKey)) {
            throw new InvalidArgumentException('apiKey field is missing in the header');
        }

        $appScope = $request->headers->get('appScope');
        if (empty($appScope)) {
            throw new InvalidArgumentException('appScope field is missing in the header');
        }

        $preAuthenticatedToken = new PreAuthenticatedToken(
            '',
            $apiKey,
            $providerKey
        );
        $preAuthenticatedToken->setAttribute('appScope', $appScope);

        return $preAuthenticatedToken;
    }

    /**
     * Check if the token type is supported
     * used internally
     * @param TokenInterface $token
     * @param                $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey): bool
    {
        return (($token instanceof PreAuthenticatedToken) && ($token->getProviderKey() === $providerKey));
    }

    /**
     * Check if the user exists and the password is correct, based on the Token
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param mixed $providerKey
     * @return PreAuthenticatedToken
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function authenticateToken(
        TokenInterface $token,
        UserProviderInterface $userProvider,
        $providerKey
    ): PreAuthenticatedToken
    {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            throw new InvalidArgumentException(
                'The user provider must be an instance of ApiKeyUserProvider (' . get_class($userProvider) . ' was given).'
            );
        }

        $username = null;
        $cacheKeyUsername = null;
        $cachedApiKey = null;
        $apiKey = $token->getCredentials();
        //check the user in the cache, based on the apiKey
        $cacheKeyApiKey = 'auth:apiKey:' . $apiKey . ':user';
        if ($this->cacheManager->hasItem($cacheKeyApiKey)) {
            $cachedUser = $this->cacheManager->getItem($cacheKeyApiKey);
            $encodedUser = $cachedUser->get();
            $appScope = $token->getAttribute('appScope');

            $user = $userProvider->createUserFromJson($encodedUser, $appScope);

            //refresh cache TTL
            $cachedUser->expiresAfter(getenv('API_USER_SESSION_TTL'));
            $this->cacheManager->save($cachedUser);
        } else {
            throw new InvalidArgumentException('invalid apiKey ' . $apiKey);
        }

        if (empty($user)) {
            throw new Exception('invalid user');
        }

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * Handle auth failure
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(AuthenticationException $exception): Response
    {
        return new Response(
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            401
        );
    }
}