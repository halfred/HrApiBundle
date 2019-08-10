<?php

namespace Hr\ApiBundle\Service;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class JsonHelper
 * @package Hr\ApiBundle\Service
 */
class JsonHelper
{

    public function __construct()
    {

    }

    public function getValidJsonBody(Request $request, $mandatoryJsonKeys = []): array
    {
        $jsonBody = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        if (!empty($mandatoryJsonKeys)) {
            foreach ($mandatoryJsonKeys as $jsonKey) {
                if (!array_key_exists($jsonKey, $jsonBody)) {
                    throw new BadRequestHttpException("'$jsonKey' mandatory key is missing");
                }
            }
        }

        return $jsonBody;
    }
}