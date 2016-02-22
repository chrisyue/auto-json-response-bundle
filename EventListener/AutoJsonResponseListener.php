<?php

namespace Chrisyue\Bundle\AutoJsonResponseBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class AutoJsonResponseListener
{
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ('json' !== $request->getRequestFormat()) {
            return;
        }

        $result = $event->getControllerResult();

        if (null === $result) {
            $event->setResponse(new JsonResponse(null, 204));

            return;
        }

        $response = new JsonResponse();

        if ($request->isMethod('POST')) {
            $response->setStatusCode(201);
        }

        if (is_object($result)) {
            $normalizer = new GetSetMethodNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
            $serializer = new Serializer([$normalizer]);
            $result = $serializer->normalize($result);
        }

        $response->setData($result);

        $event->setResponse($response);
    }
}

