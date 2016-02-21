<?php

namespace Chrisyue\Bundle\AutoJsonResponseBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

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
            $event->setResponse(new Response(null, 204));

            return;
        }

        if (is_array($result)) {
            $response = new JsonResponse($result);

            if ($request->isMethod('POST')) {
                $response->setStatusCode(201);
            }

            $event->setResponse($response);
        }
    }
}

