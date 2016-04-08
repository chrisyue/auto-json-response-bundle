<?php

/*
 * This file is part of the ChrisyueAutoJsonResponseBundle package.
 *
 * (c) Chrisyue <http://chrisyue.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisyue\Bundle\AutoJsonResponseBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\Serializer;

class AutoJsonResponseListener
{
    private $serializer;
    private $options;

    public function __construct(Serializer $serializer = null, array $options = [])
    {
        $this->serializer = $serializer;
        $this->options = $options;
    }

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

        if ($result instanceof Response) {
            return;
        }

        $response = new JsonResponse();

        if ($request->isMethod('POST')) {
            $response->setStatusCode(201);
        }

        if (!is_scalar($result)) {
            $result = $this->getSerializer()->normalize($result, null, [
                'groups' => $this->options['serialization_default_groups'],
            ]);
        }

        $response->setData($result);

        $event->setResponse($response);
    }

    private function getSerializer()
    {
        if (null === $this->serializer) {
            throw new \BadMethodCallException('You should enable `serializer` in `config.yml` to get this work');
        }

        return $this->serializer;
    }
}
