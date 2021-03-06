<?php

namespace Hr\ApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Hr\ApiBundle\Entity\User;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    const APP_SPECIFIC_USER_TYPES = [
        'organizer' => [
            'name'  => 'Organizer',
            'alias' => 'UORG',
        ],
    ];
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    public function checkAppSpecificUsersForOneUser(int $userId)
    {
        $entityManager = $this->getEntityManager()->getConnection();
        
        $appSpecificUserJoins = [];
        $selectFields         = [];
        foreach (self::APP_SPECIFIC_USER_TYPES as $appSpecificUserType) {
            $appSpecificUserJoins[] = "LEFT JOIN authUser" . $appSpecificUserType['name'] . " " . $appSpecificUserType['alias']
                                      . ' ON ' . $appSpecificUserType['alias'] . '.user_id = aU.id';
            $selectFields[]         = $appSpecificUserType['alias'] . '.id as User' . $appSpecificUserType['name'];
        }
        
        //get all app specific users id or null if missing
        $request = "
            SELECT " . implode(', ', $selectFields) . "
            FROM authUser aU
            " . implode(' ', $appSpecificUserJoins) . "
            WHERE aU.id= :userId
            ";
        
        try {
            $statement = $entityManager->prepare($request);
            $statement->execute(['userId' => $userId]);
            $appSpecificUsers = $statement->fetch();
            
            //check ids ; adds if null
            $addedUsers = 0;
            foreach ($appSpecificUsers as $appSpecificUser => $appSpecificUserId) {
                if (empty($appSpecificUserId)) {
                    $request   = "
                    INSERT INTO auth$appSpecificUser VALUES (:userId,:userId);
                ";
                    $statement = $entityManager->prepare($request);
                    $statement->execute(['userId' => $userId]);
                    $addedUsers++;
                }
            }
            
            return "checked : $addedUsers added";
        } catch (TableNotFoundException $e) {
            return 'TableNotFoundException : ' . $e->getMessage();
        }
        
        
    }
}
