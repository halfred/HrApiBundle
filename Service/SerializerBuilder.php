<?php

namespace Hr\ApiBundle\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerBuilder
{
    /** @var Serializer */
    protected $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            $this->getNormalizers(),
            $this->getEncoders()
        );
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    protected function getNormalizers()
    {
        return [
            new ObjectNormalizer(),
        ];
    }

    protected function getEncoders()
    {
        return [
            new JsonEncoder(),
        ];
    }
}