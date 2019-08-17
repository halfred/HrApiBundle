<?php

namespace Hr\ApiBundle\Service;


use Doctrine\ORM\EntityManagerInterface;

/**
 * Class FormatHelper
 * @package Hr\ApiBundle\Service
 */
class FormatHelper
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function convertDateTimesFromStrings(array $arrayToConvert, array $convertKeys): array
    {
        foreach ($convertKeys as $convertKey) {
            if (!empty($arrayToConvert[$convertKey]) && is_string($arrayToConvert[$convertKey])) {
                $arrayToConvert[$convertKey] = date_create_from_format($this::DATE_TIME_FORMAT, $arrayToConvert[$convertKey]);

                if (!$arrayToConvert[$convertKey] instanceof \DateTime) {
                    $arrayToConvert[$convertKey] = null;
                }
            } else {
                $arrayToConvert[$convertKey] = null;
            }
        }

        return $arrayToConvert;
    }

    public function convertRelationsFromIds(array $arrayToConvert, array $convertKeys): array
    {
        foreach ($convertKeys as $convertKey => $entityToFetch) {
            //TODO: handle $arrayToConvert[$convertKey] is an array
            if (!empty($arrayToConvert[$convertKey]) && (is_int($arrayToConvert[$convertKey])
                    || intval($arrayToConvert[$convertKey] == $arrayToConvert[$convertKey]))) {
                $arrayToConvert[$convertKey] = intval($arrayToConvert[$convertKey]);

                $repository = $this->entityManager->getRepository($entityToFetch);
                $arrayToConvert[$convertKey] = $repository->findOneBy(['id' => $arrayToConvert[$convertKey]]);

                if (!$arrayToConvert[$convertKey] instanceof $entityToFetch) {
                    $arrayToConvert[$convertKey] = null;
                }
            } else {
                $arrayToConvert[$convertKey] = null;
            }
        }
        return $arrayToConvert;
    }
}