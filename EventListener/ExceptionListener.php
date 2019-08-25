<?php

namespace Hr\ApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        if (getenv('APP_ENV') !== 'dev') {
            $exception = $event->getException();

            $jsonException = json_encode([
                'errorMessage' => $exception->getMessage(),
//                'errorCode' => $exception->getCode(),
            ]);

            $response = new Response($jsonException,500,['Content-Type' => 'application/json']);

//            if ($exception instanceof BadCredentialsException) {
//                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
//            }
            $event->setResponse($response);
        }
    }
}