<?php

namespace Hr\ApiBundle\Security;

use Hr\ApiBundle\Entity\User;
use Hr\ApiBundle\Interfaces\CacheManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * Class ApiKeyGenerator
 * @package Hr\ApiBundle\Security
 */
class ApiKeyGenerator
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
     * @var CacheManagerInterface The cache manager service
     */
    protected $cacheManager;
    /**
     * @var SerializerInterface The cache manager service
     */
    protected $serializer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param CacheItemPoolInterface $cacheManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        CacheItemPoolInterface $cacheManager,
        SerializerInterface $serializer
    ) {
        $this->entityManager       = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->cacheManager        = $cacheManager;
        $this->serializer        = $serializer;
    }

    /**
     * Generate an apiKey based on the passed username and password
     * @param Request $request
     * @return mixed|null|string
     */
    public function generate(Request $request)
    {
        //check required fields for auth
        $username = $request->request->get('username');
        if (empty($username)) {
            throw new BadCredentialsException('username field is missing in the posted request');
        }

        $password = $request->request->get('password');
        if (empty($password)) {
            throw new BadCredentialsException('password field is missing in the posted request');
        }

        //get user based on username from the DB
        $repository = $this->entityManager->getRepository(User::class);
        /** @var User $user */
        $user = $repository->findOneBy([
            'username' => $username,
        ]);
        if (!$user) {
            throw new BadCredentialsException('User or password incorrect');
        }

        //check password
        $isPasswordValid = $this->userPasswordEncoder->isPasswordValid($user, $password);
        if (!$isPasswordValid) {
            throw new BadCredentialsException('User or password incorrect');
        }

        //check if the user already has an api key & username registered in the cache. create and store it otherwise
        $cachedUser     = null;
        $cachedApiKey   = null;
        $apiKey         = null;

        //create the apiKey if the cache is empty for this user/apiKey
        $apiKey       = hash('sha1', $username . $password . date('YmdHis') . rand(1, 100));
        $cacheKeyApiKey = 'auth:apiKey:' . $apiKey . ':user';
        $cachedUser = $this->cacheManager->createItem($cacheKeyApiKey);

        $user->setLastApiKey($apiKey);
        $serializedUser= $this->serializer->serialize($user, 'json');

        $cachedUser->set($serializedUser);

        //in all cases, set/reset ttl countdown
        $cachedUser->expiresAfter(getenv('API_USER_SESSION_TTL'));
        $this->cacheManager->save($cachedUser);

        return $apiKey;
    }
}