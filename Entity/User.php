<?php

namespace Hr\ApiBundle\Entity;

use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Hr\ApiBundle\Repository\UserRepository")
 * @ORM\Table(name="authUser")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("user,admin")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Groups("user,admin")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Groups("user,admin")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Groups("admin")
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     * @Groups("admin")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups("admin")
     */
    private $lastApiKey;

    /**
     * @var string
     * @Groups("admin")
     */
    private $appScope;

    /**
     * @return mixed
     */
    public function getLastApiKey()
    {
        return $this->lastApiKey;
    }

    /**
     * @param mixed $lastApiKey
     */
    public function setLastApiKey($lastApiKey): void
    {
        $this->lastApiKey = $lastApiKey;
    }

    /**
     * @return string
     */
    public function getAppScope()
    {
        return $this->appScope;
    }

    /**
     * @param string $appScope
     */
    public function setAppScope($appScope): void
    {
        $this->appScope = $appScope;
    }

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }

    public function setRoles(array $roles)
    {
        $this->roles = array_unique($roles);
    }

    public function addRoles(array $roles)
    {
        $this->roles = array_merge($this->roles, $roles);
        $this->roles = array_unique($this->roles);
    }
}