<?php

namespace Hr\ApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Hr\ApiBundle\Entity\User;
use Hr\ApiBundle\Entity\UserOrganizer;
use Symfony\Bridge\Doctrine\RegistryInterface;


class UserOrganizerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserOrganizer::class);
    }

    public function findByUsername(string $username) {
        return $this->createQueryBuilder('uo')
            ->join('uo.user', 'u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
